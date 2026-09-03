# Case Study 1 — Account Takeover via Missing Re-Authentication

| Field | Value |
|-------|-------|
| **Severity** | High |
| **CWE** | CWE-306 (Missing Authentication) / CWE-620 (Unverified Password Change) |
| **OWASP** | A07:2021 — Identification and Authentication Failures |
| **Surface** | OIDC-based identity stack, millions of accounts |
| **Status** | Packaged — full evidence package delivered |

---

## Problem

A subscription platform let users change their **password and email address with only a valid session cookie** — no current password, no re-authentication, and (for email) no verification step.

Any holder of a session identifier could silently and irreversibly take over the account.

## Attack Chain

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Obtain session  │────▶│  Change email to  │────▶│  Trigger public │
│  cookie (any     │     │  attacker-controlled│    │  password       │
│  method)         │     │  address          │     │  recovery       │
└─────────────────┘     └──────────────────┘     └────────┬────────┘
                                                           │
                                                           ▼
                                                ┌─────────────────┐
                                                │  Recovery code  │
                                                │  sent to        │
                                                │  attacker inbox │
                                                └────────┬────────┘
                                                           │
                                                           ▼
                                                ┌─────────────────┐
                                                │  Set new         │
                                                │  password        │
                                                │  → Full ATO      │
                                                └─────────────────┘
```

## Approach

Found **two independent API surfaces** with the same control gap:

**Surface 1 — Identity Settings API**
- Password change and email (`traits.email`) change accepted with only the session cookie + session-owned double-submit CSRF token
- The CSRF token proves "I hold a session," not "I am the user" — it gates nothing

**Surface 2 — GraphQL Profile Mutation**
- Email-change mutation protected only by an `Origin` header check + session cookies
- No CSRF token at all

**Key Insight:** The CSRF token is owned by the same session the re-authentication step is supposed to gate, so it adds zero security.

**All testing done on my own registered test accounts. No victim, no production account, no MITM.**

## Results

- **Full account takeover** of any account by a session-cookie holder
- Permanent lockout of legitimate user included
- Demonstrated across both surfaces with scripted PoC chains
- Full evidence package: screen recording + screenshots + HTTP request/response pairs

## Remediation Delivered

- Enforce `requirereauthentication` on credential-change flows
- Verify email changes at old AND new addresses
- Require re-auth server-side on GraphQL mutations
- Tighten CORS-with-credentials allowlist

## Tooling

Session capture, CSRF flow scripts, recovery-trigger scripts, login-verification scripts — all developed through AI-assisted generation. I architect the attack chain, review every request, and validate each result end-to-end.

---

*Sanitized: platform/program names and endpoints removed. Full technical report available on request under NDA.*
