# Red Team Toolkit

**This repo is about findings, not inventory.** The tools below are incidental — they cover
the repetitive surface work so the time goes into analysis and proof. Where a tool appears in
a case study it was in service of a specific hypothesis, and the result is what is written up.

## What the automation covers

| Area | Purpose |
|------|---------|
| **Recon pipeline** | Subdomain enumeration, DNS resolution, HTTP probing, directory scanning, URL crawling, service fingerprinting, technology detection, JS analysis |
| **Multi-engine dorking** | Search engines, GitHub code search, certificate-transparency logs, URL-scan and archive services, cloud-bucket and subdomain discovery |
| **PoC generators** | CSRF chain generation with ready-to-serve HTML and cURL, search-query injection against Lucene/Elasticsearch backends, GraphQL authorization testing, JWT algorithm-confusion testing, Cloudflare Turnstile implementation review |
| **Mobile & wireless** | Traffic capture and interception, radio reconnaissance, runtime instrumentation of app traffic |

Scripts are published separately at
[github.com/Rahadrab/SEC-SCRIPTS](https://github.com/Rahadrab/SEC-SCRIPTS).

## Why there is no tool table here

A list of installed tools tells a hiring manager nothing they can act on — they cannot tell
whether the person holding it found a real vulnerability. The findings in
[`case-studies/`](../case-studies/) are the evidence: what was wrong, how it was proven, what
the consequence was, and — in three cases — what I got wrong and corrected myself.

Each case study names the tools it needed at the point of use.

---

*Sanitized — organisation and endpoint names removed. Full technical reports available on
request under NDA.*
