# Red Team Toolkit

Tools actively used in engagements. All verified and documented below.

## Exploitation Frameworks

### Metasploit (msfconsole)
- **Version:** 6.x
- **Use:** Exploitation, post-exploitation, privilege escalation, C2
- **Status:** Active — used in Monash engagement testing
- **Modules:** Auxiliary, Exploit, Post, Payload, Encoder, NOP
- **Techniques:** Search, use, set, exploit, run

### BeEF (Browser Exploitation Framework)
- **Version:** 0.6.0.0
- **Use:** Client-side browser exploitation, hook injection, command execution
- **Status:** Installed and tested on Kali
- **Features:** Hook.js injection, command queue, REST API, WebSocket
- **Port:** 3000 (HTTP), 61985 (WebSocket)

## AI Security / LLM Red Team

### Garak
- **Version:** 0.17.0
- **Use:** LLM vulnerability scanning — prompt injection, jailbreaks, data extraction
- **Status:** Installed in ~/ai-redteam (py3.11 venv)
- **Tests:** 100+ probe plugins across 15+ categories
- **Categories:** prompt_injection, hallucination, encoding, overrefusal, etc.
- **Run:** `python -m garak -t probes --target <model>`

## Automation Scripts

| Script | Lines | Purpose |
|--------|-------|---------|
| `auto-workflow.sh` | 500+ | Subdomain enum → port scan → service detection → tech fingerprint → vuln scan |
| `auto-dork.sh` | 1000+ | Multi-engine dorking (Google/Bing/DuckDuckGo/GitHub) |
| `lucene_injection_poc.py` | — | Search injection → ACL bypass |
| `csrf_poc.py` | — | CSRF chain development |
| `graphql_control.py` | — | GraphQL authorization testing |
| `turnstile-bypass.py` | — | Cloudflare Turnstile weakness |
| `jwt_none_runner.py` | — | JWT algorithm confusion |

**All scripts:** [github.com/Rahadrab/SEC-SCRIPTS](https://github.com/Rahadrab/SEC-SCRIPTS)

## Methodology: Tool → Finding → Evidence

```
Recon (auto-workflow.sh) → Discovery (manual mapping) → Exploitation (msfconsole/BeEF)
→ Validation (Garak for AI/LLM) → Reporting (CWE/OWASP mapped) → Evidence Package
```

All findings delivered as structured evidence: root cause, sanitized reproduction, PoC, impact, remediation.
