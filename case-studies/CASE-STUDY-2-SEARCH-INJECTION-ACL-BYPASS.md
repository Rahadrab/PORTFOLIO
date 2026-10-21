# Case Study 2 — Search Injection → Scope Bypass

| Field | Value |
|-------|-------|
| **Severity** | P2 |
| **CWE** | CWE-200 (Exposure of Sensitive Information) / CWE-284 (Improper Access Control) |
| **Impact** | Unauthenticated access to the global CMS index — scope enforcement absent on the search surface |
| **Surface** | Public search endpoint of a national hospital group's CMS portal |
| **Status** | Closed by the program as not applicable — see the correction below |

> **Correction on the record (2026-10-21).** An earlier version of this write-up claimed
> "5,940 internal documents", "mortuary forms", "supplier terms", "governing-board docs" and
> "GDPR Art. 9 health data exposure". **Re-checking the captures showed those do not hold.**
> The 5,940 was a **result count**, not a document count; the content behind it was the
> hospital's **public** site content (cost estimates, clinic pages, patient guides, an open
> webinar); "mortuary forms" and "governing-board documents" appear in **no capture at all**,
> and "supplier terms" appear in the *public*-scope capture. The program closed the report as
> not applicable on exactly this basis. The claims below are what the evidence actually
> supports. Everything the technique *did* establish — the WAF bypass, the scope differential,
> the field enumeration — was reproducible and is unchanged.

---

## Problem

A hospital group's public website search was backed by Lucene/Elasticsearch. The public UI showed only the guest scope, but the search endpoint accepted raw query-operator syntax **and an explicit scope identifier**. A WAF filtered obvious injection attempts.

**5,940 results were returned for a query that returned 5 in guest scope.** The content behind
them was the hospital's public web content — care pages, cost estimates, patient guides, an
event invitation — but it was served by the **global** CMS index rather than the guest scope
the public site is meant to expose.

## Attack Chain

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  WAF Bypass      │────▶│  Scope Bypass     │────▶│  Field          │
│  Comment syntax  │     │  scopeGroupId:0   │     │  Enumeration    │
│  + GET method    │     │  → Global index   │     │  username:*     │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

## Approach — Three Steps

### Step 1: WAF Bypass
- `AND`/`OR` keyword queries → 403 (blocked)
- **Two variants passed clean:**
  - Query-comment syntax: `AND //`, `AND /**/`
  - Method switching: POST → GET (WAF didn't filter GET)
- Once through, all special query operators became usable

### Step 2: Scope Bypass via Scope Parameter
- Endpoint accepted an explicit scope identifier
- Passing the global scope (`scopeGroupId:0`) instead of guest scope returned the **entire CMS index**
- **5,940 results** for a keyword that returned 5 in public scope — a reproducible differential
- Content was public-site material reached through the global index; the security defect is the
  **missing scope enforcement on the search surface**, not the sensitivity of the material

### Step 3: User Enumeration via Field Syntax
- Field queries (`username:*`, `firstName:*`, `emailAddress:*`) showed the `username` field was indexed and anonymously queryable
- Narrowest enumerable user attribute — full user-field enumeration possible

## Results

- **Unauthenticated reach of the global CMS index** — 5,940 vs 5 results, reproduced across
  multiple scope ids and on two separate runs
- **User-field enumeration** on the indexed user store (`username:*` → 1 result, 0 for every
  other field)
- Full evidence package: WAF-bypass recap, scoped-vs-global comparison, field-query proof

**The finding that stands:** the search surface does not enforce scope server-side, so a
client-supplied scope id selects the index. On a site where the global index holds
non-guest material, that is an access-control defect. On this site the material proved to be
public, which is why the program closed it as not applicable — the technique reproduced, the
impact did not.

## Remediation Delivered

- Server-side scope enforcement (never trust client-supplied scope)
- Field-allowlist on query parser (only index public fields)
- WAF coverage for GET requests + comment syntax

## Tooling

Injection probes, response-body capture, diff/compare scripts — developed through AI-assisted generation. I designed the bypass chain, reviewed each request, and validated every result against raw response captures.

---

*Sanitized: organization and endpoints removed. Full technical report available on request under NDA.*
