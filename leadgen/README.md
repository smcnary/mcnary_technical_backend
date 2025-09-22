# Tech Spec — Multi‑Source Lead Generator (API‑Driven)

**Doc owner:** <TBD>
**Stakeholders:** Growth, Sales Ops, Data Eng, Legal
**Status:** Draft v1.0
**Last updated:** <today>

---

## 1) Summary

We will build a lead-generation service that pulls, enriches, deduplicates, scores, and distributes leads for multiple business verticals (e.g., local services, B2B SaaS, e‑commerce). The system integrates with external data providers via APIs (e.g., Google Places, Yelp Fusion, Crunchbase, Clearbit, Apollo, Hunter, People Data Labs—final vendors TBD), normalizes heterogeneous schemas, applies vertical-specific enrichment and scoring, and syncs qualified leads to CRMs/marketing tools (e.g., Salesforce, HubSpot, Outreach, Mailchimp). The platform must be extensible via a "Source Adapter" and "Destination Adapter" pattern, support regional compliance, and provide observability and admin tooling.

**Success metrics**

* Time‑to‑first‑lead (TTFL) ≤ 1 hour from new vertical onboarding.
* Precision\@Top‑N ≥ 0.7 on pilot verticals after 2 weeks.
* Deduplication recall ≥ 95% across sources.
* 99.9% job success rate across ingestion pipelines.
* Cost per enriched lead ≤ target (TBD per vertical).

---

## 2) Objectives & Non‑Objectives

**Objectives**

* Aggregate leads from multiple APIs with pluggable adapters.
* Normalize to a canonical lead schema with confidence scores and provenance.
* Vertical‑aware enrichment (e.g., for local services include hours/geo; for SaaS include employee count, tech stack signals).
* ML/heuristics‑based scoring and prioritization; feedback loop from downstream CRMs.
* Robust deduplication and entity resolution across sources.
* Deliver to destinations (CRMs, CSV exports, webhooks) with idempotency.
* Admin console for configuration, monitoring, replays.

**Non‑Objectives (Phase 1)**

* Real‑time web scraping/crawling (only API‑based ingestion).
* Auto‑dialer/sequence execution.
* On‑device/mobile client.

---

## 3) Users & Use Cases

* **Growth Marketer:** configure campaigns by vertical, geo, firmographics, and target signals; export to Mailchimp/HubSpot.
* **Sales Ops:** define lead fields mapping to Salesforce, set routing rules, validate dedupe.
* **BDR/AE:** consume prioritized lead lists with reasons‑why and source evidence links.
* **Data Eng:** add a new data source adapter in <1 day> with minimal boilerplate; monitor health.

---

## 4) Requirements

### 4.1 Functional

1. **Source Adapters** for provider APIs: Google Places, Yelp Fusion, Crunchbase, Clearbit, Apollo, Hunter, PDL, Google Maps Geocoding, OpenCorporates (final list TBD, gated by contracts/quotas).
2. **Query Planning**: Given a campaign spec (vertical, geo, filters), compute one or more provider queries with pagination, rate‑limit awareness, and backoff.
3. **Normalization**: Map results to a **CanonicalLead** with typed fields and a **Provenance** list.
4. **Enrichment**: Optional async enrichment (domain, emails, phone, social, tech tags, revenue/employee ranges).
5. **Entity Resolution**: Deduplicate across sources using deterministic + probabilistic keys; attach **MergeGraph**.
6. **Scoring**: Per‑vertical scoring model combining rules + ML; output `lead_score` and `explanations`.
7. **Approval & QA**: Admin can spot‑check samples with side‑by‑side raw vs normalized data and approve/publish.
8. **Delivery**: Support Salesforce, HubSpot, Outreach, CSV, S3, Webhook; idempotent upserts; field mapping UI.
9. **Feedback Ingestion**: Pull disposition/outcome fields from CRMs (e.g., replied, qualified, closed) to retrain models.
10. **Scheduling**: Recurring campaigns; incremental updates; change detection (e.g., new reviews, job posts, tech changes).
11. **Observability**: Job logs, metrics, costs per source, per‑lead lineage, replay failed steps.
12. **Access Control**: RBAC (Admin, Operator, Viewer). API keys/credentials vaulting per environment.

### 4.2 Non‑Functional

* **Scalability:** 1M+ leads/week; burst friendly with autoscaling workers.
* **Latency:** Batch‑oriented; end‑to‑end within 6 hours for 100k leads at P50.
* **Reliability:** 99.9% pipeline success; exactly‑once delivery semantics per destination.
* **Cost:** Budget guardrails and alerting per source; dynamic throttling.
* **Security & Compliance:** SOC2‑friendly controls, audit logs; GDPR/CCPA compliant data handling; DPA with vendors.

---

