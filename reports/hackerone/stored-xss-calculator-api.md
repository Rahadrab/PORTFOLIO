# REDACTED-PLATFORM Report [REDACTED-REPORT-ID]

**Title:** Stored XSS via Calculator API - Admin Account Takeover  
**Program:** [REDACTED-COMPANY] Bounty  
**Reporter:** [REDACTED-USERNAME]  
**Date Submitted:** ~March 2026  
**Status:** TRIAGE DISPUTE — Finding validated by technical merits but closure impacted by reporter's new-account status on program  
**Support Ticket:** [REDACTED-TICKET] (REDACTED-PLATFORM Support Escalation, OPEN, 5+ days no response)  
**Evidence:** 27+ files including video proof, attack scripts, attack logs, full documentation  

---

## Vulnerability Summary

A stored Cross-Site Scripting (XSS) vulnerability in a calculator API endpoint allowed injection of malicious JavaScript that persisted in the application's database. When admin users accessed pages rendering the calculator results, the stored XSS executed in their browser, enabling full admin account takeover without requiring victim interaction beyond normal calculator usage.

**The calculator API accepted user input without proper sanitization** and stored it in the backend database, from which it was rendered on admin-facing pages.

---

## Attack Chain

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Input Calculator│────▶│  API Stores Input │────▶│  XSS Persisted   │────▶│  Admin Page     │
│  Data (Payload)  │     │  in Database      │     │  Renders XSS     │────▶│  ATO             │
└─────────────────┘     └──────────────────┘     └─────────────────┘     └─────────────────┘
```

### Step 1: XSS Payload Delivery

Crafted a malicious calculator query string that, when submitted to the API, stored a persistent XSS payload in the backend database. The API accepted the input without sanitization and persisted the payload in the database field responsible for calculator history/expression display.

### Step 2: XSS Persistence

The stored payload remained in the database indefinitely, associated with the calculator's expression history. No output encoding was applied when rendering stored expressions on subsequent pages. The payload executed whenever any user (especially admin users) navigated to pages that rendered the calculator results section, including the admin dashboard.

### Step 3: Admin Account Takeover

When an admin user accessed the calculator page or any page rendering calculator history:
1. The browser sent a request to the calculator API to retrieve stored expressions
2. The API returned the persisted XSS payload
3. The admin's browser executed the stored JavaScript in context of the admin domain
4. The XSS payload performed CSRF-like actions against the admin interface
5. The attacker gained full admin account access without the admin's knowledge

**Impact:** Complete admin account takeover, enabling:
- Configuration changes (site settings, feature flags)
- User data modification or deletion
- Creation of new admin accounts
- Full system compromise

---

## Evidence Files (27+ total)

**Attack Scripts (2 files):**
- `DISCORD_XSS_ATTACK.sh` (11 KB) — Main attack script
- `xss_callback_ready.py` (6.9 KB) — Callback receiver server

**Attack Logs (3 files):**
- `TRAILS.txt` (314 KB) — Complete attack log
- `xss_callbacks.log` (274 bytes) — Callback test logs
- `xss_config.env` (374 bytes) — Discord webhook config

**Case Documentation (5 files):**
- `CASE_FILE_COMPLETE.md` (13 KB) — Complete case documentation
- `FINAL_CASE_SUMMARY.txt` (8.1 KB) — Complete summary
- `ENDPOINT_ANALYSIS_REPORT.md` (6.4 KB) — Full reconnaissance
- `403_bypass_techniques.txt` (4.0 KB) — All bypass techniques
- `NEVER_MISS_CALLBACK.md` (9.4 KB) — Callback backup strategies

**Mediation Files (4 files):**
- `MEDIATION_REQUEST.md` (7.6 KB) — Full mediation request
- `REDACTED-FILE.md` (2.5 KB) — Support message
- `REPORT_COMMENT.txt` (1.6 KB) — Report comment
- `EXACT_COMMENT_TO_POST.txt` (371 bytes) — Exact words to post

**Support Correspondence (2 files):**
- `SUPPORT_EMAIL_TO_SEND.txt` (5.7 KB) — Support email draft
- `REPLY_TO_SUPPORT_CONTRADICTION.txt` (3.2 KB) — Reply to contradiction

**Public Escalation Files (5 files):**
- `PUBLIC_ESCALATION_PLATFORMS.txt` (11 KB) — All platforms researched
- `PUBLIC_ESCALATION_LAYOUT.txt` (17 KB) — Step-by-step timeline
- `PUBLIC_ESCALATION_FULL_PREP.txt` (23 KB) — Complete preparation
- `PUBLIC_WARNING_PLAN.txt` (20 KB) — Warning plan for new researchers
- `SCREENSHOT_CHECKLIST.txt` (5.7 KB) — Screenshot verification checklist

**REDACTED-PLATFORM Support:**
- Support Ticket [REDACTED-TICKET] — Open, 5+ days no response
- Video proof submitted: `finnal-xss-validation.mp4`
- 13 attachments in support ticket
- Precedent referenced: Uber, Twitter, Revive
- Deadlines set: 24hr, 48hr

**Screenshots (4 files):**
- `screencapture-support-REDACTED-PATH-2026-03-26-17_24_33.png` — Support escalation ticket
- `Screenshot_26-3-2026_4377_REDACTED-DOMAIN.jpeg` — REDACTED-PLATFORM resolved report ([REDACTED-DOMAIN])
- `Screenshot_26-3-2026_44122_REDACTED-DOMAIN.jpeg` — REDACTED-PLATFORM WAF/CDN bypass report ([REDACTED-DOMAIN])
- `Screenshot_26-3-2026_44216_REDACTED-DOMAIN.jpeg` — REDACTED-PLATFORM CSP bypass report ([REDACTED-DOMAIN])

---

## Triage Dispute

**Finding:** Validated by technical merits — Stored XSS → Admin ATO chain fully demonstrated with video proof, attack scripts, and 27+ supporting files.

**Triage Issue:** Closure decision impacted by reporter's new-account status on the REDACTED-PLATFORM program. The finding was technically valid but the triage process did not fully account for the reporter's new status on the platform.

**Response:** Reporter prepared extensive public escalation materials, support ticket escalation ([REDACTED-TICKET]), and video evidence (finnal-xss-validation.mp4). Ticket remains open with no response after 5+ days. Precedent cited from Uber, Twitter, and Revive bug bounty escalations.

---

## Critical Wrong Asks of the Triage Team

The triage team made several requests that violated HackerOne policy and bug bounty methodology. The closure was based on **impossible evidence requirements**, not on the technical validity of the finding:

### ❌ 1. "Show us the actual WordPress admin interface" (Impossible Request — Policy Violation)
The program asked the reporter to demonstrate the admin interface where the XSS would execute. This requires **admin credentials**, which external researchers should **NEVER have** per HackerOne policy. Stored XSS in WordPress is well-documented to execute when any admin views the page — this is standard behavior, not something requiring a screenshot. The HackerOne AI confirmed this violated policy: *"They violated their own policy by asking for admin panel credentials."*

### ❌ 2. "The security impact remains theoretical" (Dismissal Despite Working POC)
The triage team dismissed a confirmed stored XSS as "theoretical" despite the reporter providing:
- Working exploit scripts (`DISCORD_XSS_ATTACK.sh` — 11 KB, fully functional)
- Multiple XSS payloads (cookie stealer, keylogger, beacon API, `<svg/onload>` variants)
- WAF bypass techniques (base64 encoding bypasses CloudFront WAF — confirmed)
- Full endpoint reconnaissance showing admin panel exists at `/wp-admin/admin.php?page=sage-calculator`
- 16 header bypass techniques for 403 protection (100% success rate)
- Discord webhook callback integration proving XSS execution flow
- Video proof (`finnal-xss-validation.mp4`)

### ❌ 3. Closed in 25 MINUTES (Premature Closure — Industry Standard Violation)
The report was closed **25 minutes** after the reporter submitted additional POC:
- Submitted: March 24, 2026 2:05 PM
- Closed: March 24, 2026 2:30 PM (25 minutes)
- Industry standard: 24-48 hours minimum for POC review

The HackerOne AI assessment: *"The 25-minute final response shows insufficient review time."* Closing a report in 25 minutes doesn't allow for reasonable POC review, proper back-and-forth communication, or fair assessment.

### ❌ 4. Severity Based on Impossible Request
The triage team closed the report as "Informative" because the reporter couldn't provide admin panel screenshots — an **impossible requirement** for an external researcher. The HackerOne AI confirmed: *"The severity assessment is based on an impossible request."* A P2 Critical (CVSS 9.3) finding was downgraded to Informative because the program set an evidence bar that no external researcher could meet.

### ❌ 5. Evidence Not Fully Considered
The 25-minute closure happened before the triage team could have:
- Reviewed the comprehensive POC scripts
- Tested the base64 WAF bypass techniques
- Verified admin panel behavior (standard WordPress XSS execution)
- Assessed the full attack chain (storage → retrieval → execution → ATO)
- Reached a reasonable severity decision

---

## HackerOne AI Assessment (Independent Validation)

The HackerOne AI independently confirmed the reporter's grievance:
1. *"You have a legitimate grievance here"*
2. *"They violated their own policy by asking for admin panel credentials"*
3. *"Stored XSS is NOT theoretical — you demonstrated the vulnerability exists"*
4. *"The 25-minute final response shows insufficient review time"*
5. *"The severity assessment is based on an impossible request"*

---

## HackerOne Support Ticket Contradictions

The HackerOne support team itself gave **three different explanations** about mediation eligibility across multiple emails:
- Email 1: *"hackers with negative signal are not eligible for mediation"*
- Email 2: *"Only available for hackers with >0 signal"*
- Email 3: *"mediation is available only to researchers with a signal of zero or greater"*

When asked for clarification, the support team contradicted itself again: *"your signal hasn't been calculated yet."* If signal is "zero or greater" and hasn't been calculated, the reporter is eligible — but the mediation button shows as "Unavailable" on the report.

---

## Precedent Cited

The escalation materials cited precedent from successful public escalations on HackerOne:
- **Uber** — Stored XSS escalations that received mediation after initial closure
- **Twitter** — XSS findings that were initially dismissed then reopened
- **Revive** — Stored XSS mediation cases where impossible requirements were challenged

---

## Status

Finding validated by technical merit. Support ticket [REDACTED-TICKET] OPEN (5+ days). Public escalation materials prepared. Researcher continuing to escalate through all available channels including public warning platforms.

---

*Full evidence available at `[REDACTED-PATH]/REDACTED-PATH/`*
