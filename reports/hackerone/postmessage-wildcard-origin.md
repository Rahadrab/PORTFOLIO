# REDACTED-PLATFORM Report [REDACTED-REPORT-ID]

**Title:** postMessage Wildcard Origin ("*") - Session Token Leakage  
**Program:** [REDACTED-COMPANY] Bounty  
**Reporter:** [REDACTED-USERNAME]  
**Date Submitted:** March 15, 2026, 6:03pm UTC  
**Status:** DUPLICATE  
**Closed:** March 19, 2026, 6:47am UTC by [REDACTED-ANALYST]  
**Original Report:** [REDACTED-REPORT-ID] (reported 2025-12-29)

---

## Vulnerability Summary

The [REDACTED-COMPANY] enforcement iframe uses `postMessage()` with a wildcard origin (`"*"`), allowing any website to receive sensitive session tokens and challenge completion data.

**Vulnerable Code:** All 8 `postMessage` calls use wildcard origin `"*"`, exposing session tokens in the payload:

```javascript
// All 8 instances:
onCompleted:function(e){
    parent.postMessage(JSON.stringify({
        eventId:"challenge-complete",
        publicKey:t,
        payload:{sessionToken:e.token}  // ← SESSION TOKEN LEAKED
    }),"*")  // ← WILDCARD ORIGIN - VULNERABLE!
}

// Similarly for: onReady, onSuppress, onShown, onError, onWarning, onFailed, onResize
```

**Technical Details:**
- 8 `postMessage` calls identified, all using `origin: "*"`
- Session tokens leaked in payload across multiple event types
- No origin validation = any embedding website can receive tokens

---

## Proof of Concept

**Step 1: Download iframe source**
```bash
curl -sL "https://iframe.[REDACTED-DOMAIN]/" -o iframe-source.html
```

**Step 2: Verify wildcard origin**
```bash
cat iframe-source.html | grep -oE 'postMessage.*"*"'
```

**Evidence:**
- `iframe-source.html` — Complete vulnerable iframe source code with all 8 `postMessage` calls (202.36 KiB)
- `sessiontoken.png` — Burp Suite capture showing postMessage with session token leakage

---

## Technical Impact

**Session Token Leakage:**
- 6 out of 8 `postMessage` calls expose session tokens to **any origin**
- Any website embedding the iframe can receive session tokens
- Tokens sent with wildcard origin = no origin validation

**Challenge Bypass:**
- Attackers can receive challenge completion tokens
- Fraud prevention can be bypassed programmatically
- Automated attacks become possible

**Session Hijacking:**
- Stolen session tokens can potentially be reused
- Attacker can impersonate legitimate users
- Account takeover possible

**User Tracking:**
- All challenge events can be monitored by any website
- User behavior tracking without consent
- Privacy violations

---

## Business Impact

**All Customers Affected:**
- This is [REDACTED-COMPANY]'s CORE fraud prevention product
- EVERY customer using [REDACTED-COMPANY] is impacted
- Includes financial institutions, e-commerce, gaming platforms

**Fraud Prevention Bypassed:**
- Attackers can bypass [REDACTED-COMPANY] protection
- Automated bot attacks become possible
- Financial losses for customers

**Reputation Damage:**
- Core product vulnerability
- Customer trust impacted
- Potential regulatory scrutiny

**Regulatory Compliance:**
- GDPR violations (session tracking by third parties)
- CCPA violations (data exposure)
- Potential fines for customers

---

## Attack Scenario

1. Attacker creates malicious website
2. Embeds [REDACTED-COMPANY] iframe (`https://iframe.[REDACTED-DOMAIN]`)
3. Victim visits attacker's website
4. [REDACTED-COMPANY] challenge loads (visible or invisible)
5. Challenge completes/fails/shown
6. Session token sent to attacker via `postMessage("*")`
7. Attacker now has valid session token
8. Attacker can bypass fraud prevention OR hijack session

---

## Supporting Material

- `iframe-source.html` — Complete vulnerable iframe source code (202.36 KiB)
- `sessiontoken.png` — Burp Suite capture showing postMessage with session token leakage

---

## Triaging Analyst Notes

**[REDACTED-ANALYST]** (March 19, 2026, 6:47am UTC):
> "Thank you for your report! I appreciate the time you invested in researching this issue and submitting it to us.
> 
> Unfortunately, this was submitted previously by another researcher. Unfortunately, we cannot add you to the original report as this report contains additional information that we cannot share with you. This may include personal information or additional vulnerability information that shouldn't be exposed to other users. Thank you for your understanding.
> 
> For transparency, I am including an excerpt here from the original report:
> 
> **Title:** [REDACTED-COMPANY] CAPTCHA Session Token Exposure via Insecure postMessage Origin leads to token theft
> **State:** informative
> **Date:** 2025-12-29T09:30:44.096Z
> 
> All the best for your next find! Look forward to your next awesome bug report!
> 
> Cheers,
> [REDACTED-ANALYST]"

**[REDACTED-USERNAME]** (March 14, 2026, after submission):
> "As a new researcher even knowing that my submission was ok is just a great, i m really happy with it, as i know i m in the right path. thank you for your valuable time letting me know this."

---

## Status: DUPLICATE

**Closed as duplicate of [REDACTED-REPORT-ID]** (originally reported 2025-12-29 by another researcher).  
Finding was validated but duplicate; analyst was courteous and included findings excerpt for transparency. Reporter accepted gracefully and noted they are "in the right path" as a new researcher.