## 5) Architecture

### 5.1 High‑Level Diagram (textual)

```
Campaign Config → Orchestrator → [Source Adapters] → Staging (Raw) → Normalizer → Enrichment →
Entity Resolver → Scorer → QA/Approval → Destination Adapters → CRM/Exports
                                   ↘────────────── Feedback Ingest ←─────────────↗
```

### 5.2 Services

* **API Gateway & Admin UI** (Next.js/React + Node/Go): auth, campaign CRUD, mapping UI, run controls.
* **Orchestrator** (Temporal/Argo/Airflow; choose one): DAGs for ingestion, fan‑out per provider, retries, backoff.
* **Workers** (Go/TypeScript/Python): adapter execution, normalization, enrichment, scoring, delivery.
* **Storage**:

  * **OLTP**: Postgres for campaigns, credentials, mapping, lineage.
  * **Data Lake**: S3/GCS raw JSON blobs; Parquet for analytics.
  * **Search**: OpenSearch/Elasticsearch for entity resolution and lead retrieval.
  * **Cache/Queue**: Redis for idempotency keys, rate‑limit tokens; Kafka/SQS for events.
* **ML/Rules Engine**: lightweight Python service (FastAPI) for scoring; model registry (e.g., MLflow) optional in v2.
* **Secrets**: Vault/AWS Secrets Manager for provider keys and OAuth tokens.
* **Observability**: OpenTelemetry, Prometheus/Grafana, structured logs, DLQ with replay.

---

## 6) Data Model (Canonical)

```json
CanonicalLead {
  lead_id: UUID,                     // internal stable id (post-resolution)
  source_records: [SourceRecord],    // raw snapshots by provider
  legal_entity: {
    name: string,
    alt_names: [string],
    registration_id: string|null,
    jurisdictions: [string]
  },
  brand: { name: string|null },
  vertical: enum[local_services, b2b_saas, ecommerce, healthcare, real_estate, other],
  website: string|null,
  domains: [string],
  emails: [Email],
  phones: [Phone],
  address: Address|null,
  geo: { lat: number, lon: number }|null,
  social: { linkedin: string|null, twitter: string|null, facebook: string|null, instagram: string|null },
  firmographics: { employees_range: string|null, revenue_range: string|null, founded_year: number|null },
  tech_signals: [string],            // e.g., Shopify, Stripe, Segment, React
  reviews: { count: number, rating: number|null, last_reviewed_at: timestamp|null },
  hours: [BusinessHour],
  tags: [string],
  lead_score: number|null,
  score_explanations: [string],
  provenance: [ProvenanceEntry],
  created_at: timestamp,
  updated_at: timestamp
}

SourceRecord {
  provider: string,                  // e.g., google_places
  provider_key: string,              // place_id, crunchbase uuid, etc.
  raw: jsonb,                        // raw response snapshot
  fetched_at: timestamp,
  hash: string                       // content hash for change detection
}

ProvenanceEntry { field: string, provider: string, confidence: number, note?: string }
Email { value: string, type: enum[generic, role, personal], verified: boolean|null, provider: string|null }
Phone { value: string, type: enum[main, mobile, fax], provider: string|null }
Address { line1: string, line2: string|null, city: string, region: string, postal: string, country: string }
BusinessHour { dow: number(0-6), open: string, close: string }
```

---

## 7) Source Adapter Pattern

**Interface**

```ts
export interface SourceAdapter {
  name: string;                                 // unique id
  supportsVerticals: Vertical[];                // e.g., [local_services]
  planQueries(input: CampaignSpec): QueryPlan;  // compute provider calls
  fetchPage(q: ProviderQuery, cursor?: string): Promise<Page<ProviderItem>>;
  normalize(item: ProviderItem): Partial<CanonicalLead> & { provenance: ProvenanceEntry[] };
  rateLimit(): RateLimitPolicy;                 // tokens/sec, burst, quotas
}
```

**Example: Google Places (Place Search + Details)**

* Inputs: `keywords`, `types`, `location(lat,lon)`, `radius`, `opennow?`, `minrating?`
* Pagination: `next_page_token` (2 min TTL)
* Fields: name, address components, place\_id, phone, website, opening\_hours, rating, user\_ratings\_total, geometry.
* Notes: Must comply with ToS and display attribution where required.

**Example: Yelp Fusion**

* Inputs: `term`, `categories`, `location` or `latitude/longitude`, `radius`.
* Fields: name, url, categories, rating, review\_count, phone, coordinates, location.

**Example: Firmographic Enrichment (Clearbit/PDL/Apollo/Crunchbase)**

* Inputs: domain/company name.
* Fields: employees, revenue, industry, emails, people roles, funding, tech tags.
* Compliance: verify license allows lead gen use; respect opt‑out.

---

