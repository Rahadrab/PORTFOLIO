# CV Bullets + LinkedIn Headline — Evidence-Backed Update (Sep 2026)

Built from the real WORK archive assessment (`SKILLS-ASSESSMENT.md`). Every line is backed by a packaged finding with PoC. **Keep the "2+ years bug bounty, accepted P2–Critical, verifiable on profiles" claim identical everywhere — never blur bounty into employment.**

---

## 1. CV bullet block (paste at top of Experience/Projects — replaces the old block)

> **Offensive Security Researcher / Bug Bounty — 2+ years (Intigriti `rahadrab`, Bugcrowd `rahat_rab`)**
> - **Auth & session logic:** Identified missing re-authentication on credential change (password + email) across two independent API surfaces (OIDC identity stack + GraphQL) and chained it to full account takeover via the public recovery flow; built scripted PoC chains and delivered a screen-recorded evidence package. (High, packaged)
> - **SAML/SSO:** Audited SAML 2.0 implementations across enterprise IdPs (Azure AD, Okta, ADFS, Shibboleth, CyberArk) — AuthnRequest hygiene, ACS signature enforcement, static-nonce analysis, RelayState handling; tested 25+ federation endpoints and reported clean negatives where controls held.
> - **Access control:** Bypassed application-level ACL scoping via search-query injection (Lucene/Elasticsearch), exposing permission-gated internal documents including health-related data; enumerated indexed user fields. (GDPR Art. 9 impact framing)
> - **WAF bypass engineering:** Bypassed application WAFs using query-operator comment syntax, method switching, and Host-header routing to reach unprotected controls — including a reCAPTCHA v3 bypass across a 4-route cross-product login matrix (P2, packaged).
> - **Known-CVE exploitation:** Verified an unauthenticated path traversal (CVE-2021-43264) on an EOL ePortfolio platform — arbitrary file read, relative + absolute PoCs, version-confirmed vulnerable.
> - **Tooling:** Authored 15+ Python/bash PoC and automation scripts (session capture, CSRF/anti-CSRF chains, search-injection proof, OIDC flows, evidence capture) — developed through AI-assisted generation, architected and validated by me.
> - **Reporting discipline:** Every finding delivered as a structured evidence package (root cause, sanitized reproduction, PoC, impact, remediation); I withdraw my own false positives rather than submit them — including a critical-looking JWT bypass I proved to be a non-vulnerability.

## 2. LinkedIn headline v3 (evidence-backed, fits 220 chars)

> Offensive Security Researcher (CEH) | Auth/SAML/SSO, WAF Bypass, ACL Escalation | Accepted P2–Critical on Intigriti · Bugcrowd | AI-Directed PoC Automation | 100% Remote · Any Timezone

*(v3 replaces v2: leads with the now file-backed skill areas instead of the JWT/XSS names.)*

## 3. LinkedIn About (top 3 lines)

> Offensive security researcher (CEH) with 2+ years of verified bug bounty work on Intigriti and Bugcrowd — accepted P2–Critical findings including an account-takeover chain via missing re-authentication, a WAF-bypass + ACL-escalation chain exposing internal health data, and a verified CVE exploitation on an EOL platform. Every finding ships as a structured evidence package with remediation. **100% remote; timezone-flexible (UTC+6) — can match US/EU/APAC hours.**

## 4. PoC attachment map (updated — match to role)

| Role you're applying to | Attach (max 2) |
|---|---|
| Identity / auth / API security | Redacted ATO-missing-reauth case study (Case Study 1) |
| Web AppSec / pentest | Case Study 2 (search-injection ACL bypass) |
| Red team / offensive | Case Study 1 + Case Study 2 |
| Security analyst / triage / junior | Case Study 3 (CVE verification) + the JWT false-positive story in the cover note |
| AI-security roles | Case Study 1 (auth) + the local-LLM guardrail write-up once published (DEEP-RESEARCH plan) |

**Attach rule (unchanged):** CV + at most 1–2 PDFs, matched to the role, always redacted. Never attach raw evidence, session JSONs, or program-named files.

## 5. Interview one-liner for the false-positive story (use verbatim)

> "I built a full critical-bypass package, then proved to myself the endpoint was an SPA catch-all that returned the same response for any token — so I withdrew it. Verifying your own findings, even painful ones, is what makes a report trustworthy."

## 6. Consistency fixes (from OUTREACH-KIT §0 — still pending on your CV)

- Use **"2+ years"** everywhere (not 2 vs 2–3).
- Remove all "mid-to-senior" and "relocation/sponsorship" language → "Seeking remote red team / offensive security roles; timezone-flexible."
- Add the OneDrive redacted-PoC folder under Contact info → websites; pin Case Study 1 + 2 under Featured.