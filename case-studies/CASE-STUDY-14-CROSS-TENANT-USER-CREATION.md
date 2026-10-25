# Case Study 14 — Cross-Tenant User Creation via Request-Body `tenantId` (`addNewUser`)

| Field | Value |
|-------|-------|
| **Severity** | **HIGH (7.2) as analysed** — my own scoring; no triager set it |
| **CWE** | CWE-639 (Authorization Bypass Through User-Controlled Key) · CWE-284 (Improper Access Control) |
| **OWASP** | A01:2021 — Broken Access Control |
| **Surface** | Open-source Kafka/cluster admin UI (`Aiven-Open/klaw`) — `core` module, `UsersTeamsController` |
| **Status** | **Submitted and reviewed by triage; denied as a “trap.”** The denial was not based on a live test disproving the source-level finding. No live request was made. The same code was present on upstream `main` as of this write-up |

---

## Problem

`POST /addNewUser` accepts a `tenantId` field in the request body, and the service trusts it.

The endpoint *is* permission-gated — it carries `@PermissionAllowed` for team/user management, and the service additionally requires the caller to hold `ADD_EDIT_DELETE_USERS` in **their own** tenant. So this is **not** an unauthenticated flaw and **not** a privilege escalation. It is a **horizontal, cross-tenant authorization bypass**: a user-creation administrator in Tenant A can choose the tenant that the new user record is written into. The bug is one branch — when the body carries a non-zero `tenantId`, that value is used verbatim and is **never compared against the caller's own tenant**. Every other tenant-scoped write in the same class derives the tenant from the session identity.

**Impact, stated at the floor it actually reaches: the ability to create a user record in another tenant.** Creating a user in another tenant does **not** by itself grant access to that tenant's data, and impact beyond the cross-tenant write was **not** demonstrated. **This is a source-code finding with no live proof-of-concept:** no two-tenant rig was stood up, no request was replayed against a running instance, and no account was created. Every claim below is traced to a file and line in the upstream source.

## Attack Chain

```
┌────────────────────────────────────────────────────────────┐
│ Caller in Tenant A holds ADD_EDIT_DELETE_USERS             │
│ POST /addNewUser { "username":"…", "role":"…",             │
│                   "teamId":N, "tenantId":<Tenant B> }      │
└──────────────┬─────────────────────────────────────────────┘
               ▼
┌────────────────────────────────────────────────────────────┐
│ UsersTeamsController.addNewUser            (ctrl:119-131)  │
│ @PermissionAllowed(ADD_EDIT_DELETE_TEAMS |                 │
│                     FULL_ACCESS_USERS_TEAMS_ROLES)         │
│ ⇒ permission gate PASSES (caller-scoped)                   │
└──────────────┬─────────────────────────────────────────────┘
               ▼
┌────────────────────────────────────────────────────────────┐
│ UsersTeamsControllerService.addNewUser     (svc:558-568)   │
│   getTenantId()==0 → tenantId = getTenantId(getUserName()) │
│                  else → tenantId = newUser.getTenantId()   │
│                          ↑ BODY VALUE, never compared      │
│                    against the caller's tenant             │
└──────────────┬─────────────────────────────────────────────┘
               ▼
┌────────────────────────────────────────────────────────────┐
│ insertIntoUsers(userInfo)         (InsertDataJdbc:287)     │
│ only check: username not already taken (global)            │
│ ⇒ row written with tenantId = Tenant B                     │
│ if authType=="db": inMemoryUserDetailsManager.createUser   │
│   ⇒ account registered with submitted password / role      │
└──────────────┬─────────────────────────────────────────────┘
```

## Evidence

Every line quoted below is verbatim from the upstream source.

### Step 1 — The endpoint and its permission gate

`core/src/main/java/io/aiven/klaw/controller/UsersTeamsController.java:119-131`

```java
  @PermissionAllowed(
      permissionAllowed = {
        PermissionType.ADD_EDIT_DELETE_TEAMS,
        PermissionType.FULL_ACCESS_USERS_TEAMS_ROLES
      })
  @PostMapping(
      value = "/addNewUser",
      produces = {MediaType.APPLICATION_JSON_VALUE})
  public ResponseEntity<ApiResponse> addNewUser(@Valid @RequestBody UserInfoModel newUser)
      throws KlawException {
    return new ResponseEntity<>(
        usersTeamsControllerService.addNewUser(newUser, true), HttpStatus.OK);
  }
```

