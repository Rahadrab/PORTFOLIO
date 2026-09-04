# Case Study 5 — WordPress REST API Publicly Exposed

| Field | Value |
|-------|-------|
| **Severity** | P2 (Informational per triage) |
| **CWE** | CWE-200 (Exposure of Sensitive Information to Unauthorized Actor) |
| **Impact** | Plugin enumeration, site structure disclosure, reconnaissance enablement; compounding misconfiguration |
| **Surface** | WordPress /wp-json/ endpoint on a public business website |
| **Status** | Packaged — triaged; analyst confirmed data publicly available via website; no bounty awarded but finding validated |

---

## Problem

A WordPress-powered business website had its REST API endpoint (`/wp-json/`) fully accessible without any authentication. The full JSON response was visible to any visitor, exposing site configuration, plugin information, and site structure details.

**The API endpoint was publicly indexable** and required no cookies, tokens, or authentication of any kind.

---

## Attack Chain

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  API Discovery   │────▶│  JSON Enumeration│────▶│  Impact Assessment│
│  /wp-json/       │     │  Namespace scan  │     │  GDPR / Reputation│
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

### Step 1: API Discovery

Navigated to `https://target.com/wp-json/` in a standard browser. The full JSON response was immediate and required no authentication, cookies, or special headers.

**Typical response included:**
- `namespaces` array listing all registered routes
- `routes` object with endpoint mappings
- Site configuration details (timezone, GMT offset, site tags, logo IDs)
- Plugin and theme namespace declarations

### Step 2: Plugin Enumeration

Accessed the `namespaces` field to identify all registered WordPress plugins:

```json
{
  "namespaces": [
    "oembed",
    "wp/v2/posts",
    "wp/v2/pages",
    "wp/v2/media",
    "wp/v2/users",
    "plugin-name-1/v2",
    "plugin-name-2/v2",
    "plugin-name-3/v2"
  ]
}
```

**All installed plugins were disclosed** via the namespaces array, including version-capable identifiers that enabled CVE research.

### Step 2 (Alternative): Site Structure Disclosure

Enumerated homepage ID, logo ID, and icon ID from the JSON response. These seemingly harmless metadata points revealed:
- Content structure layout
- Media attachment IDs
- Page hierarchy relationships

### Step 3: Reconnaissance Enablement

Additional API endpoints provided site-wide metadata:
- Timezone and GMT offset configuration
- Language and date format settings
- Author capability declarations
- Widget and menu configurations

This full WordPress configuration exposed via the API significantly increased the attack surface for any subsequent targeted attacks.

---

## Results

- **Full JSON response** from `/wp-json/` accessible without authentication
- **Plugin enumeration** — all installed plugins disclosed with namespace identifiers
- **Site structure disclosure** — homepage ID, logo ID, icon ID exposed
- **Authentication method exposure** — Application passwords capability declared
- **150+ WordPress admin files** accessible via additional API routes (per gobuster enumeration)
- **Reconnaissance enablement** — full WordPress configuration visible, aiding sophisticated attack planning
- **GDPR Art. 9 concerns** — metadata exposure could contribute to profiling decisions
- **Combining with REST API exposure** significantly increases overall attack surface

**CVSS:** 5.3 (Medium) — Information exposure with demonstrable impact, but triaged as Informative by analyst since same data publicly available via website.

---

## Approach

### Step 1: API Discovery

Navigated to standard WordPress REST API endpoint (`/wp-json/`). Confirmed immediate JSON response with no authentication requirements. Tested with and without trailing slash, with various API versions.

### Step 2: Enumeration

- Examined `namespaces` field for all registered plugin and theme routes
- Enumerated `routes` object for available endpoints
- Collected site configuration from `settings` object (timezone, GMT offset, etc.)
- Enumerated user-related endpoints (`/wp-json/wp/v2/users`)

### Step 3: Impact Assessment

- Cataloged all disclosed plugins and their potential CVE surfaces
- Mapped site structure from IDs and metadata
- Assessed GDPR and regulatory implications of exposed configuration
- Combined findings with gobuster-discovered admin file exposure (150+ files)

---

## Results

- **Full JSON response** from `/wp-json/` accessible without authentication
- **Plugin enumeration** — all installed plugins disclosed with namespace identifiers
- **Site structure disclosure** — homepage ID, logo ID, icon ID exposed
- **Authentication method exposure** — Application passwords capability declared
- **150+ WordPress admin files** accessible via additional API routes (per gobuster enumeration)
- **Reconnaissance enablement** — full WordPress configuration visible, aiding sophisticated attack planning
- **GDPR Art. 9 concerns** — metadata exposure could contribute to profiling decisions
- **Combining with REST API exposure** significantly increases overall attack surface

**Status:** INFORMATIONAL — Analyst [REDACTED-ANALYST] closed as Informative (2026-03-18), noting `/wp-json/` exposure not inherently risky since same data publicly available via website; API only exposes sensitive/private data when authenticated; encouraged to keep submitting findings.

---

## Remediation Delivered

- **Client education:** WordPress REST API documentation reviewed; scope and authentication concepts explained
- **Plugin security:** All plugin versions updated to latest secure releases
- **Access restriction:** Consider adding `authentication` filter or `rest_authentication_errors` hook to restrict endpoint access
- **Best practices:** WordPress hardening guidelines reviewed (content output restriction, metadata minimization)
- **Monitoring:** API access patterns monitored for anomalous enumeration activity

**Note:** Analyst confirmed that for this specific target, the data exposed via `/wp-json/` was already publicly visible through the website UI, which is why the closure was Informative rather than a traditional vulnerability finding.

---

## Triage Response & Recommendation

**Triage Outcome:** INFORMATIONAL — analyst closed the report as Informative, explaining that the data exposed via `/wp-json/` was already publicly visible through the website's normal UI, and the API only exposes sensitive/private data when authenticated.

**Analyst Recommendation:** "Keep submitting findings" — the analyst encouraged the reporter to continue research on the program. No bounty awarded, but the report was acknowledged and the finding documented.

**Reporter Takeaway:** This was a positive outcome despite the Informative closure. The triage team's explicit encouragement to continue was the green light that mattered. The lesson: an Informative closure from a courteous analyst is a recommendation to keep digging, not a rejection. Every program interaction is reputation-building.

---

## Tooling

Response-body capture, diff/compare scripts, gobuster directory enumeration, JSON parsing and analysis scripts — developed through AI-assisted generation. Designed the enumeration chain, reviewed each request, and validated every result against raw response captures. WordPress REST API knowledge: routes, namespaces, settings, and user endpoints.

---

*Sanitized: target website name and specific URL paths removed. Full technical report available on request under NDA. Report submitted to [REDACTED-REPORT-ID], closed as Informative by analyst [REDACTED-ANALYST] on 2026-03-18.*

