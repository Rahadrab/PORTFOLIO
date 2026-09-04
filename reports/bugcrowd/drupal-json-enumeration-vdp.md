# REDACTED-PLATFORM Submission — [REDACTED-DOMAIN] ([REDACTED-COMPANY] VDP)

**Target:** [REDACTED-DOMAIN]  
**Program:** [REDACTED-COMPANY] VDP (REDACTED-PLATFORM)  
**Reporter:** [REDACTED-USERNAME]  
**Submitted:** 29 Oct 2025, 06:20:49 GMT+0  
**VRT:** Sensitive Data Exposure → User Enumeration (JSON:API); Other (Drupal Installer)  
**Priority:** P2 (initially P3, then upgraded to P2)  
**Status:** RESOLVED  
**Closed:** 13 Nov 2025  
**Reward:** VDP programs do not offer monetary rewards  

---

## Vulnerability 1: Drupal Installer Exposure

**URL:** `https://[REDACTED-DOMAIN]/core/install.php`  
**Type:** Administrative portal exposure / Misconfiguration  
**VRT:** Other (initially), then severity upgraded to P2  

**Description:**
Administrative portals for an application allow Admins to log in and modify how the application runs and the content it serves. This can include adding, removing, updating, or creating new content, account provisioning, data manipulation, and other configuration changes.

**Issue:**
An attacker is able to identify an exposed portal and can then brute force credentials. If they successfully log in, they can access the backend interface and carry out activities within it with Admin privileges.

**Business Impact:**
- Exposed portals can lead to indirect financial loss due to the attacker's ability to modify, remove or create data within the admin portal
- Can also cause reputational damage for the business due to a loss in confidence and trust by users

**Steps to Reproduce:**
1. Use a browser to navigate to the portal via the URL: `https://[REDACTED-DOMAIN]/core/install.php`
2. The following are the functionalities of the admin portal:
   - **Drupal Installer (P3):** Public access to `/core/install.php` exposes an administrative interface. If protections fail, this could allow re‑installation or misconfiguration of the production site.

**HTTP Response (as captured):**
```
HTTP/2 301
date: Wed, 29 Oct 2025 05:54:35 GMT
content-type: text/html
location: https://[REDACTED-DOMAIN]/core/install.php
traceresponse: 00-1872e08725c5aebc7b791cb356c8dd2b-8ef1aed73912aef9-01
cf-cache-status: DYNAMIC
server: cloudflare
cf-ray: 99606b47a9868321-SIN

HTTP/2 200
date: Wed, 29 Oct 2025 05:54:36 GMT
content-type: text/html; charset=UTF-8
cache-control: must-revalidate, no-cache, private
expires: Sun, 19 Nov 1978 05:00:00 GMT
last-modified: Wed, 29 Oct 2025 05:54:36 +0000
traceresponse: 00-1872e087510c6798425b14e14e6ae9b8-deae80eefea3d762-01
cf-cache-status: DYNAMIC
server: cloudflare
cf-ray: 99606b4d2dca8321-SIN
```

**Proof Images:**
- `image-2025-10-29T05:56:52.454Z.png` (39.1 KB) — Installer visible
- `installer%20visible.png` (35.1 KB) — Admin portal access
- `user%20names%20visible%20with%20id.png` (40.6 KB) — JSON:API user enumeration

---

## Vulnerability 2: JSON:API User Enumeration

**URL:** `https://[REDACTED-DOMAIN]/jsonapi/user/user`  
**Type:** Sensitive Data Exposure → User Enumeration  
**VRT:** Sensitive Data Exposure → User Enumeration  
**Priority:** P2 (upgraded from P3)  

**Description:**
Unauthenticated access to the `/jsonapi/user/user` endpoint allows an attacker to enumerate valid user accounts.

**Impact:**
- **Reconnaissance for targeted attacks:** Attackers can identify usernames, IDs, or roles to craft phishing or credential-stuffing campaigns
- **Privilege escalation attempts:** Knowing which accounts exist (especially admin or staff accounts) helps attackers focus brute-force or password spraying
- **Information disclosure:** Even seemingly harmless metadata (user IDs, display names, timestamps) can be leveraged to map the application's structure and relationships

