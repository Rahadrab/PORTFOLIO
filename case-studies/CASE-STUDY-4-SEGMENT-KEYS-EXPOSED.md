# Case Study 4 — Segment API Write Keys Exposed in Client-Side JavaScript

| Field | Value |
|-------|-------|
| **Severity** | P2 |
| **CWE** | CWE-798 (Use of Hard-coded Credentials) / CWE-200 (Exposure of Sensitive Information to Unauthorized Actor) |
| **Impact** | Business metric pollution, fake user profile creation, analytics data integrity compromise |
| **Surface** | Public-facing JavaScript bundles across multiple subdomains of a SaaS platform |
| **Status** | Packaged — full evidence package delivered to triage; report validated and accepted by analyst |

---

## Problem

A SaaS platform's client-side JavaScript bundles contained hardcoded Segment API Write Keys embedded directly in source code across multiple subdomains. These keys were easily extractable and allowed unauthenticated actors to inject fake analytics data, create fake user profiles, and pollute business metrics.

**The keys were left exposed** in bundled JavaScript served to all visitors, requiring no authentication to extract and abuse.

---

## Attack Chain

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Key Extraction │────▶│  API Testing     │────▶│  Data Injection │
│  from JS bundle  │     │  via /v1/        │     │  / Analytics    │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

### Step 1: Key Extraction

Segment Write Keys were discovered in main JavaScript bundles on two subdomains via source-code review and `grep` patterns:
- **Key 1** on subdomain A: `[REDACTED-KEY-1]`
- **Key 2** on subdomain B: `[REDACTED-KEY-2]`

Both keys were confirmed present in minified/beautified source and extractable with simple pattern matching.

### Step 2: API Testing

Both keys were tested against Segment's public API endpoints:

```bash
# Test Key #1 with Identify endpoint
curl -X POST "https://api.segment.io/v1/identify" \
  -u "[REDACTED-KEY-1]:" \
  -H "Content-Type: application/json" \
  -d '{"userId":"REDACTED-TEST","traits":{"email":"test@test.com","role":"admin"}}'
# Response: {"success": true}

# Test Key #2 with Track endpoint
curl -X POST "https://api.segment.io/v1/track" \
  -u "[REDACTED-KEY-2]:" \
  -H "Content-Type: application/json" \
  -d '{"event":"Test Event","userId":"REDACTED-TEST","properties":{"test":true}}'
# Response: {"success": true}
```

Both API calls returned `{"success": true}`, confirming the keys were active and functional.

### Step 3: Data Injection

With verified working keys, the following injection scenarios were demonstrated:

- **Fake analytics events:** `track`, `identify`, `page` events injected into the analytics pipeline
- **Fake user profiles:** Administrator-level user profiles created with arbitrary traits
- **Business metric pollution:** Revenue, conversion, and engagement metrics artificially inflated or distorted
- **Downstream integration triggers:** Email marketing, ad platforms, and CRM systems activated by fake events

---

## Approach

### Step 1: Bundle Analysis

Extracted and analyzed JavaScript bundles from the affected subdomains. Identified hard-coded Segment write key patterns in minified source code. Used `grep` and manual review to locate all key occurrences.

### Step 2: Key Validation

Tested each extracted key against Segment's API endpoints (`/v1/identify`, `/v1/track`, `/v1/page`). Confirmed all keys returned `{"success": true}`, proving they were active and not placeholder/dead keys.

### Step 3: Impact Demonstration

Demonstrated data injection capabilities by sending test events and user profiles through the validated keys. Showed how fake data would propagate through the analytics pipeline and affect business metrics.

---

## Results

- **Two Segment Write Keys exposed** in client-side JavaScript across multiple subdomains
- **Both keys tested and confirmed working** — `{"success": true}` responses from API endpoints
- **Data injection demonstrated** — fake analytics events, user profiles, and business metrics achievable
- **Business impact:** Analytics data integrity compromised; fake user profiles created; business metrics potentially distorted
- **CVSS:** 5.3 (Medium) — Information exposure with demonstrable impact
- **P2 (High)** per report severity classification

---

## Remediation Delivered

- **Immediate:** Both Segment Write Keys rotated [CRITICAL]
- **Access Segment dashboard** to revoke old keys and generate new ones
- **Move keys to server-side:** Use environment variables (e.g., `SEGMENT_WRITE_KEY`)
- **Never expose keys in client-side JavaScript**
- **Use server-side Segment SDKs** (Node.js, Python, Go, etc.)
- **Long-term:** Implement key rotation policy (every 90 days), automate rotation process, monitor for key exposure, add pre-commit hooks (git-secrets, TruffleHog), security training for developers on secret management

---

## Tooling

Injection probes, API test scripts, response-body capture, diff/compare scripts — all developed through AI-assisted generation. Designed the attack chain, reviewed every request, and validated each result end-to-end. 15+ Python/Bash PoC and automation scripts authored.

---

*Sanitized: organization name, subdomain names, and specific key values removed. Full technical report available on request under NDA. Report submitted to [REDACTED-REPORT-ID], validated by analyst [REDACTED-ANALYST], status: RESOLVED/APPROVED.*

