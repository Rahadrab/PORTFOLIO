# Case Study 12 — Authorization Gate That Never Denies: `validateRole()` Boolean Logic Flaw

| Field | Value |
|-------|-------|
| **Severity** | **CRITICAL (9.8) as analysed** — my own assessment; not a triage severity |
| **CWE** | CWE-480 (Use of Incorrect Operator) / CWE-863 (Incorrect Authorization) / CWE-287 (Improper Authentication) |
| **OWASP** | A01:2021 — Broken Access Control · A07:2021 — Identification and Authentication Failures |
| **VRT** | Broken Access Control → Privilege Escalation (analyst classification) |
| **Surface** | Open-source Kafka/cluster admin UI (Aiven-Open/klaw) — `core` module, `JwtTokenUtilService` |
| **Status** | **Submitted and reviewed by triage.** Source analysis is based on the upstream release (tag `v2.10.6`, commit `631b680`); no live exploit was demonstrated |

---

## Problem

One character of boolean logic makes the application's role check decorative. The helper
guarding the cache-management routes joins three validation conditions with `&&`:

```java
// JwtTokenUtilService.java:65-75
public void validateRole(String token, String... expectedRole) throws KlawNotAuthorizedException {
  token = token.substring(BEARER.length()).trim();
  Claims claims = getAllClaimsFromToken(token);
  UserDetails user = userDetailsService.loadUserByUsername(getUsernameFromToken(token));
  if (user == null
      && !validateToken(token)
      && !claims.get("Roles", List.class).containsAll(List.<String>of(expectedRole))) {
    throw new KlawNotAuthorizedException("UnAuthorized");
  }
}
```

The exception is raised **only when all three conditions hold simultaneously** — the subject
does not exist **and** the token is expired/invalid **and** the role is absent. Any one of
them on its own defeats the check, and this is the *only* role gate on these routes.

**Impact, at the level the source supports:** the role check never denies. A caller whose
token parses and whose subject resolves reaches the guarded handler whatever role the token
carries — including none at all. Both guarded routes are `permitAll` at the Spring Security
layer, so the broken gate is the *entire* authorization boundary. This is a **source-code
analysis finding**, confirmed by reading the upstream release, not a live-proven exploit.

## The Defect in Source

- `JwtTokenUtilService.java:70-72` — the `&&` chain, verified byte-for-byte against upstream
  `v2.10.6` (`631b680`), `git diff` against `HEAD` empty. Only two commits ever touched the
  file upstream and the `&&` form is in both, so it is not a local artefact.
- `JwtTokenUtilService.java:68-69`, `:61-63` — the token is parsed *before* the gate is
  evaluated, then the subject is resolved (this makes the first operand false);
  `validateToken` = `!isTokenExpired(token)`, true only for an expired token.
- `JwtTokenUtilService.java:23` · `CacheController.java:19` — both beans need
  `klaw.core.ha.enable`; the controller adds `matchIfMissing = false`. `CacheController.java:37`,
  `:51` — the only two call sites, both `CACHE_ADMIN`. `ConfigUtils.java:47` +
  `SecurityConfigNoSSO.java:79-82` — `/cache/**` is `permitAll`, so no session is needed.

## Attack Chain

```
┌────────────────────────────┐──▶┌──────────────────────────────────────┐
│ Any username that resolves │──▶│ Mint a JWT with the app-to-app HMAC  │
│ in the instance's user     │──▶│ secret; subject = that username,     │
│ store (any account,tenant) │──▶│ Roles = omitted or any other role    │
└────────────────────────────┘   └───────────────────┬──────────────────┘
                                                    ▼
                     ┌────────────────────────────────────────────────────────────┐
                     │ POST/DELETE /cache/**  (permitAll — no session)            │
                     │ validateRole(token, CACHE_ADMIN)          [line 65]        │
                     │ line 68 parse OK → line 69 user != null                    │
                     │ line 70 FALSE ⇒ line 73 throw NEVER reached                │
                     └─────────────────────────────┬──────────────────────────────┘
                                                    ▼
                     ┌────────────────────────────────────────────────────────────┐
                     │ CacheController.addEnvToCache / removeEnvFromCache         │
                     │ [lines 37, 51] ⇒ cache mutated for a caller-chosen tenant  │
                     └────────────────────────────────────────────────────────────┘
```

## What the Code Permits

The `&&` chain is the whole defect: every combination in which any one operand is false passes
the guard — a resolvable user, an unexpired token, or a present role each defeat it on their
own. The only failing row is `user == null AND !validateToken AND role absent`.

