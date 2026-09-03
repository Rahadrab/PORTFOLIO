# Case Study 2 — Search Injection → ACL Bypass + Health Data Exposure

| Field | Value |
|-------|-------|
| **Severity** | P2 |
| **CWE** | CWE-200 (Exposure of Sensitive Information) / CWE-284 (Improper Access Control) |
| **Impact** | GDPR Art. 9 — health data exposure (penalty ceiling €20M / 4% global turnover) |
| **Surface** | Public search endpoint of a national hospital group's CMS portal |
| **Status** | Packaged — evidence package delivered |

---

## Problem

A hospital group's public website search was backed by Lucene/Elasticsearch. The public UI showed only the guest scope, but the search endpoint accepted raw query-operator syntax. A WAF filtered obvious injection attempts.

**5,940 internal documents were exposed** — including pediatric psychiatry pages, surgical cost PDFs, mortuary forms, and supplier terms.

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

### Step 2: ACL Bypass via Scope Parameter
- Endpoint accepted an explicit scope identifier
- Passing the global scope (`scopeGroupId:0`) instead of guest scope returned the **entire CMS index**
- **5,940 results** for a keyword that returned 5 in public scope
- Internal documents exposed: clinical pages, surgical PDFs, mortuary forms, governing-board docs, supplier terms

### Step 3: User Enumeration via Field Syntax
- Field queries (`username:*`, `firstName:*`, `emailAddress:*`) showed the `username` field was indexed and anonymously queryable
- Narrowest enumerable user attribute — full user-field enumeration possible

## Results

- **Anonymous read of permission-gated internal hospital documents**
- Health-related content (child psychiatry, treatment costs) on a public endpoint = **GDPR Art. 9 exposure**
- User-field enumeration on indexed user store
- Full evidence package: WAF-bypass recap, scoped-vs-global comparison, field-query proof

## Remediation Delivered

- Server-side scope enforcement (never trust client-supplied scope)
- Field-allowlist on query parser (only index public fields)
- WAF coverage for GET requests + comment syntax

## Tooling

Injection probes, response-body capture, diff/compare scripts — developed through AI-assisted generation. I designed the bypass chain, reviewed each request, and validated every result against raw response captures.

---

*Sanitized: organization and endpoints removed. Full technical report available on request under NDA.*
