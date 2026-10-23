# Case Study 10 — Unauthenticated Cross-Tenant Cache Write (permitAll + Shipped-Default Key)

| Field | Value |
|-------|-------|
| **Severity** | P3 (as triaged) — finding demonstrated; closed as Duplicate because an earlier report was filed first |
| **CWE** | CWE-798 (Hard-coded Credentials) / CWE-306 (Missing Authentication) / CWE-284 (Improper Access Control) |
| **OWASP** | A07:2021 — Identification and Authentication Failures · A05:2021 — Security Misconfiguration |
| **VRT** | Broken Access Control → Privilege Escalation |
| **Surface** | Open-source Kafka/cluster admin UI (Aiven-Open/klaw) |
| **Status** | Submitted Bugcrowd 2026-09-27 — finding demonstrated; closed as **Duplicate** because an earlier `/resetMemoryCache` report (2025-09-23) had priority |

---

## Problem

Two defaults that are each individually defensible combine into an unauthenticated, cross-tenant
cache-mutation primitive.

1. **The auth layer treats server-side API paths as static assets.** `ConfigUtils.getStaticResources()`
   returns the `permitAll()` matcher list, and it carries `/resetMemoryCache/**` and `/cache/**` in the
   same bucket as `/js/**` and `/assets/**`. Spring Security never demands a session for them.
   `/resetMemoryCache/**` is additionally stripped from CSRF protection.

2. **The service credential ships with a value.** `klaw.core.app2app.base64.secret` has a non-empty
   default baked into `application.properties`, alongside the default service username `KlawApp2App`
   (which is *also* injected as an `@Value` fallback in two services, so overriding the property alone
   does not remove the default).

An attacker who reads the public repository holds the signing key for the only credential the internal
path actually checks.

**Impact:** forge the internal-service token → call the cache-reset endpoint with an attacker-chosen
`tenantId` / `entityType` / `entityValue` / `operationType` → reload another tenant's cached users,
teams and cluster state — including the in-memory authentication manager — with no session and no
tenant comparison anywhere in the branch.

## Attack Chain

```
┌──────────────────────┐   ┌──────────────────────────┐   ┌──────────────────────────┐
│ Read public repo     │──▶│ HS256-sign JWT with      │──▶│ POST /resetMemoryCache   │
│ application.         │   │ shipped default key,     │   │ tenantId = victim tenant │
│ properties:88        │   │ Roles: ["App2App"]       │   │ entityType = USERS       │
└──────────────────────┘   └──────────────────────────┘   └────────────┬─────────────┘
                                                                       │
   permitAll on /resetMemoryCache/**  +  csrf exemption                ▼
                                              ┌────────────────────────────────────┐
                                              │ UtilControllerService.resetCache   │
                                              │ isValidInternalServiceToken → true │
                                              │ ⇒ skip anonymous / permission /    │
                                              │   tenant checks entirely           │
                                              └────────────────┬───────────────────┘
                                                               ▼
                                              ┌────────────────────────────────────┐
                                              │ updateMetadataCache(m, isLocal=    │
                                              │ false)                             │
                                              │ USERS → loadUsersForAllTenants() + │
                                              │ updateInMemoryAuthenticationManager│
                                              └────────────────────────────────────┘
```

## Approach

### Step 1 — What the auth layer actually exempts

`ConfigUtils.getStaticResources(boolean)` builds the `permitAll` list. Line for line it carries
`/resetMemoryCache/**` and `/cache/**` alongside the static asset globs, and
`csrf.ignoringRequestMatchers("/resetMemoryCache/**")` removes the CSRF backstop on the mutating route.
Both `SecurityConfig` variants wire that list straight into
`.requestMatchers(...).permitAll().anyRequest().authenticated()`.

### Step 2 — What the endpoint checks

`/resetMemoryCache` lives on `UtilController`, a plain `@RestController` with **no**
`@ConditionalOnProperty` — so it is present in **every** deployment, hardened config or not. It requires
an `Authorization` header and hands off to `UtilControllerService.resetCache()`, which branches:

- internal-service token valid → log and proceed, **no further authorization**
- otherwise → anonymous check, `UPDATE_SERVERCONFIG` permission check, plus an
  `ADD_EDIT_DELETE_USERS` check for the `USERS` entity type

The internal branch is `CommonUtilsService.isValidInternalServiceToken()`, which requires exactly four
things: a `Bearer` prefix, a non-blank configured secret, a token that parses under HS256, and a `Roles`
claim containing `App2App`. No tenant binding, no audience, no privilege scoping beyond the JWT's own
`exp`.

### Step 3 — The key

