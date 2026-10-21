# Red Team Operations

**Positioning:** web application and identity attack expertise, applied to the red-team
problem. Modern environments are identity-driven — the majority of modern intrusions still
start with a credential, a token, or a session, which is exactly the surface I have spent
two years testing against real programs and real triagers.

**Honesty note on this section.** Everything under *Demonstrated* is backed by a case study
in this repo, written from a real engagement and reviewed by a real analyst. The *Capability*
table below is a statement of what I can do and have set up — **not** a claim of engagement
history. I would rather show the path than dress up a lab as an operation. Each line marked
**[build]** is work I have not yet produced evidence for; the honest move is to run it, write
it up, and let the write-up stand or fall on its own.

---

## Demonstrated — real engagements, real triage

| Capability | Evidence |
|------------|----------|
| **Account takeover chains** | CS1 — OIDC + GraphQL identity stack, session → email change → recovery → ATO |
| **Credential leakage & persistence** | CS9 — server-side JWT exfiltration, plus proof the attacker URL persisted in core config as a standing MITM |
| **Authorization bypass** | CS2 — client-supplied scope id selected the global index (5,940 vs 5 results) |
| **Client-side / browser-origin** | CS6 `postMessage` wildcard origin → session token leak; CS8 stored XSS → admin ATO |
| **API & GraphQL security** | CS11 — unauthenticated `env` parameter → server-side request to attacker host, confirmed by listener capture |
| **White-box source review** | CS9, CS10, CS11 — read the authorization layer, then proved it against the running product |
| **Self-correction** | CS8, CS9, CS11 — three reports where re-running my own evidence contradicted what I had written, and I published the correction instead of letting an analyst find it |

---

## Capability — set up and able to demonstrate

| Domain | What I bring | Status |
|--------|--------------|--------|
| **Identity & AD** | Kerberos protocol analysis (tickets, SPNs, delegation types, service-account abuse), LDAP enumeration, AD object/ACL analysis, BloodHound path reasoning, trust relationships, group/permission abuse | [build] |
| **Privilege escalation** | Windows (token/permission abuse, service misconfig, unquoted paths, AlwaysInstallElevated), Linux (SUID, capabilities, cron, PATH hijack, writable service files) | [build] |
| **Lateral movement** | Remote services, credential reuse and relay (NTLM, AD CS abuse), pass-the-hash/ticket, pivoting | [build] |
| **Cloud & identity-provider** | AWS IAM abuse (privilege escalation paths, credential exposure, trust policies), Azure/Entra ID (app registration, OAuth consent abuse, managed identity exposure, Conditional Access bypass research), GCP IAM | [build] |
| **C2 & post-exploitation** | Payload development and staging, C2 infrastructure and beaconing, proxy/tunnel pivoting, persistence mechanisms across Windows and Linux, OPSEC considerations | [build] |
| **Phishing & initial access** | Payload development, credential-harvest infrastructure, browser tradecraft, QR and HTML smuggling | [build] |
| **Detection evasion** | The WAF-evasion work in this repo is the same discipline applied to EDR: TLS and HTTP fingerprinting, request shaping, living-off-the-land execution choices, and a requirement that every evasion claim comes with a **control test** proving the pre-evasion path is actually blocked | partial (web tier) |
| **Adversary emulation & ATT&CK** | Technique-by-technique emulation planning, mapping findings to ATT&CK, detection-gap analysis — with the same discipline as my report corrections: a mapping claim is only worth what its evidence shows | [build] |
| **OSINT & physical-world tradecraft** | Cell-tower geolocation (MCC/MNC/LAC/Cell-ID triangulation, carrier mapping), phone/username/email OSINT, domain and DNS recon | demonstrated (see [`evidence/`](evidence/)) |

---

## The differentiator

Most red-team candidates arrive from HTB, TryHackMe and OSCP labs. I arrive from:

**real targets → real findings → real triage → real business impact.**

That means I already know what a program's triage team considers a vulnerability, what makes
them close a report, and what makes them pay. I know how to write the reproduction, how to
scope a claim honestly, and — the part that matters most to a red team lead — **how to
invalidate my own assumption before it costs the engagement**. A false positive in a red team
engagement does not just waste an analyst's day; it burns the client's trust in the whole
assessment.

---

## What I am building toward

The honest gap: my evidence is concentrated in web and identity attack surface. The
**[build]** items above are the shortest path to closing it, and I am working through them
in order of what a red team manager asks first:

1. **AD attack-path analysis** — build the lab, produce a BloodHound-backed path, write it up
2. **Cloud IAM abuse** — AWS and Azure privilege-escalation chains with reproducible proof
3. **C2 + lateral movement** — a full post-exploitation chain, not a framework screenshot
4. **Adversary emulation** — a detection-gap report against a realistic identity environment

Each one will be a case study in this repo with the same standard as the others: root cause,
sanitised reproduction, evidence, and an honest statement of what I got wrong.

---

*Sanitized — no client or program names, no real infrastructure. Full technical reports
available on request under NDA.*