## 8) Destination Adapter Pattern

**Interface**

```ts
export interface DestinationAdapter {
  name: string; // e.g., salesforce
  testConnection(cfg: DestinationConfig): Promise<Result>;
  upsertLead(lead: CanonicalLead, mapping: FieldMapping, opts: IdempotencyOptions): Promise<Result>;
  pullFeedback(since: Timestamp): Promise<LeadOutcomes[]>; // reply status, stage changes, etc.
}
```

**Mappings & Idempotency**

* Each destination defines a **natural key** (e.g., domain + phone, or website) to achieve upsert semantics.
* Maintain an **IdempotencyKey** per (destination, naturalKey, batchId).
* Backpressure on API limits with retry‑with‑jitter; DLQ after N attempts; replay UI.

---

## 9) Workflows

### 9.1 Ingestion (per campaign run)

1. Validate **CampaignSpec** (vertical, geo, filters, quotas, budget caps).
2. Build **QueryPlan** across enabled sources; estimate cost; compare to budget.
3. Execute plan with **token bucket** rate limiter per provider; page until exhaustion or quotas reached.
4. Persist **SourceRecord** snapshots to lake; emit events to `normalized.leads` topic.

### 9.2 Normalization & Enrichment

1. Adapter‑specific `normalize` → partial CanonicalLead + provenance.
2. Merge partials into **StagedLead** keyed by tentative entity key (domain || phone || address+name).
3. Enrich missing fields (domain discovery, emails, firmographics, tech tags).
4. Validate required fields per vertical; emit to `resolve.leads`.

### 9.3 Entity Resolution & Dedupe

* **Deterministic**: exact domain match; exact phone; normalized address+name (Soundex/Metaphone on name).
* **Probabilistic**: TF‑IDF/MinHash on name+address; email domain similarity; geo proximity (<100m).
* Build a **MergeGraph**; choose **golden** record with highest confidence per field; keep links.

### 9.4 Scoring

* **Inputs**: firmographics, tech signals, reviews, geo density, recency of changes, web presence, intent signals (job posts, tech installs).
* **Model**: start with rules + logistic regression baseline; pluggable XGBoost later.
* **Outputs**: `lead_score ∈ [0,100]`, explanations (top features), `priority_tier ∈ {A,B,C}`.

### 9.5 QA & Publish

* Sample K records per batch; show side‑by‑side raw vs normalized; approve or reject batch.
* On approve → deliver to destinations with field mapping; on reject → annotate and replay.

### 9.6 Feedback Loop

* Nightly sync from destinations; map outcomes back to `lead_id`; compute label for model retraining; recalc calibration.

---

## 10) API (External)

### 10.1 Authentication

* OAuth2 or API Key (per workspace).
* Rate limits: default 10 RPS per workspace; burst 100; 429 on exceed.

### 10.2 Endpoints (v1)

```
POST   /v1/campaigns                   // create campaign
GET    /v1/campaigns/{id}
POST   /v1/campaigns/{id}/run          // trigger manual run
GET    /v1/campaigns/{id}/runs         // list runs & status
GET    /v1/leads?campaignId=...&tier=A // query published leads
POST   /v1/destinations/test           // test connection
POST   /v1/deliveries                  // deliver ad‑hoc selection
POST   /v1/replays/{runId}             // replay failed steps
```

### 10.3 Example Payloads

**Create Campaign**

```json
{
  "name": "Denver Plumbers Q4",
  "vertical": "local_services",
  "geo": { "city": "Denver", "region": "CO", "country": "US", "radius_km": 50 },
  "filters": { "min_rating": 4.0, "review_count_min": 20 },
  "sources": ["google_places", "yelp"],
  "enrichment": ["clearbit", "hunter"],
  "budget": { "max_cost_usd": 500 },
  "schedule": { "cron": "0 2 * * *" },
  "destinations": [{ "type": "salesforce", "config_id": "sf-01" }]
}
```

**Lead (Published)**

```json
{
  "lead_id": "f9b3...",
  "name": "Ace Plumbing Co.",
  "vertical": "local_services",
  "website": "https://aceplumbing.com",
  "phones": [{ "value": "+1-303-555-0101", "type": "main" }],
  "address": { "city": "Denver", "region": "CO", "postal": "80202", "country": "US" },
  "reviews": { "count": 152, "rating": 4.6 },
  "lead_score": 87,
  "score_explanations": ["high rating", "many reviews", "service area in target radius"],
  "provenance": [{"field": "phone", "provider": "google_places", "confidence": 0.98}]
}
```

---

## 11) Rate Limits, Retries, Idempotency

