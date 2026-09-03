# Rahadrab — Offensive Security Researcher

**CEH (Verified: [A52W25045098](https://admission.arenawebsecurity.net/)) | 2+ Years Bug Bounty | Intigriti & Bugcrowd | 100% Remote**

---

## Verified Findings

| Severity | Finding | Class |
|----------|---------|-------|
| **High** | Account Takeover via Missing Re-Authentication (OIDC + GraphQL) | CWE-306 / CWE-620 |
| **P2** | Search-Query Injection → ACL Bypass + Health Data Exposure | CWE-200 / CWE-284 |
| **P2** | WAF Bypass + reCAPTCHA v3 Bypass (4-route cross-product) | WAF Evasion |
| **P3** | CVE-2021-43264 — Unauthenticated Path Traversal (EOL Platform) | CWE-22 |
| **Clean** | SAML 2.0 Audit across 5 enterprise IdPs (25+ federation endpoints) | SAML/SSO |

> All findings delivered as structured evidence packages: root cause, sanitized reproduction, PoC, impact, remediation. Full technical reports available on request under NDA.

---

## Skills

| Category | Details |
|----------|---------|
| **Auth & Identity** | OIDC, SAML 2.0, session management, OAuth flows, CSRF chains |
| **Web AppSec** | OWASP Top 10, API security, business logic, access control |
| **WAF Evasion** | Query-operator injection, method switching, Host-header routing |
| **Tooling** | Python, Bash, Burp Suite, Nmap, Metasploit, Kali Linux |
| **Reporting** | CWE/OWASP mapping, GDPR impact framing, structured evidence packages |
| **Frameworks** | MITRE ATT&CK, PTES, OWASP Testing Guide |

---

## Profiles

| Platform | Link |
|----------|------|
| LinkedIn | [rahadrab](https://www.linkedin.com/in/rahadrab/) |
| Intigriti | [rahadrab](https://app.intigriti.com/researcher/profile/rahadrab/) |
| Bugcrowd | [rahat_rab](https://bugcrowd.com/h/rahat_rab) |

---

## Case Studies (Sanitized)

### [Case Study 1 — Account Takeover via Missing Reauth](case-studies/CASE-STUDY-1-ATO-MISSING-REAUTH.md)
Full ATO chain across two independent API surfaces (OIDC identity stack + GraphQL). Session cookie → change email → recovery flow → account takeover. Screen-recorded evidence package.

### [Case Study 2 — Search Injection → ACL Bypass](case-studies/CASE-STUDY-2-SEARCH-INJECTION-ACL-BYPASS.md)
Lucene/Elasticsearch injection bypassing WAF + scope controls. Exposed 5,940 internal hospital documents including health data (GDPR Art. 9). User-field enumeration via indexed store.

### [Case Study 3 — CVE-2021-43264 Path Traversal](case-studies/CASE-STUDY-3-KNOWN-CVE-EOL-TRAVERSAL.md)
Verified unauthenticated arbitrary `.html` file read on EOL university platform. Version-confirmed vulnerable, relative + absolute PoC chains.

---

## What Recruiters Say

> "I built a full critical-bypass package, then proved to myself the endpoint was an SPA catch-all that returned the same response for any token — so I withdrew it. Verifying your own findings, even painful ones, is what makes a report trustworthy."

---

## How to Use This Portfolio

| Role Type | Attach |
|-----------|--------|
| Auth / API Security | Case Study 1 (ATO) |
| Web AppSec / Pentest | Case Study 2 (Search Injection) |
| Red Team / Offensive | Case Study 1 + Case Study 2 |
| Security Analyst / Triage | Case Study 3 (CVE) + false-positive story |
| AI Security | Case Study 1 + prompt injection research |

**Rule:** CV + max 2 redacted PDFs, matched to role. Never attach raw evidence or program-named files.

---

*All findings are sanitized. Platform/program names removed. Built from Intigriti + Bugcrowd engagements, Jun–Aug 2026.*
