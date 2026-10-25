# Rahad Rabbani

**Offensive Security Researcher | Application Security | Red Team**
CEH certified · Bugcrowd Hall of Fame · 2+ years vulnerability research · Bangladesh (UTC+6)

I test authentication, APIs, access control, and identity systems using manual web testing and Java/Python source review. I document reproducible evidence, qualify impact against what was actually demonstrated, and correct my own claims when re-verification requires it.

[Portfolio site](https://rahadrab.github.io/PORTFOLIO/) · [Case studies](case-studies/) · [LinkedIn](https://www.linkedin.com/in/rahadrab/) · [Bugcrowd](https://bugcrowd.com/h/rahad_rab) · [Intigriti](https://app.intigriti.com/researcher/profile/rahadrab/)

## Featured findings

| Finding | Outcome |
|---|---|
| Account takeover through missing re-authentication across OIDC and GraphQL flows | Packaged; researcher-assessed High |
| Cluster-API JWT disclosure and persistent MITM | Reproduced; triaged P5/Informational |
| Blind SSRF through Kafka Connect environment parameter | Validated by triage as P3; scope and impact corrected |
| Cross-tenant cache mutation through authorization defaults | Validated by triage |
| SAML 2.0 assessment across five identity providers and 25+ federation endpoints | Documented controls that held |

## All other findings and research

### Submitted reports

- **Confidential VDP finding — customer confirmed remediation; Resolved.** Recognized in Bugcrowd's Hall of Fame. Technical details are omitted under the program's disclosure policy.
- **Archive path handling in pghoard restore** — validated by triage.
- **Klaw account-directory exposure** — validated by triage.
- **Email-change and account-recovery chain** — Intigriti; Out of Scope/Archived.
- **Client-side analytics write-key exposure** — validated by triage.
- **Wildcard-origin `postMessage` token exposure** — validated by triage.
- **OAuth open redirect in an Auth0 login flow** — validated by triage.
- **Stored XSS behavior in an admin-rendered workflow** — triaged Informative.
- **Public WordPress REST API exposure** — triaged Informative.
- **MySQL migration-tool report** — retracted after re-check; triage independently confirmed the input stayed a single argument and no shell command ran, then closed it as Not Applicable. The report also required control of the migration host's source URI, which already implied host-level code execution.

### Additional case studies and research

- **Search-query injection and scope differential** — Not Applicable; re-verification confirmed returned content was public. [Case study](case-studies/CASE-STUDY-2-SEARCH-INJECTION-ACL-BYPASS.md)
- **CVE-2021-43264 path-traversal validation** on an end-of-life platform — researcher-assessed P3. [Case study](case-studies/CASE-STUDY-3-KNOWN-CVE-EOL-TRAVERSAL.md)
- **WAF and reCAPTCHA v3 bypass testing** across multi-route authentication flows.
- **Authorization-gate Boolean logic flaw** — source analysis; submitted and reviewed by triage. [Case study](case-studies/CASE-STUDY-12-VALIDATEROLE-AUTH-BYPASS.md)
- **Unauthenticated username oracle** — reproduced locally; submitted and reviewed by triage. [Case study](case-studies/CASE-STUDY-13-UNAUTH-USER-ENUMERATION.md)
- **Cross-tenant user-record creation** — source-based report denied by triage as a “trap”; includes a lesson on how sister-site context can complicate an in-scope report. No live request was made. [Case study](case-studies/CASE-STUDY-14-CROSS-TENANT-USER-CREATION.md)
- **JWT authentication-bypass hypothesis** — self-withdrawn after re-verification showed it was not a vulnerability.
- **Local LLM prompt-injection and jailbreak testing** — evaluated attacks and mitigation behavior. [Case study](case-studies/GARAK-LOCAL-HARDENING.md)
- **Time-based SQL injection report** — triage-confirmed false positive caused by unstable VPN timing; an early-career lesson in testing on a stable network. [Write-up](case-studies/CASE-STUDY-TIME-BASED-SQLI-LEARNING.md)

Severity labels are researcher assessments unless a platform outcome is stated. Findings validated by triage are identified as such. Full technical details are available only where disclosure rules permit.

## Focus areas

Authentication and identity (OIDC, OAuth, SAML) · Web and API security · Authorization and tenant isolation · Java/Python source review · SSRF and injection · Evidence-led reporting

## Profiles

- [LinkedIn](https://www.linkedin.com/in/rahadrab/)
- [Intigriti](https://app.intigriti.com/researcher/profile/rahadrab/)
- [Bugcrowd Hall of Fame and researcher profile](https://bugcrowd.com/h/rahad_rab)
- [HackerOne](https://hackerone.com/rahadrab)
- [Portfolio website](https://rahadrab.github.io/PORTFOLIO/)
