# Rahadrab — Offensive Security Researcher

**CEH — Arena Web Security (Verified: [A52W25045098](https://admission.arenawebsecurity.net/)) | 2+ Years Bug Bounty | Intigriti & Bugcrowd | 100% Remote**

---

## Verified Findings

| Severity | Finding | Class |
|----------|---------|-------|
| **High** | Account Takeover via Missing Re-Authentication (OIDC + GraphQL) | CWE-306 / CWE-620 |
| **High** | Credential Leakage & Persistent MITM — Cluster-API JWT Exfil (CVSS 8.1) | CWE-522 / CWE-200 |
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

## Burp Suite Extensions (Active)

Professional-grade extensions for advanced testing — not the default suite.

| Extension | Purpose | Category |
|-----------|---------|----------|
| **Param Miner** | Discovers hidden, unlinked parameters — web cache poisoning, unlinked param injection | Recon |
| **Active Scan++** | Enhanced active scanner with 50+ additional checks beyond Burp default | Scanning |
| **TLS Fingerprint Bypasser** | Mutates TLS ciphers to bypass bot detection (Cloudflare, Akamai, AWS WAF) | WAF Bypass |
| **CSRF Scanner** | Passive CSRF detection — analyzes tokens, checks for missing validation | Auth Testing |
| **JOSEPH** | JOSE (JWT/JWS/JWE/JOSE) pentesting helper — algorithm confusion, key injection | Auth/JWT |
| **WAF Bypass** | Adds headers to all requests to bypass WAF products | WAF Bypass |
| **Burp MCP** | MCP server integration — AI-assisted analysis and automation | AI/Automation |

**These are not default Burp extensions.** Each one fills a specific gap in the standard toolset. Combined, they enable:
- Hidden parameter discovery → cache poisoning → data exposure chains
- TLS fingerprint evasion → WAF bypass → deeper scope access
- JWT/JOSE algorithm confusion → auth bypass
- AI-assisted analysis → faster triage

---

## Red Team Toolkit

Tools actively deployed in engagements — verified and operational.

### Exploitation Frameworks

| Tool | Version | Use Case | Status |
|------|---------|----------|--------|
| **Metasploit (`msfconsole`)** | 6.x | Exploitation, post-exploitation, privilege escalation, C2 | Active |
| **BeEF (`beef-xss`)** | 0.6.0.0 | Browser exploitation, hook injection, client-side command execution | Active |

**Metasploit modules:** Auxiliary, Exploit, Post, Payload, Encoder, NOP — full exploitation chain: search → use → set → exploit.

**BeEF features:** Hook.js injection, command queue, REST API, WebSocket (port 3000/61985), browser-side RCE.

### AI Security / LLM Red Team

| Tool | Version | Use Case | Status |
|------|---------|----------|--------|
| **Garak** | 0.17.0 | LLM vulnerability scanning — prompt injection, jailbreaks, data extraction | Active |

**Garak probes:** 100+ plugins across 15+ categories — prompt_injection, hallucination, encoding, overrefusal, encoding, etc.

**Run:** `python -m garak -t probes --target <model>`

### Methodology: Tool → Finding → Evidence

```
Recon (auto-workflow.sh) → Discovery (manual mapping) → Exploitation (msfconsole/BeEF)
→ WAF Bypass (Burp extensions) → Validation (Garak for AI/LLM) → Reporting (CWE/OWASP mapped) → Evidence Package
```

All findings delivered as structured evidence: root cause, sanitized reproduction, PoC, impact, remediation.

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
P2 Critical (CVSS ~9.8). Stored XSS in calculator API endpoint persisted in database, executed on admin pages, enabling full admin account takeover. What I keep from this one: my proof needed admin-panel access I wouldn’t force — ethics stays higher than any single report, so I stopped at the line and demoed safely instead (video PoC, 27 files, local sample). It still closed, and support replies left me waiting days between templates — that waiting is what made me stop working HackerOne. The mediation gate (3 valids in 3 months) can be impossible new. So to new researchers: don’t rage past lines that can hurt you — document clean, stack valids, walk standing.

### [Case Study 9 — Credential Leakage & Persistent MITM (Cluster-API JWT)](case-studies/CASE-STUDY-9-SSRF-CLUSTER-API-JWT.md)
Authenticated admin "test cluster API connectivity" flow accepted an arbitrary base URL and issued a server-side request carrying a freshly minted cluster-API bearer JWT — no allowlist, no egress control. Exfiltrated the token to Burp Collaborator (7× interaction), then proved the successful probe **persisted** the attacker URL into core config as a standing MITM, and replayed the JWT against cluster-api for a 200. A second pass proved it also fires on a cookie-only cross-site GET with no CSRF token. **Triage:** Closed as Duplicate (P5 / Informational) of a same-endpoint report filed seven months earlier — accepted, not contested. The write-up is about knowing when *not* to file.

### [Case Study 10 — Unauthenticated Cross-Tenant Cache Write (permitAll + Shipped-Default Key)](case-studies/CASE-STUDY-10-CROSS-TENANT-CACHE-WRITE.md)
Two defensible defaults that compose badly: the Spring Security `permitAll()` list carries `/resetMemoryCache/**` and `/cache/**` in the same bucket as `/js/**`, and the App2App service key ships with a non-empty hardcoded value. Forge the internal-service token → cache-reset endpoint accepts an attacker-chosen `tenantId` with no tenant comparison, reloading another tenant's users, teams and in-memory auth manager. **Triage:** Closed as Duplicate — methodology write-up, and a lesson in reading what the auth layer actually exempts.

### [Case Study 11 — Blind SSRF via the Kafka Connect `env` Parameter](case-studies/CASE-STUDY-11-KAFKA-CONNECT-ENV-SSRF.md)
`cluster-api` splits a caller-controlled `env` on commas, prepends `http://` and fires a `RestTemplate` at the result — no host allowlist. Pointing it at an attacker `host:port` makes the *server* send `POST /connectors` there (`User-Agent: Java/…` proves JVM origin). **This one is really about the corrections.** I filed six affected endpoints, an exfiltration claim and `AV:N`. Re-running my own claims found five endpoints never tested, the response body discarded (`processResponseBody` is status-only ⇒ **blind**), a 400 I'd misread as a path problem when it was a missing required parameter, and an actor model that didn't hold. I wrote the correction myself rather than let an analyst find them. **Triage:** Duplicate, P3 — put the corrections on the record instead of spending a slot contesting.

---

## What Recruiters Say

> "I built a full critical-bypass package, then proved to myself the endpoint was an SPA catch-all that returned the same response for any token — so I withdrew it. Verifying your own findings, even painful ones, is what makes a report trustworthy."

---

## How to Use This Portfolio

| Role Type | Attach |
|-----------|--------|
| Red Team / Offensive | Case Study 1 (ATO), Case Study 8 (Stored XSS → Admin ATO), Case Study 9 (Credential Leakage & Persistent MITM), Red Team Toolkit (Metasploit, BeEF, Garak) |
| AppSec / Source Review | Case Study 9 (JWT exfil + persist), Case Study 10 (permitAll + hardcoded key, cross-tenant), Case Study 11 (SSRF sink + self-verification) |
| AI Security | Case Study 1 + Garak prompt injection testing |
| Web AppSec / Pentest | Case Study 2 (Search Injection), Case Study 7 (Drupal/JSON:API), Case Study 11 (SSRF) |

**Rule:** CV + max 2 redacted PDFs, matched to role. Never attach raw evidence or program-named files.

---

*All findings are sanitized. Platform/program names removed. Built from Intigriti + Bugcrowd engagements, Jun–Aug 2026.*