**Screenshots:**
- `user%20names%20visible%20with%20id.png` (40.6 KB) — User names visible with IDs

---

## Triaging & Resolution Timeline

**29 Oct 2025 06:20:49 GMT+0** — [REDACTED-USERNAME] submitted the finding  
**29 Oct 2025 21:55:45 GMT+0** — Tal_REDACTED-PLATFORM created a blocker to respond to comments  
**29 Oct 2025 21:55:46 GMT+0** — REDACTED-PLATFORM Staff sent private message to Customer  
**30 Oct 2025 22:42:35 GMT+0** — Customer sent private message to REDACTED-PLATFORM Staff  
**01 Nov 2025 17:39:23 GMT+0** — Tal_REDACTED-PLATFORM changed severity to P2  
**01 Nov 2025 17:39:24 GMT+0** — Tal_REDACTED-PLATFORM changed state to Triaged  
**01 Nov 2025 17:39:42 GMT+0** — REDACTED-PLATFORM Staff sent private message to Customer  
**01 Nov 2025 17:39:54 GMT+0** — Tal_REDACTED-PLATFORM sent message to [REDACTED-USERNAME]: marked as 'Triaged', customer will have closer look  
**03 Nov 2025 17:30:28 GMT+0** — [REDACTED-USERNAME] sent thank you message  
**06 Nov 2025 13:08:30 GMT+0** — [REDACTED-USERNAME] submitted response request for reward  
**06 Nov 2025 13:10:54 GMT+0** — REDACTED (bugcrowd) replied: VDP programs do not offer monetary rewards; timeline depends on customer  
**06 Nov 2025 13:10:55 GMT+0** — REDACTED marked response request as resolved  
**13 Nov 2025 05:43:50 GMT+0** — [REDACTED-ANALYST] welcomed [REDACTED-USERNAME] to the program, thanked for finding, team restricted leaked information  
**13 Nov 2025 22:49:48 GMT+0** — [REDACTED-ANALYST] changed state to Resolved  

---

## Analyst & Customer Feedback

**Tal_REDACTED-PLATFORM** (01 Nov 2025):
> "Thank you for your patience. We have marked this submission as 'Triaged' so that the customer team can have a closer look at your submission.
> 
> Note that the final severity, and status of this submission are subject to change as this receives a further review from the team working on this program. We appreciate your time, and look forward to more submissions from you in the future!"

**[REDACTED-ANALYST]** (06 Nov 2025):
> "Hi [REDACTED-USERNAME],
> 
> We would like to clarify that VDP programs on REDACTED-PLATFORM do not offer monetary rewards or points for submissions.
> 
> The timeline for the fix to be applied and confirmed entirely depends on the customer, we can't provide you with an estimation in that regard.
> 
> Best regards,
> - [REDACTED-ANALYST]"

**[REDACTED-ANALYST]** (13 Nov 2025):
> "Hi [REDACTED-USERNAME],
> 
> Welcome to the program!
> 
> Thanks for this finding — the team was able to restrict the leaked information."

---

## Resolution

**Status:** RESOLVED (13 Nov 2025)  
**Outcome:** The team was able to restrict the leaked information.  
**Reward:** VDP programs do not offer monetary rewards or points.  
**Lesson:** Even without financial reward, triaged findings provide valuable feedback, program access welcome messages, and portfolio credit.  

---

## Key Takeaways

1. **VDP programs** focus on fixing issues, not payments — acceptance and triage are the rewards
2. **Severity upgrades** can happen during triage (P3 → P2 in this case)
3. **Customer response** matters — [REDACTED-COMPANY] restricted the leaked information and resolved the finding
4. **Portfolio value** — Even non-paid findings add to your bug bounty portfolio and demonstrate responsible disclosure
5. **Two vulnerabilities in one submission** — Drupal Installer + JSON:API User Enumeration showed breadth of testing