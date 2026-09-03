# Case Study 1 — Account Takeover via Missing Re-Authentication on Credential Change

**Finding class:** CWE-306 (Missing Authentication for Critical Function) / CWE-620 (Unverified Password Change) · OWASP A07:2021
**Severity:** High
**Vulnerable surface:** customer identity stack (OIDC-based) of a Tier-1 European media platform, millions of accounts

---

## 1. Problem

A subscription media platform let users change their **password and email address with only a valid session cookie** — no current password, no re-authentication, and (for email) no verification step. Any holder of a session identifier — a leaked session cookie, a session on a shared device, or a session exfiltrated through any other flaw — could silently and irreversibly take over the account.

## 2. Approach

I mapped the full identity flow and found **two independent API surfaces** with the same control gap:

1. **Identity settings API** — password change and email (`traits.email`) change accepted with only the session cookie plus the session-owned double-submit CSRF token (which proves "I hold a session," not "I am the user").
2. **GraphQL profile mutation** — an email-change mutation protected only by an `Origin` header check and session cookies; no CSRF token at all.

The key insight: the CSRF token is owned by the same session the re-authentication step is supposed to gate, so it adds nothing. I then completed the takeover chain via the **public unauthenticated password-recovery flow**:

```
session cookie (no password) → change email → recovery code sent to attacker inbox → set new password → login as victim
```

All testing was done on my own registered test accounts; no victim, no production account, no MITM. The reproduction was pure ordinary HTTPS requests — exactly what a victim's browser sends on every page interaction.

## 3. Result

- **Full account takeover** of any account by a session-cookie holder — permanent lockout of the legitimate user included.
- Demonstrated across both surfaces with scripted PoC chains and a full evidence package (screen recording + screenshots).
- Delivered remediation: enforce `requirereauthentication` on credential-change flows, verify email changes at old and new addresses, require re-auth server-side on the GraphQL mutation, tighten the CORS-with-credentials allowlist.

## 4. Method note

Tooling (session capture, CSRF flow scripts, recovery-trigger and login-verification scripts) developed through AI-assisted generation; I architect the attack chain, review every request, and validate each result end-to-end against test accounts before packaging evidence.

---

*Sanitized: platform/program names and endpoints removed. Full technical report available on request under NDA.*