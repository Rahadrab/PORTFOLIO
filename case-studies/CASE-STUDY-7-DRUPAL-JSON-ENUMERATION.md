# Case Study 7 — Drupal Installer + JSON:API User Enumeration (VDP Program)

| Field | Value |
|-------|-------|
| **Severity** | P3 → P2 (upgraded during triage) |
| **CWE** | CWE-200 (Exposure of Sensitive Information to Unauthorized Actor) / CWE-702 (Use of Dangerously Permissive Character Encoding) |
| **Impact** | Admin portal exposure enabling brute-force credential attacks; user enumeration enabling reconnaissance, credential stuffing, and privilege escalation; reputational and financial business impact |
| **Surface** | Public-facing Web Application (VDP program); [REDACTED-DOMAIN] |
| **Status** | RESOLVED (13 Nov 2025) — Customer restricted leaked information; VDP program (no monetary reward but finding accepted and triaged) |

---

## Vulnerability 1: Drupal Installer Exposure

**URL:** `https://[REDACTED-DOMAIN]/core/install.php`  
**Type:** Administrative portal exposure / Misconfiguration  
**VRT:** Other (initially P3, upgraded to P2 during triage)  

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
cf-cache-status: DYNAMIC
server: cloudflare
cf-ray: 99606b47a9868321-SIN

HTTP/2 200
date: Wed, 29 Oct 2025 05:54:36 GMT
content-type: text/html; charset=UTF-8
cache-control: must-revalidate, no-cache, private
expires: Sun, 19 Nov 1978 05:00:00 GMT
last-modified: Wed, 29 Oct 2025 05:54:36 +0000
cf-cache-status: DYNAMIC
server: cloudflare
cf-ray: 99606b4d2dca8321-SIN
```

**Proof Images:**
- `image-2025-10-29T05:56:52.454Z.png` — Installer visible
- `installer%20visible.png` — Admin portal access

---

## Vulnerability 2: JSON:API User Enumeration

**URL:** `https://[REDACTED-DOMAIN]/jsonapi/user/user`  
**Type:** Sensitive Data Exposure → User Enumeration  
**VRT:** Sensitive Data Exposure → User Enumeration  
**Priority:** P2 (upgraded from P3 during triage)  

**Description:**
Unauthenticated access to the `/jsonapi/user/user` endpoint allows an attacker to enumerate valid user accounts.

**Impact:**
- **Reconnaissance for targeted attacks:** Attackers can identify usernames, IDs, or roles to craft phishing or credential-stuffing campaigns
- **Privilege escalation attempts:** Knowing which accounts exist (especially admin or staff accounts) helps attackers focus brute-force or password spraying
- **Information disclosure:** Even seemingly harmless metadata (user IDs, display names, timestamps) can be leveraged to map the application's structure and relationships

**Screenshots:**
- `user%20names%20visible%20with%20id.png` (40.6 KB) — User names visible with IDs

---

## Approach — Two Findings in One Submission

### Finding 1: Drupal Installer Exposure

1. **Discovery:** Navigated to `https://[REDACTED-DOMAIN]/core/install.php` in a standard browser
2. **Confirmation:** HTTP 200 response with Drupal installer interface exposed, no authentication required
3. **Risk:** Public access to admin portal enables brute-force credential attacks and potential admin-level backend access

### Finding 2: JSON:API User Enumeration

1. **Discovery:** Navigated to `https://[REDACTED-DOMAIN]/jsonapi/user/user` in a standard browser
2. **Confirmation:** Unauthenticated JSON response returned valid user accounts and IDs
3. **Risk:** User enumeration enables reconnaissance for targeted attacks, credential stuffing, and privilege escalation

### Triaging Process

- **29 Oct 2025:** [REDACTED-USERNAME] submitted the finding via [REDACTED-PLATFORM]
- **01 Nov 2025:** [REDACTED-ANALYST] changed severity from P3 to P2 during triage
- **01 Nov 2025:** Finding marked as Triaged; customer team to have closer look
- **13 Nov 2025:** [REDACTED-ANALYST] resolved the finding; team restricted the leaked information
- **Status:** RESOLVED

---

## Results

**Vulnerability 1 — Drupal Installer Exposure:**
- Public access to `/core/install.php` confirmed with HTTP 200 response
- Admin interface accessible without authentication
- Potential for brute-force credential attacks and admin-level backend access
- **Severity:** P3 → P2 during triage

**Vulnerability 2 — JSON:API User Enumeration:**
- Unauthenticated access to `/jsonapi/user/user` confirmed
- Valid user accounts and IDs leaked in JSON response
- Enables reconnaissance, credential stuffing, and privilege escalation
- **Severity:** P2 (upgraded from P3 during triage)

**Combined Business Impact:**
- Potential financial loss from admin portal modification/deletion
- Reputational damage from lost user trust
- Reconnaissance enablement for targeted attacks
- User data exposure mapping

**VDP Outcome:** Customer restricted the leaked information; finding triaged and closed; VDP program provided feedback; reporter welcomed to program.

---

## Remediation Delivered

**Vulnerability 1 — Drupal Installer:**
- Admin portal access restricted (authentication now required)
- `/core/install.php` endpoint hardened or removed
- Production site configuration verified to prevent re-installation

**Vulnerability 2 — JSON:API User Enumeration:**
- `/jsonapi/user/user` endpoint restricted to authenticated users only
- User enumeration now requires valid session/credentials
- User data exposure minimized; only role-appropriate data accessible

**VDP Outcome:** The team was able to restrict the leaked information. Status changed to RESOLVED on 13 Nov 2025.

---

## Tooling

curl commands for endpoint testing, response-body capture, screenshot capture — developed through manual verification. Verified both endpoints' access levels before and after remediation. [REDACTED-PLATFORM] submission format followed for triage.

---

*Sanitized: target website name fully removed ([REDACTED-DOMAIN] context maintained for VDP program recognition). Full technical report and screenshots available in original [REDACTED-PLATFORM] submission (submitted 29 Oct 2025). Report acknowledged by [REDACTED-ANALYST] (13 Nov 2025): "Welcome to the program! Thanks for this finding — the team was able to restrict the leaked information." VDP program: no monetary reward but finding accepted and triaged.*

