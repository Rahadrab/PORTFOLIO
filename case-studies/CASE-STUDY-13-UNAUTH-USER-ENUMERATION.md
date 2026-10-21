# Case Study 13 — Unauthenticated Username Enumeration via Self-Registration

| Field | Value |
|-------|-------|
| **Severity** | MEDIUM (P2) as analysed — never triaged |
| **CWE** | CWE-203 (Observable Discrepancy) / CWE-200 (Exposure of Sensitive Information to an Unauthorized Actor) / CWE-770 (Allocation of Resources Without Limits or Throttling) |
| **OWASP** | A07:2021 — Identification and Authentication Failures · A04:2021 — Insecure Design |
| **Surface** | Open-source Kafka/cluster admin UI (Aiven-Open/klaw) — unauthenticated self-registration endpoint |
| **Status** | Local finding — **never submitted to any platform, never triaged**. Reproduced live against a local instance of the open-source product (2.10.6) |

---

## Problem

An unauthenticated caller can turn the self-registration endpoint into a **username oracle**,
and can use the same endpoint to **flood the administrator's approval queue** and mailbox.

`POST /registerUser` sits in the Spring Security `permitAll` list (`ConfigUtils.java:43`; wired
at `ConfigUtils.java:77-82`), so no session is required. The only barrier is a CSRF token, and
that token is handed to anyone by `GET /login`, which is itself `permitAll`
(`ConfigUtils.java:31`). The CSRF filter exempts only `/logout` and `/resetMemoryCache/**`
(`ConfigUtils.java:72-73`) — `/registerUser` is *not* CSRF-exempt, but a publicly obtainable
cookie is not an authentication boundary.

The service then returns a **distinguishable response** for a name that already exists versus
one that does not, and on the "does not exist" branch it **creates a pending registration row**
and emails the tenant administrator about it.

**Impact, stated precisely:**

1. **Unauthenticated username enumeration.** An anonymous caller learns whether a given
   username exists, across **all tenants** — the existence check reads the full user table, not
   a tenant-scoped one.
2. **Unauthenticated pending-registration creation.** Every fresh username creates a `PENDING`
   row in the admin approval queue and triggers a notification email to the tenant admin — a
   queue-flooding and mail-flooding primitive with no rate limiting on the endpoint.
3. **Second-order effect (verified live):** an approved pending registration becomes a fully
   working account, so the oracle feeds directly into an account-provisioning path. The
   administrator's approval is the **only** human step in that chain.

## Attack Chain

```
┌───────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│ Anonymous caller  │─▶│ GET /login       │─▶│ XSRF-TOKEN cookie│
│ no session, no    │  │ permitAll        │  │ publicly issued  │
│ credentials       │  │ ConfigUtils:31   │  └────────┬─────────┘
└───────────────────┘  └──────────────────┘           │
                                                       ▼
                            ┌────────────────────────────────────────┐
                            │ POST /registerUser  (permitAll, :43)    │
                            │ {username, mailid, pwd, fullname, …}    │
                            └───────────────────┬────────────────────┘
                     ┌────────────────────────┴──────────────────────┐
                     ▼                                               ▼
   ┌──────────────────────────────────┐    ┌──────────────────────────────────┐
   │ username exists                  │    │ username fresh                   │
   │ Service.java:896-902             │    │ Service.java:936-993             │
   │ notOk(TEAMS_ERR_115)             │    │ PENDING row written, admin       │
   │ {"success":false,                │    │ notified by mail                 │
   │  "message":"User already exists."│    │ {"success":true,"message":"success"}│
   │ ⇒ USERNAME CONFIRMED             │    │ ⇒ QUEUE ROW + MAIL CREATED       │
   └──────────────────────────────────┘    └────────────────┬─────────────────┘
                                                            ▼
                              ┌────────────────────────────────────────┐
                              │ Admin approves (human gate)            │
                              │ POST /execNewUserRequestApprove        │
                              │ Service.java:1027-1069 → addNewUser    │
                              └───────────────────┬────────────────────┘
                                                  ▼
                              ┌────────────────────────────────────────┐
                              │ Working low-privilege account (live)    │
                              └────────────────────────────────────────┘
```

## Evidence

Every claim is tagged: **[code]** = a specific file and line in the upstream source;
**[live]** = the request/response exchange reproduced against the running product.

**What the code does — [code]**

- `ConfigUtils.java:43` — `"/registerUser"` in the `permitAll` list; `ConfigUtils.java:77-82`
  applies `.permitAll()` to it and `.authenticated()` to everything else.
- `UsersTeamsControllerService.java:888-902` — `registerUser` lowercases the submitted username
  (`:891`), loads **all users across all tenants** via `getAllUsersAllTenants()` (`:895` →
  `SelectDataJdbc.java:1637-1639` → `userInfoRepo.findAll()`), then runs two existence checks
  that both return `ApiResponse.notOk(TEAMS_ERR_115)`: `:896-898` compares
  `user.getUsername()` to `newUser.getMailid()`, `:899-902` compares `user.getUsername()` to
  `newUser.getUsername()`. `KlawErrorMessages.java:478` — `TEAMS_ERR_115 = "User already
  exists."`; `ApiResponse.java:30-36` — `ok()`/`notOk()` serialise to the `success` boolean +
  `message` the client sees.
- `UsersTeamsControllerService.java:936-993` — on the fresh-name branch the row is written with
  `status = PENDING` (`:937`), the password bcrypt-encoded (`:947`), the row inserted (`:981`),
  and the admin notification mail fired (`:986-989`). `dao/RegisterUserInfo.java:20` — pending
  registrations persist in the `kwregisterusers` table; `MailUtils.java:177-218` — the mail goes
  to the tenant admin (`:193-195` → `sendMailToAdmin` → `:244-253` →
  `emailService.sendSimpleMessage`) and to the submitted mail address (`:208-218`).
