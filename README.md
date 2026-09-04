# Rahadrab — Offensive Security Researcher

**CEH — Arena Web Security (Verified: [A52W25045098](https://admission.arenawebsecurity.net/)) | 2+ Years Bug Bounty | Intigriti & Bugcrowd | 100% Remote**

---

## Verified Findings

| Severity | Finding | Class |
|----------|---------|-------|
| **High** | Account Takeover via Missing Re-Authentication (OIDC + GraphQL) | CWE-306 / CWE-620 |
| **P2** | Search-Query Injection → ACL Bypass + Health Data Exposure | CWE-200 / CWE-284 |
| **P2** | WAF Bypass + reCAPTCHA v3 Bypass (4-route cross-product) | WAF Evasion |
| **P3** | CVE-2021-43264 — Unauthenticated Path Traversal (EOL Platform) | CWE-22 |
| **Clean** | SAML 2.0 Audit across 5 enterprise IdPs (25+ federation endpoints) | SAML/SSO |
| **P2** | Segment API Keys Exposed → Analytics Injection | CWE-200 / CWE-538 |
| **P2** | WordPress REST API Public Exposure | CWE-200 / CWE-284 |
| **P2** | postMessage Wildcard Origin → Session Token Leak | CWE-201 / CWE-942 |
| **P2** | Stored XSS via Calculator API → Admin ATO | CWE-79 / CWE-352 |
| **P2** | Drupal Installer + JSON:API Enumeration | CWE-200 / CWE-284 |

> All findings delivered as structured evidence packages: root cause, sanitized reproduction, PoC, impact, remediation. Full technical reports available on request under NDA. Bug bounty reports available in [`reports/`](reports/) directory.

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

## Methodology

| Phase | Description |
|-------|-------------|
| **Recon** | Automated subdomain enumeration, port scanning, service fingerprinting |
| **Dorking** | Google/Bing/GitHub dorking for exposed endpoints, secrets, and API documentation |
| **Discovery** | Manual endpoint mapping, API surface analysis, technology stack identification |
| **Vulnerability Research** | Auth logic analysis, access control testing, business logic evaluation |
| **Exploitation** | PoC development, exploit chain construction, impact validation |
| **Reporting** | CWE/OWASP mapping, GDPR impact framing, structured evidence packages |

**Automation toolkit:**
- `auto-workflow.sh` — 500+ line recon pipeline (subdomain enum → port scan → service detection → tech fingerprint)
- `auto-dork.sh` — 1000+ line Google/Bing/GitHub dorking automation
- Custom Python/Bash scripts for CSRF chains, search injection, session capture, OIDC flows

All PoCs developed through AI-assisted generation, architected and validated by me.

---

## Automation & Tooling

| Script | Purpose |
|--------|---------|
| [`auto-workflow.sh`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/auto-workflow.sh) | 500+ line recon pipeline — subdomain enum, DNS resolution, HTTP probing, directory scanning, URL crawling, vuln scanning, SQLi testing, JS analysis, OIDC discovery, GraphQL/S3/CORS testing, nmap, CMS scanning |
| [`auto-dork.sh`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/auto-dork.sh) | 1000+ line multi-engine dorking — Google, Bing, DuckDuckGo, GitHub code search, InternetDB/crt.sh, Wayback Machine, AlienVault OTX, FOFA, URLScan.io, cloud bucket enumeration, subdomain dorking |
| [`lucene_injection_poc.py`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/lucene_injection_poc.py) | Search query injection → ACL bypass exploitation for Elasticsearch/Lucene backends |
| [`csrf_poc.py`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/csrf_poc.py) | CSRF chain development with auto-generated PoC HTML and cURL commands |
| [`graphql_control.py`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/graphql_control.py) | GraphQL authorization testing — introspection, depth limiting, batching, info disclosure |
| [`turnstile-bypass.py`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/turnstile-bypass.py) | Cloudflare Turnstile implementation weakness research |
| [`jwt_none_runner.py`](https://github.com/Rahadrab/SEC-SCRIPTS/blob/master/jwt_none_runner.py) | JWT algorithm confusion testing — none algorithm, HS256/RS256 confusion, key injection |

All scripts: [github.com/Rahadrab/SEC-SCRIPTS](https://github.com/Rahadrab/SEC-SCRIPTS)

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

### [Case Study 4 — Segment API Keys Exposed](case-studies/CASE-STUDY-4-SEGMENT-KEYS-EXPOSED.md)
Two Segment Write Keys exposed in client-side JavaScript, both tested with `{"success": true}` API responses. Enables fake analytics injection and data corruption. **Triage:** RESOLVED/APPROVED — analyst validated independent verification work and closed as RESOLVED.

### [Case Study 5 — WordPress REST API Exposed](case-studies/CASE-STUDY-5-WORDPRESS-REST-API.md)
Unauthenticated `/wp-json/` endpoint exposure on production subdomain. **Triage:** Informative — analyst said "keep submitting findings" and acknowledged the report.

### [Case Study 6 — postMessage Wildcard Origin](case-studies/CASE-STUDY-6-POSTMESSAGE-WILDCARD.md)
Enforcement iframe uses `postMessage()` with wildcard origin (`*`), exposing session tokens to any embedding website. 8 calls identified, all leak `sessionToken:e.token`. **Triage:** Duplicate — analyst was courteous: "appreciate the time you invested... look forward to your next awesome bug report."

### [Case Study 7 — Drupal + JSON:API Enumeration](case-studies/CASE-STUDY-7-DRUPAL-JSON-ENUMERATION.md)
VDP — Drupal Installer Exposure (P3→P2) + JSON:API User Enumeration. **Triage:** RESOLVED — analyst said "Welcome to the program! Thanks for this finding — the team was able to restrict the leaked information." Severity upgraded P3→P2 during triage.

### [Case Study 8 — Stored XSS via Calculator API → Admin ATO](case-studies/CASE-STUDY-8-STORED-XSS-CALCULATOR-API.md)
P2 Critical (CVSS ~9.8). Stored XSS in calculator API endpoint persisted in database, executed on admin pages, enabling full admin account takeover. **Triage:** Triaged (dispute) — finding validated by technical merits but closure impacted by new-account status on program. 27+ evidence files including video proof. Support Ticket open 5+ days.

---

## What Recruiters Say

> "I built a full critical-bypass package, then proved to myself the endpoint was an SPA catch-all that returned the same response for any token — so I withdrew it. Verifying your own findings, even painful ones, is what makes a report trustworthy."

---

## How to Use This Portfolio

| Role Type | Attach |
|-----------|--------|
| Auth / API Security | Case Study 1 (ATO) |
| Web AppSec / Pentest | Case Study 2 (Search Injection), Case Study 7 (Drupal/JSON:API) |
| Red Team / Offensive | Case Study 1 (ATO), Case Study 8 (Stored XSS → Admin ATO) |
| Security Analyst / Triage | Case Study 3 (CVE), Case Study 6 (postMessage) |
| Bug Bounty / Reporting | Case Study 4 (Segment Keys), Case Study 5 (WP REST API) |
| AI Security | Case Study 1 + prompt injection research |

**Rule:** CV + max 2 redacted PDFs, matched to role. Never attach raw evidence or program-named files.

---

*All findings are sanitized. Platform/program names removed. Built from Intigriti + Bugcrowd engagements, Jun–Aug 2026.*