* **Token Bucket** per adapter with dynamic tokens from a central policy registry; pre‑flight cost estimation.
* **Retries**: exponential backoff with full jitter; respect provider `Retry‑After`; circuit breaker on persistent 5xx.
* **Idempotency**: Hash of (provider, provider\_key, fetched\_at) for raw records; delivery idempotency keys per destination natural key.

---

## 12) Privacy, Legal, Compliance

* **Data Processing Agreements** with each provider and destination; document data categories.
* **Consent/Legitimate Interest** basis documented per region; maintain suppression list and opt‑out API.
* **GDPR/CCPA**: Right to Access/Delete endpoints; data minimization; retention policy (e.g., 12 months raw, 24 months canonical).
* **PII**: Store sensitive fields encrypted at rest (KMS), redact in logs, limit exposure via RBAC.

---

## 13) Observability & Tooling

* **Metrics**: leads/hour, cost/lead/source, dedupe\_rate, enrichment\_hit\_rate, model\_auc, delivery\_success\_rate.
* **Tracing**: end‑to‑end lead lineage trace id; span per adapter invocation.
* **Dashboards**: source health, spend vs budget, top signals.
* **Alerts**: quota near‑limit, cost anomaly, failure rate >1%, model drift, destination errors.

---

## 14) Deployment & Environments

* **Infra**: AWS (EKS + RDS + S3 + MSK/SQS + CloudWatch) or GCP equivalents.
* **Envs**: dev, staging, prod with isolated accounts.
* **CI/CD**: GitHub Actions; canary deployments for workers; schema migrations via Prisma/Flyway.

---

## 15) Security

* SSO (SAML/OIDC) for Admin UI.
* Secrets in AWS Secrets Manager/Vault; rotation policies.
* Network policies: egress‑only workers; allow‑listed destination IPs/webhooks.
* Audit logs for all data access and config changes.

---

## 16) Risks & Mitigations

* **API ToS changes / quota cuts** → multiple redundant providers; abstraction to swap vendors quickly.
* **Data quality variance** → provenance + confidence, human QA sampling, active learning loop.
* **Costs spike** → budget guardrails, adaptive throttling, pre‑flight estimates.
* **Dedup false merges** → conservative thresholds; manual split‑merge tooling with lineage.

---

## 17) Phased Delivery Plan

**Phase 0 (Week 0‑1):** skeleton project, canonical schema, one source (Google Places), CSV export.
**Phase 1 (Week 2‑4):** Yelp + Clearbit enrichment, dedupe, scoring v1 (rules), HubSpot adapter.
**Phase 2 (Week 5‑7):** Salesforce + webhooks, QA console, feedback ingestion, scoring v2 (LR).
**Phase 3 (Week 8‑10):** Multi‑tenant, budgets/alerts, model monitoring, role‑based access.

---

## 18) Open Questions (TBD)

* Final provider list & contracts; acceptable use confirmation per region.
* Exact verticals for launch; scoring features by vertical.
* Destination field mappings (Salesforce object types/leads vs accounts/contacts).
* Data retention durations by field category.

---

## 19) Appendix

### 19.1 Example Scoring (Rules Baseline)

```
score = 0
if reviews.rating >= 4.3 and reviews.count >= 50: score += 25
if has_website and domain_verified:           score += 15
if tech_signals includes 'Shopify':           score += 10 // for e‑com campaigns
if employees_range in ['11-50','51-200']:    score += 10 // for B2B SaaS target
if within_radius_km <= 25:                    score += 10
if email_verified_domain:                     score += 15
if phone_present and not toll_free:           score += 10
score = min(score, 100)
```

### 19.2 Sample ER (Entity Resolution) Weights (initial)

```
Exact domain match:                  1.00
Phone exact match:                   0.90
Name + address (normalized):         0.80
Geo distance < 50m + name sim>0.9:   0.75
Email domain match:                  0.60
Fuzzy name (Jaro-Winkler >0.92):     0.55
```

### 19.3 Pseudo‑code: Orchestration (Temporal)

```ts
export async function runCampaign(campaignId: string) {
  const spec = await loadCampaign(campaignId);
  const plan = await buildQueryPlan(spec);
  await Promise.all(plan.sources.map(src => ingestSource(src, spec)));
  await normalizeAndEnrich(spec);
  await resolveEntities(spec);
  await scoreLeads(spec);
  await qaAndPublish(spec);
  await deliver(spec);
}
```

### 19.4 Field Mapping Example (Salesforce)

| Canonical Field  | Salesforce Field     |
| ---------------- | -------------------- |
| `name`           | `Company`            |
| `website`        | `Website`            |
| `lead_score`     | `Lead_Score__c`      |
| `phone[0].value` | `Phone`              |
| `address.city`   | `City`               |
| `address.region` | `State`              |
| `address.postal` | `PostalCode`         |
| `vertical`       | `Industry`           |
| `provenance`     | `Lead_Provenance__c` |
