# PORTFOLIO-KIT — Evidence-Backed Job Materials (Sep 2026)

Built from a full review of the real work archive (Intigriti + Bugcrowd engagements, Jun–Aug 2026). This kit turns packaged findings into application-ready material.

**Layout rule: everything at the top level of this folder is PUBLIC-SAFE (sanitized, no program names) and is exactly what gets uploaded to a new GitHub repo. The `Private-no-upload/` folder contains program-named versions — NEVER upload it anywhere.**

## Contents (top level — safe to publish)

| File | What it is |
|---|---|
| `PORTFOLIO-INVENTORY.csv` | Public-safe inventory: publishable artifact → finding type → roles it supports (no program names) |
| `case-studies/CASE-STUDY-1-ATO-MISSING-REAUTH.md` | Sanitized: account takeover via missing re-auth on credential change (High, packaged) |
| `case-studies/CASE-STUDY-2-SEARCH-INJECTION-ACL-BYPASS.md` | Sanitized: search-query injection → ACL bypass + user enumeration (health data, GDPR) |
| `case-studies/CASE-STUDY-3-KNOWN-CVE-EOL-TRAVERSAL.md` | Sanitized: CVE-2021-43264 verification on an EOL ePortfolio (arbitrary file read) |
| `CV-BULLETS-HEADLINE.md` | Updated CV bullet block, LinkedIn headline v3, About, PoC attachment map, interview one-liner |
| `UNFINISHED-LEADS.md` | Public methodology roadmap of active research leads (no target names) |

## Private-no-upload/ (never upload)

- `Private-no-upload/PORTFOLIO-INVENTORY.csv` — full 20-artifact inventory: real program names, a live Intigriti report ID, real hostnames, internal local file paths.
- `Private-no-upload/UNFINISHED-LEADS.md` — target-named roadmap with in-scope endpoints and recon intel.
- `Private-no-upload/DO-NOT-UPLOAD.txt` — read this if you ever select-all this folder.

**When creating the new GitHub repo, upload ONLY these top-level items:** `README.md` · `PORTFOLIO-INVENTORY.csv` · `UNFINISHED-LEADS.md` · `CV-BULLETS-HEADLINE.md` · `LICENSE` · `case-studies/` (3 files).

**Never upload:** `Private-no-upload/` (whole folder).

## Where these fit the campaign (from MASTER-PLAN)

- **Message A/D/F** in `OUTREACH-KIT.md` get their proof from Case Studies 1–3 (attach max 2, redacted).
- **The JWT false-positive story** (in `CV-BULLETS-HEADLINE.md` §5) is interview-gold for triage/analyst roles.
- **Case studies double as GitHub/LinkedIn Featured content** — platform-agnostic, safe to publish.
- **Active leads 1–2** in the public roadmap can upgrade the portfolio to a Critical-tier line if finished; timebox them so the 15-apps/week engine never stalls.

## Safety (non-negotiable, from SKILLS-ASSESSMENT checklist)

- All top-level case studies and docs are sanitized and safe to publish as-is.
- Never upload `Private-no-upload/` — program names, report IDs, and hostnames must not leave the machine.
- Never attach raw session JSONs, personal credential/account files, or program-named files.
- Public claim stays at: "2+ years bug bounty, accepted P2–Critical, verifiable on Intigriti/Bugcrowd profiles."