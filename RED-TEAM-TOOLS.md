# Red Team Capability

Organised by what I can be brought in to do — not by what is installed on my box.
Tooling is incidental to the results; the substance is always the code path, the reachable
trust boundary, and whether the consequence is provable.

## Offensive Security

| Area | What it covers |
|------|----------------|
| **Exploitation & post-exploitation** | Turning a proven weakness into a working path: privilege escalation, persistence, credential access, lateral movement |
| **Client-side & browser-origin attacks** | XSS chains executing in an authenticated context, `postMessage`/origin flaws, token leakage into third-party frames, session and cookie theft |
| **Network & wireless** | Traffic capture and analysis, radio reconnaissance, interception of mobile location APIs, runtime instrumentation of app traffic |
| **AI / LLM security** | Adversarial testing of a model's instruction hierarchy — prompt injection, jailbreak, data extraction — and proving whether a mitigation actually works |

## Application & Identity Security

| Area | What it covers |
|------|----------------|
| **Authentication** | OIDC, OAuth 2.0, SAML 2.0, session management, MFA flows, re-authentication gaps, account takeover chains |
| **Authorization** | Role and scope enforcement, tenant isolation, IDOR, object-level and field-level access control, privilege-escalation chains |
| **API security** | REST and GraphQL surfaces, schema introspection, batching/depth abuse, mass assignment, rate-limit and business-logic flaws |
| **Source code review** | White-box analysis of OSS projects — dangerous defaults, hardcoded secrets, broken conditionals, deserialization, framework-specific misconfiguration (Spring Security, Liferay, Apache) |
| **Cloud & infrastructure** | Object storage exposure, SSRF into cloud metadata, IAM misconfiguration, CI/CD secret exposure |

## What I actually do about it

- Read the code when the black-box signal is ambiguous, and prove the conclusion against the
  running product.
- Build the exploit chain to the point where the impact is demonstrable, then stop at the
  line the program or the target draws.
- Deliver a structured evidence package: root cause, sanitised reproduction, PoC, impact,
  remediation.
- **Verify my own claims.** Several of my reports ended with a correction I wrote myself
  because re-running my own evidence contradicted what I had written. That discipline is the
  part I would bring to a team.

## Evidence

Case studies with root cause, reproduction and outcomes: [`case-studies/`](case-studies/).

*Sanitized — organisation and endpoint names removed. Full technical reports available on
request under NDA.*
