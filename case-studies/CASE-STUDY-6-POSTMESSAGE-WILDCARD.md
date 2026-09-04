# Case Study 6 — postMessage Wildcard Origin Session Token Leakage

| Field | Value |
|-------|-------|
| **Severity** | P2 (High) — but closed as Duplicate |
| **CWE** | CWE-200 (Exposure of Sensitive Information to Unauthorized Actor) / CWE-359 (Improper Input Validation) |
| **Impact** | Session token leakage, session hijacking, fraud bypass, automated bot attacks; all customers of the affected product affected |
| **Surface** | JavaScript enforcement iframe using `postMessage()` with wildcard origin `"*"` |
| **Status** | Packaged — triaged and closed as Duplicate of [REDACTED-REPORT-ID] (originally reported 2025-12-29); finding validated but duplicate; analyst was courteous and included findings excerpt for transparency |

---

## Problem

A digital friction/protection product's enforcement iframe used `postMessage()` with a wildcard origin (`"*"`) across all 8 event handlers. This allowed any website embedding the iframe to receive sensitive session tokens and challenge completion data via the `postMessage` API, which lacks origin validation when `"*"` is used.

**The iframe's 8 `postMessage` calls all used `origin: "*"`,** meaning any page could listen for and receive the messages — including session tokens embedded in the payload.

---

## Attack Chain

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Victim Website │────▶│  [REDACTED-COMPANY] iframe   │────▶│  Attacker Website│
│  Loads iframe   │     │  loads challenge │     │  captures tokens │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

### Technical Details

The iframe source contained 8 `postMessage` calls, all with identical structure:

```javascript
// All 8 instances use origin "*":
onCompleted:function(e){
    parent.postMessage(JSON.stringify({
        eventId:"challenge-complete",
        publicKey:t,
        payload:{sessionToken:e.token}  // ← SESSION TOKEN LEAKED
    }),"*")  // ← WILDCARD ORIGIN - VULNERABLE!
}

// Similarly for: onReady, onSuppress, onShown, onError, onWarning, onFailed, onResize
```

**6 out of 8 calls exposed session tokens** in the payload. The 2 non-token calls exposed frame size and error/warning information.

### Attack Scenario

1. Attacker creates a malicious website
2. Victim visits the attacker's website (or the iframe loads invisibly)
3. [REDACTED-COMPANY] challenge loads (visible or invisible)
4. Challenge completes/fails/shown
5. Session token sent to attacker's website via `postMessage("*")`
6. Attacker now has valid session token
7. Attacker can bypass fraud prevention OR hijack the session

---

## Approach

### Step 1: iframe Source Analysis

Downloaded the [REDACTED-COMPANY] enforcement iframe source code via `curl -sL`. Used `grep` to identify all `postMessage` calls and verify they all used the wildcard origin `"*"`.

### Step 2: Token Leakage Verification

Analyzed the `postMessage` payload structure to confirm session tokens (`sessionToken:e.token`) were included in 6 of 8 messages. Verified that `origin: "*"` meant no origin validation — any website could receive these messages.

### Step 3: Impact Assessment

- **Session token leakage** enables session hijacking, fraud bypass, and automated bot attacks
- **All customers affected** — this is [REDACTED-COMPANY]'s core fraud prevention product; every customer (financial institutions, e-commerce, gaming) is impacted
- **GDPR/CCPA compliance risks** from session tracking by third parties without proper origin validation
- **Automated attacks become possible** — the vulnerability lowers the barrier for bot-driven fraud

---

## Results

- **8 `postMessage` calls identified**, all using wildcard origin `"*"`
- **6 of 8 calls exposed session tokens** in payload to any origin
- **All [REDACTED-COMPANY] customers impacted** — this is the core fraud prevention product
- **Fraud prevention bypassed** — attackers can bypass protection programmatically
- **Session hijacking possible** — stolen tokens can potentially be reused
- **Automated bot attacks become possible** — barrier for bot-driven fraud lowered
- **GDPR/CCPA compliance risks** from session tracking without proper origin validation

**CVSS:** 5.3 (Medium) — Information exposure with demonstrable impact, but closed as Duplicate of [REDACTED-REPORT-ID] (reported 2025-12-29).

**Analyst note:** [REDACTED-ANALYST] (2026-03-19): "Thank you for your report! I appreciate the time you invested in researching this issue and submitting it to us. Unfortunately, this was submitted previously by another researcher." Excerpt from original report included for transparency.

---

## Remediation Delivered

- **Access iframe configuration** to restrict `postMessage` origin to specific trusted origins
- **Implement origin validation:** Use specific origin strings instead of `"*"` in all `postMessage` calls
- **Move session-sensitive data** to server-side channels rather than `postMessage`
- **Add message type filtering:** Only allow expected message types from trusted origins
- **Security headers:** Add `Content-Security-Policy` restrictions related to iframe embedding
- **Monitor for exposure:** Set up alerts for `postMessage` events received from unexpected origins

**Note:** Finding was validated as a real vulnerability but closed as Duplicate of [REDACTED-REPORT-ID] (originally reported 2025-12-29 by another researcher). The analyst was courteous and included an excerpt from the original report for transparency: "All the best for your next find! Look forward to your next awesome bug report!"

---

## Tooling

iframe source download scripts, `grep` pattern analysis for `postMessage` identification, payload structure analysis scripts — developed through AI-assisted generation. Designed the attack chain, reviewed each `postMessage` instance, and validated the wildcard origin vulnerability against the iframe's behavior.

---

*Sanitized: target product name and iframe URL removed. Full technical report available on request under NDA. Report submitted to [REDACTED-REPORT-ID], closed as Duplicate of [REDACTED-REPORT-ID] by analyst [REDACTED-ANALYST] on 2026-03-19. Analyst included findings excerpt for transparency.*