The `true` argument is `isExternal` — the direct UI / admin path, which is exactly the branch that honours a body-supplied `tenantId`. The annotation is enforced by an AspectJ interceptor (`validation/PermissionAllowedAspectInterceptor.java:22-31`) that throws `PermissionConstraintException` when the caller holds none of the listed permissions, so the endpoint is genuinely gated. **That gate is the precondition that makes this a cross-tenant authorization bypass rather than an unauthenticated write.**

### Step 2 — The trust branch in the service

`core/src/main/java/io/aiven/klaw/service/UsersTeamsControllerService.java:558-568`

```java
    int tenantId;
    if (isExternal) {
      if (newUser.getTenantId() == 0) {
        tenantId = commonUtilsService.getTenantId(getUserName());
      } else {
        tenantId = newUser.getTenantId();
      }
      newUser.setTenantId(tenantId);
    } else {
      tenantId = commonUtilsService.getTenantId(getUserName());
    }
```

`getUserName()` is the session identity, and `commonUtilsService.getTenantId(getUserName())` is the caller's own tenant, resolved from the caller's user record (`service/CommonUtilsService.java:503-509`). The `== 0` default path uses it correctly. The `else` branch uses the request body and **never compares the two**. `newUser.setTenantId(tenantId)` then writes the caller-chosen value back into the model, so everything downstream sees it as legitimate.

### Step 3 — The absence that *is* the finding

Every other tenant-scoped operation in this service resolves the tenant from the session — `int tenantId = commonUtilsService.getTenantId(getUserName());` at `svc:172`, `:253`, `:360`, `:408`, `:440`, `:477`, `:567`, `:690`, `:733`, `:802`, `:1006`, `:1122`. `addNewUser` at `:550` is the one method whose tenant resolution is **conditional on request-body content**. There is **no** `requestedTenantId != callerTenantId` check, no allowlist of cross-tenant targets, and no permission scoped to the target tenant. The request model does not constrain the field either — `model/requests/UserInfoModel.java:33` is a bare primitive with no validation annotation (`private int tenantId;`), so any integer is accepted and `0` is the only value with special meaning.

### Step 4 — The write, and the guard that exists

`core/src/main/java/io/aiven/klaw/helpers/db/rdbms/InsertDataJdbc.java:287-296` — the only gate is global username uniqueness; no tenant validation, no team-ownership check against the tenant in the record:

```java
    Optional<UserInfo> userExists = userInfoRepo.findByUsernameIgnoreCase(userInfo.getUsername());
    if (userExists.isPresent()) {
      return "Failure. User already exists";
    }

    userInfoRepo.save(userInfo);
```

The authorization block that *does* run (`svc:574-587`) reads two different scopes. The `ADD_EDIT_DELETE_USERS` check at `:583-587` is **caller-scoped** — `isNotAuthorizedUser` resolves the principal's own tenant (`CommonUtilsService.java:203-207`), so the caller must genuinely be a user administrator in their own tenant. **That check passes and then says nothing about the target tenant.** The superadmin guard at `:579` looks the requested role up in `getRolesPermissionsPerTenant(tenantId)` — and `tenantId` here is the **body-supplied** one. `verifySuperAdminAccess` (`:654-663`) blocks naming `SUPERADMIN_ROLE`, or any role the *target* tenant maps to `FULL_ACCESS_USERS_TEAMS_ROLES` / `UPDATE_PERMISSIONS`, unless the caller already holds those permissions in their own tenant. The guard limits *which role* can be requested; it does not stop the write from landing in the wrong tenant. At `svc:597-601`, with authentication type `db`, `inMemoryUserDetailsManager.createUser(...)` registers the account with the submitted password and role — and tenant binding for an authenticated user is derived from that user's own record (`CommonUtilsService.java:503-509`), so the tenant id stored on the new row is the tenant id the account resolves to.

## Impact — the honest floor

- **Proven by source analysis:** an authenticated caller holding user-creation rights in Tenant A can cause a user record to be written with `tenantId = Tenant B` — any `B` — with an arbitrary username, role, team id and password of the caller's choosing. The write carries **the caller's own privileges**, not elevated ones. **Source-derived but not demonstrated:** with authentication type `db`, the account is registered in the in-memory authentication manager and would resolve to the injected tenant at login (`svc:597-601`, `CommonUtilsService.java:503-509`).
- **Explicitly not claimed:** creating a user in another tenant does **not** by itself grant access to that tenant's data — the created account's effective permissions are whatever the *target* tenant maps its named role to, and a role that does not exist there grants nothing. **No tenant takeover is claimed:** the superadmin guard (`:654-663`) blocks a caller without `FULL_ACCESS_USERS_TEAMS_ROLES` / `UPDATE_PERMISSIONS` in their own tenant from creating a `SUPERADMIN` account. **Impact beyond the cross-tenant write was not demonstrated** — no login as the created account, no cross-tenant data read, and no request executed against a live instance at all.

