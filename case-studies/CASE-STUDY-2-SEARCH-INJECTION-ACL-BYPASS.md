# Case Study 2 — Search-Query Injection: ACL Bypass + User Enumeration on a Hospital Search Portal

**Finding class:** CWE-200 (Exposure of Sensitive Information) / CWE-284 (Improper Access Control) · GDPR Art. 9 (health data)
**Vulnerable surface:** public search endpoint of a national hospital group's CMS portal

---

## 1. Problem

A hospital group's public website search was backed by a full-text search engine (Lucene/Elasticsearch). The public UI exposed only the guest site scope, but the search endpoint itself accepted raw query-operator syntax — and a WAF was filtering obvious injection attempts.

## 2. Approach

Three steps, each building on the last:

1. **WAF bypass.** `AND`/`OR` keyword queries returned 403, but two variants passed clean: query-comment syntax (`AND //`, `AND /**/`) and switching from POST to GET (the WAF didn't filter GET). Once through, all special query operators became usable.
2. **ACL bypass via scope parameter.** The endpoint accepted an explicit scope identifier. Passing the global scope (`scopeGroupId:0`) instead of the guest scope returned the **entire CMS index** — 5,940 results for a keyword that returned 5 in the public scope, including internal clinical and administrative documents: pediatric psychiatry clinic pages, surgical cost-estimate PDFs, mortuary administrative forms, governing-board documents, supplier terms.
3. **User enumeration via field syntax.** Field queries (`username:*` vs `firstName:*`, `emailAddress:*`) showed the `username` field of user entities was indexed and anonymously queryable — the narrowest enumerable user attribute.

Every step was verified by capturing full HTTP response bodies, comparing scoped vs global result sets, and scripting the proof chain.

## 3. Result

- **Anonymous read of permission-gated internal hospital documents** — health-related content (child psychiatry, treatment cost documents) on a public endpoint = GDPR Art. 9 exposure (penalty ceiling €20M / 4% of global turnover).
- **User-field enumeration** on the indexed user store.
- Delivered a report with a consolidated evidence package (WAF-bypass recap, scoped-vs-global comparison, field-query proof) and remediation: server-side scope enforcement, field-allowlist on the query parser, WAF coverage of GET + comment syntax.

## 4. Method note

Injection probes, response-body capture, and diff/compare scripts developed through AI-assisted generation; I designed the bypass chain, reviewed each request, and validated every result against raw response captures before writing the report.

---

*Sanitized: organization and endpoints removed. Full technical report available on request under NDA.*