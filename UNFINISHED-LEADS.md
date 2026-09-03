# Research Roadmap — Active Leads (public methodology view)

A generic look at the bug classes I'm actively pursuing. Targets, hosts, and program details are intentionally omitted; the private roadmap lives in `PRIVATE/`.

Ranked by (potential severity × likelihood) ÷ effort.

---

## 1. SSRF on URL-fetch features (image proxy / link unfurl) → cloud metadata

- **Status:** Endpoints mapped from front-end bundles; Playwright-based tester script written that replicates the exact front-end request (CSRF cookie + headers). Live run to completion pending.
- **Why it matters:** a server-side fetch of attacker-controlled URLs on an AWS-hosted app can reach the cloud metadata endpoint — Critical/Exceptional class.
- **Next step:** run the prepared script, capture responses; if the endpoint fetches the supplied URL and returns content/status, build the metadata-exfiltration PoC.

## 2. Same-site XSS to complete a packaged ATO chain

- **Status:** Full account-takeover chain (missing re-auth → public recovery → takeover) is packaged as High. It upgrades to Critical only with a same-site XSS on one of four CORS-trusted origins. Dead-ends documented: reflected params clean, audited SPA bundles have no URL-driven sink, postMessage has an allowlist, XFO/CSP restrictive.
- **Next step:** audit the referenced-but-never-downloaded JS sub-bundles for an attribute-to-JSON sink; sweep SSR routes for URL-to-JSON reflection; check legacy error-page reflection on the weakest CORS-trusted origin.

## 3. SAML assertion replay / bypass

- **Status:** Found a deployment with static AuthnRequest ID + IssueInstant + RelayState across sessions, and an ACS that accepts an empty response. Replay exploit is one real captured assertion away.
- **Next step:** capture a genuine assertion from a test-account browser login, replay it with the static RelayState, and observe whether a session is minted.

## 4. Webhook / event-ingestion SSRF to internal services

- **Status:** Endpoint discovered; only preflight captured; if it forwards attacker-supplied URLs to internal services, it is an internal-estate pivot (internal backends already shown reachable via Host-header routing on the same platform).
- **Next step:** full-method probe with JSON payloads; test URL-forwarding behavior.

## 5. User-enumeration oracle on an account-unlock flow

- **Status:** Unlock-flow initialization confirmed working with a clean 200 and new state token; high rate limit, no captcha. The distinguishing test (real vs fake identifier) was not yet run.
- **Next step:** one comparison test — if responses diverge, it is an automatable enumeration oracle.

## 6. Authenticated IDOR on a session-gated API

- **Status:** Endpoint 401s unauthenticated; enumeration never run with a session.
- **Next step:** with a test-account session, iterate identifiers and compare responses; stop at the first 404 pattern (enumeration discipline).

## 7. Subdomain takeover sweep on wildcard domains

- **Status:** One host ruled out ("not cloud-claimable"); the wildcard set was never swept.
- **Next step:** certificate-transparency dump → resolve → filter CNAME-to-NXDOMAIN → test claimable cloud services. Fully scriptable.

## 8. AI chat-surface prompt injection → data exfiltration

- **Status:** AI chat surfaces fingerprinted; guardrail-bypass-to-profile-exfiltration untested.
- **Next step:** test prompt-injection primitives against the chat assistant (OWASP LLM Top 10 framing); doubles as portfolio material for AI-security roles.

## 9. Watch items

- **Newly-online API:** full OpenAPI spec already downloaded; endpoint currently 503 — re-probe; if live, test authorization + business-logic gaps.
- **OIDC token/state leakage** in redirect flows.
- **Regression watch:** a previously-public admin panel now 403 — a regression would be an easy High.

---

## Sequencing note

Leads 1–2 are the highest value (Critical-tier if they land) and both have most of the tooling already built. Leads 3 and 5 are each one focused test away from a verdict. Everything else is background/scripted. All of this is timeboxed so the application engine never stalls.