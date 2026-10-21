# REDACTED-PLATFORM Report [REDACTED-REPORT-ID]

**Title:** Multiple Segment API Write Keys Exposed in Client-Side JavaScript  
**Program:** [REDACTED-COMPANY] / [REDACTED-COMPANY] Bounty  
**Reporter:** [REDACTED-USERNAME]  
**Date Submitted:** March 14, 2026, 7:52am UTC  
**Status:** RESOLVED / APPROVED  
**Closed:** Duplicate of [REDACTED-REPORT-ID] (original reporter not identified)

---

## Vulnerability Summary

Multiple Segment API Write Keys are exposed in client-side JavaScript code across multiple subdomains of `[REDACTED-DOMAIN]`, allowing attackers to inject fake analytics data, create fake user profiles, and pollute business metrics.

**Keys Exposed:**
- **Key 1:** `REDACTED_KEY_1` (found on `voting.[REDACTED-DOMAIN]`)
- **Key 2:** `REDACTED_KEY_2` (found on `issuer.[REDACTED-DOMAIN]`)

---

## Proof of Concept

**Step 1: Extract keys from source code**
```bash
curl -s "https://voting.[REDACTED-DOMAIN]/" -H "X-Bug-Bounty: RAHAD" -H "X-Test-Account-Email: [REDACTED-EMAIL]" | grep -o 'REDACTED_KEY_1'
curl -s "https://issuer.[REDACTED-DOMAIN]/" -H "X-Bug-Bounty: RAHAD" -H "X-Test-Account-Email: [REDACTED-EMAIL]" | grep -o 'REDACTED_KEY_2'
```

Both commands return the respective API keys, confirming they are present in the source code.

**Step 2: Test Key #1 with Segment Identify Endpoint**
```bash
curl -X POST "https://api.segment.io/v1/identify" \
-u "REDACTED_KEY_1:" \
-H "Content-Type: application/json" \
-d '{"userId":"REDACTED-TEST","traits":{"email":"test@test.com","role":"admin"}}'
```
**Response:** `{"success": true}`

**Step 3: Test Key #2 with Segment Track Endpoint**
```bash
curl -X POST "https://api.segment.io/v1/track" \
-u "REDACTED_KEY_2:" \
-H "Content-Type: application/json" \
-d '{"event":"Test Event","userId":"REDACTED-TEST","properties":{"test":true}}'
```
**Response:** `{"success": true}`

---

## Impact

Both keys were tested and confirmed working with actual API responses showing `{"success": true}`. The keys were extracted directly from the source code of the affected subdomains and successfully used to inject fake data into Segment analytics.

**Attackers can:**
- Inject fake analytics events (track, identify, page)
- Create fake user profiles with arbitrary traits
- Pollute business metrics and revenue data
- Trigger downstream integrations (email, ads, CRM)
- Cause compliance/reporting issues (GDPR, financial)

**CVSS:** 5.3 (Medium) — Information exposure with demonstrable impact  
**Severity:** P2 (High) per report title

---

## Recommended Solution

**Immediate Actions (Within 24-48 hours):**
- Rotate Both Segment Write Keys [CRITICAL]
- Access Segment dashboard to revoke old keys and generate new ones
- Audit Segment Data — check for fake users/events already injected
- Review recent analytics for anomalies
- Search for user ID: "REDACTED-TEST" (test data created during responsible testing)

**Move Keys to Server-Side:**
- Use environment variables (e.g., `SEGMENT_WRITE_KEY`)
- Never expose keys in client-side JavaScript
- Use server-side Segment SDKs (Node.js, Python, Go, etc.)

**Long-Term Actions (Within 30 days):**
- Implement Key Rotation Policy — rotate keys every 90 days
- Automate rotation process
- Monitor for key exposure
- Security Training — train developers on secret management
- Include in code review checklist
- Add pre-commit hooks to prevent key commits (use tools like git-secrets, TruffleHog)
- Monitor for Exposure — set up alerts for key usage from unexpected origins
- Use tools like GitGuardian, Detectify, or Segment's built-in monitoring

---

## Supporting Material

- `apikey-source.png` — Shows key extraction from source code (43.86 KiB)
- `api-test.png` — Shows successful API responses with `{"success": true}` (118.40 KiB)
- 8 additional screenshots show comprehensive testing methodology

---

## Triaging Analyst Notes

**[REDACTED-ANALYST]** (March 14, 2026, 7:31pm UTC):
> "Unfortunately, this was submitted previously by another researcher, but we appreciate your work and look forward to additional reports from you.
> 
> At this time, we cannot add you to the original report as the report may contain additional information that we cannot share with you. This may include personal information or additional vulnerability information that shouldn't be exposed to other users. Thank you for your understanding."

**[REDACTED-USERNAME]** (March 14, 2026, 9:01pm UTC):
> "As a new researcher even knowing that my submission was ok is just a great, i m really happy with it, as i know i m in the right path. thank you for your valuable time letting me know this."

---

## Status: RESOLVED / APPROVED

Report validated by analyst; reporter accepted the duplicate closure gracefully. Reporter noted they are "in the right path" as a new researcher.