- `UsersTeamsControllerService.java:1027-1069` — approval reads the pending row, calls
  `addNewUser` (`:1058`) and flips the row to `APPROVED` (`UpdateDataJdbc.java:486-509`);
  `UsersTeamsController.java:167-179` — that endpoint is permission-gated
  (`APPROVE_ALL_REQUESTS_TEAMS` / `FULL_ACCESS_USERS_TEAMS_ROLES`). **This is the only human
  step in the chain.** No rate limiting exists on this path — no filter, interceptor, throttle
  or per-IP limiter is registered for the endpoint in the core module.

**The live differential — [live]**

Reproduced against a local instance of the open-source product (2.10.6), **no credentials**, and
only the publicly issued XSRF cookie:

```
GET  /login
  -> XSRF-TOKEN cookie (36 chars)

POST /registerUser  { "username": "<existing-username>", "mailid": "<fresh-email-1>", ... }
  -> {"success":false,"message":"User already exists."}

POST /registerUser  { "username": "<fresh-username>",  "mailid": "<fresh-email-2>", ... }
  -> {"success":true,"message":"success"}
```

Same endpoint, same method, no authentication — a one-bit difference in the response body
distinguishes "this username exists" from "this username does not exist". The second response
also **creates** the pending registration row and **sends** the administrator notification.

**The second-order effect — [live]**

The pending row created by the oracle-registered request was approved through the normal
administrator flow, and the account became **fully active**: login succeeded and the session
landed on the dashboard. The chain is therefore: unauthenticated register → admin approval →
working low-privilege account. The approval gate is the only human step, and on a busy
deployment the queue it reads is exactly the queue the attacker may pollute.

## What I Corrected Before Publishing This

My first-pass write-up was wrong on the scope of the oracle, and the correction changed the
claim rather than just the wording.

**The original report claimed the service "checks existence of both username and mailid against
all tenants"** — which reads as an oracle over *both* usernames and registered email addresses.
The actual code does not do that:

```java
// UsersTeamsControllerService.java:896-902
if (userList.stream()
    .anyMatch(user -> user.getUsername().equalsIgnoreCase(newUser.getMailid()))) {
  return ApiResponse.notOk(TEAMS_ERR_115);
} else if (userList.stream()
    .anyMatch(user -> user.getUsername().equalsIgnoreCase(newUser.getUsername()))) {
  return ApiResponse.notOk(TEAMS_ERR_115);
}
```

Both sides of **both** comparisons read `user.getUsername()` — the **existing user's USERNAME**
from the `kwusers` table. The submitted `mailid` is only ever the *right-hand side of a
comparison against usernames*; the stored `mailid` column (`dao/UserInfo.java:40-41`) is never
read by the existence check. No code path here compares a submitted address against a
registered address.

**So the supported finding is:**

- The endpoint enumerates **usernames**. Via the `username` field, a submitted name matching an
  existing user's username returns `"User already exists."`.
- The `mailid` field is a **second username probe**, not an email probe: a submitted `mailid`
  equal to an existing user's *username* also returns `"User already exists."` — it matches the
  same username column. (Because `mailid` carries `@Email` validation
  (`RegisterUserInfoModel.java:39-40`), that branch is only reachable for username strings that
  are themselves email-shaped; usernames here may be email IDs, per
  `RegisterUserInfoModel.java:19`.)
- It does **not** confirm whether a given email address is registered. My original claim of
  "enumeration of valid usernames **and** email addresses" was not supported by the code, and
  this write-up reflects the code, not the first draft.

**Not claimed, because it was not tested:** whether the request body can influence the role
attached to a registration on approval, and any privilege outcome beyond the low-privilege
account actually observed. Those were not exercised and carry no claim here.

## Results

- **Unauthenticated username oracle** — `POST /registerUser` returns a distinguishable
  `success`/`message` pair for existing vs fresh usernames
- **Cross-tenant** — the existence check reads the full user table (`getAllUsersAllTenants()` →
  `findAll()`), so usernames from every tenant are probeable
- **No email oracle** — corrected; the stored mailid column is never compared
- **Unauthenticated queue creation** — each fresh username writes a `PENDING` row to
  `kwregisterusers` and mails the tenant admin
- **No rate limiting** on the endpoint
- **Second-order path verified live** — approval turns the pending row into a working account
- **Not submitted to any platform and never triaged** — a self-published analysis of a defect
  found in an open-source product; the severity above is my own assessment

## Remediation

- Return an **identical response** for "already exists" and "fresh" on the unauthenticated
  registration path — same success flag, same message, same status code. Do the uniqueness check
  in the database layer (`findByUsernameIgnoreCase` already exists for this) and surface one
  neutral outcome to the caller.
- Never include a submitted identifier in an error string that distinguishes account state.
- Apply **rate limiting / per-IP throttling** to the unauthenticated registration endpoint, and
  cap how many pending registrations one source can leave in the approval queue.
- Do not send an administrator notification email for an unauthenticated, unapproved
  registration — or batch and rate-limit those notifications, so the queue cannot be used as a
  mail amplifier.
- Present the pending-registration queue so bulk spam is distinguishable (source, volume,
  freshness) to the reviewer who approves from it, and keep the approval gate mandatory.

---

*Sanitized: organisation and endpoints removed. Full technical report available on request under NDA.*