The three scenarios I recorded are all downstream of this one chain, and differ in how far
the source alone carries them:

1. **Valid user, wrong role — fully supported by the source.** A correctly signed, unexpired
   token whose subject resolves passes the gate whatever role the `Roles` claim carries:
   `user != null` makes the first operand false and the chain short-circuits to "no throw".
2. **Expired token, existing user — the chain tolerates it.** `validateToken` returns
   `!isTokenExpired(token)` (line 61-63), so an expired token makes the second operand true
   while the first is false, and the chain still passes. Caveat: line 68 parses first, and
   JJWT rejects expired tokens by default, so such a token is expected to raise a JWT
   exception before the gate is evaluated — reachability depends on parser configuration.
3. **Forged token, valid username — same caveat, different mechanism.** The chain would
   tolerate a bad signature for the same reason, but line 68 calls `parseClaimsJws` against
   the HMAC key (lines 48-52), which verifies the signature, so a wrongly-signed token is
   expected to fail at the parser rather than reach the gate.

The *logical* defect is unconditional; the *practical* consequence of scenarios 2 and 3
depends on the JWT parser configuration upstream of line 68.

## Honest Limits

- **The check throws only when all three conditions are true.** A valid user, a valid token,
  **or** a present role each individually defeats it. That is the whole defect, and it holds
  independently of any HTTP response, error page, or rendered artifact.
- **In the shipped configuration, `user == null` is effectively unreachable.** Both
  `UserDetailsService` beans are Spring's `InMemoryUserDetailsManager`
  (`CacheConfig.java:91` for AD auth, `SecurityConfigNoSSO.java:170` for DB auth), and that
  implementation raises `UsernameNotFoundException` rather than returning `null`. The first
  operand is therefore false whenever the call at line 69 returns, so the `throw` at line 73
  has no path — a framework contract I assert, not a runtime observation.
- **The guarded surface is off by default** (`klaw.core.ha.enable` gates both beans), and a
  validly signed token is still a precondition: line 68 parses against the shared app-to-app
  HMAC secret (`klaw.core.app2app.base64.secret`), so the chain is not a bypass of signature
  verification. The signing-key angle is a separate finding — Case Study 10 — not re-claimed
  here.

## What I Got Wrong — and Caught by `git diff`

This finding nearly became a false negative through a process error, not a technical one. The
working copy of the repository carried an **uncommitted local patch** changing exactly the two
`&&` operators at lines 71-72 to `||` — the remediation, pasted into the evidence tree.
Grepping the file locally, or reading it in an editor, showed the *fixed* logic and the
correct conclusion was "this bug does not exist"; only `git diff` showed the change was mine,
not upstream's.

The lesson I keep: **check `git status` before quoting source, every time.** A local patch
silently inverts a conclusion and no amount of reading the file reveals it — quote from
`git show HEAD:<path>`. The same trap had a second edge: the compiled artifact had been
rebuilt from the patched source, so a rig run would have exercised the *fixed* code and
produced a false negative for a live test.

## Results

- **Confirmed by source analysis at the upstream release** — `&&` chain verified present at
  tag `v2.10.6` / commit `631b680`; `git diff` against `HEAD` empty
- **The role gate never denies** for a caller with a parseable token and a resolvable subject;
  only two call sites, both `CACHE_ADMIN`, on routes that are themselves `permitAll`
- **Not a live-proven exploit** — source-based analysis; no rig was run against the upstream build for this write-up
- **Submitted and reviewed by triage** — the source-level finding was not live-tested against the upstream build for this write-up.

## Remediation

The one-character fix — `&&` becomes `||`, so the exception is raised when **any** condition
fails, which is the intent the method name states:

```java
// JwtTokenUtilService.java:70-72  — corrected
    if (user == null
        || !validateToken(token)
        || !claims.get("Roles", List.class).containsAll(List.<String>of(expectedRole))) {
      throw new KlawNotAuthorizedException("UnAuthorized");
    }
```

Beyond the operator fix:

- Treat a missing subject explicitly (`UsernameNotFoundException` → deny) rather than relying
  on a `null` comparison the shipped `UserDetailsService` never produces
- Deny on a token that fails to parse, rather than letting the parser's exception escape as
  the authorization outcome
- Remove `/cache/**` from the static-resource `permitAll()` list, bind the role check to a
  tenant as well as a role name (Case Study 10 covers the same list), and add a unit test
  asserting a wrong role is rejected — the current suite passes with the defect present

---

*Sanitized: organisation and endpoints removed. Full technical report available on request under NDA.*