### Where my own earlier write-up overreached

My local triage note for this defect asserted *tenant takeover* and *access all data in the target tenant*, and stated flatly that the attacker "can now authenticate as `backdoor_admin` in Tenant 2". The source does not support those claims: the guard at `:654-663` exists specifically to stop that, and I have no execution evidence for the login step. The claim I stand behind is the narrower one — **cross-tenant user-record creation** — with downstream consequences reasoned from source and flagged as such.

## Novelty

A public-advisory check of the upstream repository and of OSV/NVD found **no advisory covering this defect**, so no CVE identifier is claimed for it. The project has three published advisories, all covering different mechanisms (password-hash disclosure, a user-lockout case-sensitivity issue, and the `/resetMemoryCache` authorization check); none concerns `addNewUser` or a request-body `tenantId`. The defect is also **still present on upstream `main`** as of this write-up — the same `if (newUser.getTenantId() == 0) { … } else { tenantId = newUser.getTenantId(); }` block, at the same lines, and the same `@PermissionAllowed` annotation on the controller.

## Remediation

Derive the tenant from the caller's identity, and treat any body-supplied value as a request for cross-tenant action that must be authorized explicitly:

```java
// core/src/main/java/io/aiven/klaw/service/UsersTeamsControllerService.java
    int callerTenantId = commonUtilsService.getTenantId(getUserName());
    int requestedTenantId = newUser.getTenantId();

    if (isExternal && requestedTenantId != 0 && requestedTenantId != callerTenantId) {
      // Cross-tenant provisioning must be an explicit, separately authorized action.
      if (!hasCrossTenantProvisioningPermission(callerTenantId, requestedTenantId)) {
        return ApiResponse.notOk("Tenant mismatch: user creation is restricted to your own tenant");
      }
    }

    int tenantId = requestedTenantId != 0 ? requestedTenantId : callerTenantId;
    newUser.setTenantId(tenantId);
```

Hardening, in order of value:

- **Never trust a body-supplied tenant id on a write path.** Drop `tenantId` from the request model for this endpoint and derive it server-side; if cross-tenant provisioning is a real product requirement, expose it as a distinct endpoint with its own permission type rather than as a magic value inside `addNewUser`.
- **Scope the role lookup to the authorized tenant.** `getRolesPermissionsPerTenant(tenantId)` at `:576` consults the *target* tenant while the guard's second half consults the caller's tenant — two different scopes inside one condition. Resolve both against the same authorized tenant.
- **Validate the team id against the tenant being written to**, and **audit-log every user creation with both the caller's tenant and the target tenant** — `insertIntoUsers` (`InsertDataJdbc.java:287-296`) checks only global username uniqueness, and a team id belonging to another tenant should be rejected before the row is written. Add a regression test asserting that a body-supplied `tenantId` differing from the session tenant is refused.

## Results

- **Cross-tenant user-record creation confirmed at source level** — `svc:558-568` trusts the request-body `tenantId` with no comparison against the caller's tenant
- **Endpoint is permission-gated, and the persistence layer adds no tenant validation** — `@PermissionAllowed` on the controller (`ctrl:119-123`), enforced by `PermissionAllowedAspectInterceptor.java:22-31`, plus a caller-scoped `ADD_EDIT_DELETE_USERS` check at `svc:583-587`; but `insertIntoUsers` (`InsertDataJdbc.java:287-296`) validates only global username uniqueness.
- **One guard exists and is narrower than it looks** — the superadmin check at `svc:654-663` constrains the *role*, not the *tenant*.
- **Submitted and reviewed by triage; denied as a “trap.”** No live proof-of-concept was run, so the denial should not be presented as a failed live reproduction. The source-level behavior was identified at the upstream release and was still present on upstream `main` at the time of writing.
- **No CVE claimed** — no advisory for this mechanism was found in the upstream advisory list or in OSV/NVD

---

*Sanitized: organisation and endpoints removed. Full technical report available on request under NDA.*