`application.properties` ships `klaw.core.app2app.base64.secret` with a non-empty default
`[REDACTED — base64, publicly readable in the repository]` and
`klaw.core.app2app.username=KlawApp2App`. Any install that has not rotated the value accepts tokens
signed with the shipped key, and the username default is duplicated as an `@Value` fallback in
`SecurityConfigNoSSO` and `HARestMessagingService`.

### Step 4 — Forged token → cross-tenant write

Sign an HS256 JWT with the shipped key, `Roles: ["App2App"]`, arbitrary `sub`, short `exp`. POST
`/resetMemoryCache` with `tenantId` set to a tenant other than the caller's (there is no caller) plus
the target `entityType` / `entityValue` / `operationType`.

The internal branch accepts it, returns `ApiResponse.SUCCESS`, and calls
`commonUtilsService.updateMetadataCache(kwMetadataUpdates, false)` with an object built from the
**attacker-supplied** `tenantId` — constructed *before* any authorization decision is made. There is no
tenant comparison on that branch at all.

Note one guard that *is* present: `entityType=TENANT` with `operationType=DELETE` is refused up front.
Tenant deletion is blocked; tenant-adjacent cache state is not.

### Step 5 — Why it lands on auth state

`updateMetadataCache` dispatches on the attacker-chosen `entityType`:

| `entityType` | Effect |
|---|---|
| `USERS` | `loadUsersForAllTenants()` **and** `updateInMemoryAuthenticationManager(...)` when auth type is `db` |
| `TEAM` | `loadEnvsForOneTenant(tenantId)` + `loadTenantTeamsForOneTenant(null, tenantId)` |
| `CLUSTER` + `DELETE` | cluster-config eviction path |

The `USERS` case is the one that matters: an unauthenticated caller can drive a reload of user data
across all tenants and touch the in-memory authentication manager.

### Step 6 — Adjacent surface: `/cache/**`

`CacheController` maps `/cache` and is gated `@ConditionalOnProperty(prefix = "klaw.core.ha",
name = "enable", matchIfMissing = false)` — **absent unless HA mode is explicitly switched on**, and
`false` is the shipped value. Its `POST` and `DELETE` handlers both call
`validateRole(token, CACHE_ADMIN)`. Because it sits in the same `permitAll` list, the two surfaces share
one root cause: internal-service routes registered as public static resources. I stood up a local HA rig
(`--klaw.core.ha.enable=true`) to exercise it rather than reason about it from source alone.

## Results

- **Unauthenticated entry**: `permitAll` on `/resetMemoryCache/**` and `/cache/**`, CSRF exempt on the former
- **Shipped-default service key** in `application.properties` — no install-time randomization, no forced rotation
- **Cross-tenant**: the request body carries `tenantId` and the internal branch never compares it
- **Present in every deployment**: `UtilController` has no `@ConditionalOnProperty`; `/cache/**` requires
  `klaw.core.ha.enable=true` (ships `false`)
- **Tenant `DELETE` is blocked** — the one guard that exists
- **Triage:** closed as **Duplicate** of the 2025-09-23 report covering `/resetMemoryCache`. I could not
  see the master report's description, so I cannot claim the `/cache/**` path or the hardcoded default key
  were absent from it. Recorded here as methodology, not as a new claim.

## Remediation Delivered

- Remove `/resetMemoryCache/**` and `/cache/**` from the static-resource `permitAll` list; authenticate them
  explicitly as service identity, not as anonymous static assets
- Ship `klaw.core.app2app.base64.secret` **empty**, and refuse the internal branch when it is blank or
  equals a known default — force install-time generation
- Bind the internal-service token to a tenant and/or audience, and enforce a tenant check against the
  requested `tenantId` regardless of token validity
- Remove the `@Value` username fallbacks so overriding the property is actually effective
- Re-enable CSRF on the mutating route, or move it behind a non-cookie auth scheme where CSRF is genuinely
  irrelevant
- Keep `/cache/**` behind `klaw.core.ha.enable`, but treat that flag as a deployment choice rather than a
  security boundary

## Tooling

Repository source review (`SecurityConfig` / `SecurityConfigNoSSO` / `ConfigUtils` / `UtilController` /
`UtilControllerService` / `CommonUtilsService`), local HA-mode rig, Python HS256 sign/verify helper,
request/response capture — AI-assisted generation; I drove the chain and kept every claim inside what the
source and the live rig actually demonstrate.

---

*Sanitized: the shipped default key value, rig passwords, and account handles redacted — the key is public
in the upstream repository and is described here rather than reproduced. Full report and evidence package
on request under program terms.*
