# Technical Specification — Symfony SEO Audit Automation Service

**Product name:** CounselRank SEO Audit Service  
**Owner:** Backend Platform Team  
**Tech stack:** Symfony 7.3 (PHP 8.2+), API Platform 4.1, Doctrine ORM (PostgreSQL 16), Symfony Messenger, Redis, Headless Chrome, Docker, OpenTelemetry.

---

## 1) Purpose & Goals

Automate comprehensive SEO audits for one or more websites, producing repeatable, explainable, and diffable results. The system must be:

* **Accurate**: deterministic checks with reproducible evidence (HTML snapshots, headers, screenshots).
* **Scalable**: handle thousands of pages per audit with worker pools & back-pressure.
* **Tenant‑aware**: agencies manage many client sites with strict isolation.
* **Actionable**: weighted scoring + prioritized recommendations and diffs vs previous runs.
* **Auditable**: every finding backed by evidence and the exact rule version.

**Primary outputs:** JSON API, HTML/PDF report, CSV exports, webhooks.

---

## 2) In-Scope / Out-of-Scope

**In-scope:** technical/on‑page/off‑page\* (via connectors), performance (PSI/Lighthouse), accessibility (subset, automated only), sitemaps/robots, Core Web Vitals, indexability heuristics, content signals.

**Out-of-scope (v1):** manual accessibility, advanced NLP topic modeling, backlink crawling (we'll read vendor APIs), rank tracking, site migrations.

\*Off‑page limited to vendor APIs (Ahrefs/Semrush/Majestic) if credentials are provided.

---

## 3) High-Level Architecture

* **API App (Symfony + API Platform):** REST endpoints, auth, RBAC, multi‑tenant, orchestration.
* **Workers (Symfony Messenger):** async jobs: crawl → lighthouse → analyze → aggregate → report → notify.
* **Scheduler:** CRON (k8s CronJob) or Symfony Scheduler to enqueue recurring audits.
* **Crawler Service:** polite, robots-aware, concurrent HTTP fetcher & HTML parser.
* **Analysis Service:** rules engine (versioned) that evaluates checks on stored page artifacts.
* **Scoring Service:** category weights + per‑check severity → overall & category scores.
* **Reporting Service:** HTML/PDF (wkhtmltopdf or headless Chrome print), CSV/JSON exports.
* **Connectors:** Google PageSpeed Insights (CrUX + Lab), Google Search Console (OAuth2), vendor backlink APIs.
* **Storage:** PostgreSQL (entities & results), Redis (queues/cache/locks), S3‑compatible object store (artifacts, screenshots, PDFs).
* **Observability:** Monolog, OpenTelemetry traces, Prometheus metrics.

**Sequence (per audit run):**
`POST /audits` → create run (DRAFT) → enqueue → \[Crawl N pages] → \[Run Lighthouse subset on sample] → \[Analyze checks] → \[Aggregate scores] → \[Generate reports] → \[Emit webhooks] → state=COMPLETED.

---

## 4) Multi‑Tenancy & RBAC

* **Tenancy model:** Agency → Client → Project/Site → Audits.
* **Isolation:** `tenant_id` on all rows, Postgres RLS (Row Level Security) enforced by connection role + Doctrine filters.
* **Roles:** `ROLE_SUPER_ADMIN`, `ROLE_AGENCY_ADMIN`, `ROLE_ANALYST`, `ROLE_CLIENT_READ`.

---

## 5) Data Model (PostgreSQL 16)

**Core entities** (simplified columns only):

**tenant**(id, name, created_at)

**user**(id, tenant_id FK, email UNIQUE, password_hash, roles JSONB, is_active, created_at, last_login_at)

**client**(id, tenant_id FK, name, notes, created_at)

**project**(id, tenant_id FK, client_id FK, name, primary_domain, start_url, crawl_scope ENUM\[domain, subpath, list], allowed_paths JSONB, blocked_paths JSONB, max_pages INT, created_at)

**credential**(id, tenant_id FK, type ENUM\[gsc, psi, ahrefs, semrush, majestic], label, encrypted_payload JSONB, created_at)

**audit**(id, tenant_id FK, project_id FK, label, schedule_cron NULLABLE, categories_weight JSONB, created_at)

**audit_run**(id, tenant_id FK, audit_id FK, state ENUM\[DRAFT,QUEUED,RUNNING,FAILED,CANCELED,COMPLETED], started_at, finished_at, requested_by FK user, seed_urls JSONB, config JSONB, totals JSONB, version_semver, error TEXT)

**page**(id, tenant_id FK, project_id FK, url HASHED_INDEX, status_code, content_type, fetched_at, response_time_ms, content_length, canonical_url, robots_directives JSONB, headers JSONB, hash_body, sitemap_priority, discovered_via ENUM\[crawl,sitemap,seed], screenshot_key, html_key)

**lighthouse_run**(id, tenant_id FK, audit_run_id FK, url, json_key, performance FLOAT, accessibility FLOAT, best_practices FLOAT, seo FLOAT, pwa FLOAT, created_at)

**check**(id, code UNIQUE, title, category ENUM\[technical,content,authority,ux], severity ENUM\[info,low,med,high,critical], default_weight FLOAT, version_int, description, fix_hint)

**finding**(id, tenant_id FK, audit_run_id FK, page_id FK NULLABLE, check_id FK, status ENUM\[pass,fail,warn,na], evidence JSONB, score_delta FLOAT, created_at)

**metric**(id, tenant_id FK, audit_run_id FK, page_id FK NULLABLE, key TEXT, value NUMERIC, unit TEXT, created_at)

**report**(id, tenant_id FK, audit_run_id FK, format ENUM\[json,html,pdf,csv], storage_key, bytes, checksum, created_at)

**webhook_subscription**(id, tenant_id FK, target_url, secret, events JSONB, is_active, created_at)

**job_lock**(key PRIMARY KEY, acquired_until TIMESTAMPTZ)

**Indexes & constraints**

* Unique `(tenant_id, url_hash)` on `page` to dedupe.
* Partial indexes for `audit_run(state)` and `page(fetched_at desc)`.
* GIN on JSONB columns used in filtering (`headers`, `robots_directives`).
* RLS policies: tenant scoping for all tables.

---

## 6) External Integrations

* **Google PageSpeed Insights API:** field (CrUX) + lab metrics. Keys stored in `credential`. Backoff & daily quotas tracked in `metric`.
* **Google Search Console API:** OAuth 2.0 (offline refresh). Scopes: `https://www.googleapis.com/auth/webmasters.readonly`. Pull index coverage, sitemaps list, crawl errors.
* **Backlink providers (optional):** Ahrefs/Semrush/Majestic — pull DR/UR, backlink counts, referring domains, toxic links summary.

Security for credentials: AES-256-GCM via Sodium, per-tenant KMS key; rotate keys; redact in logs.

---

## 7) Crawling & Discovery

* **Seeds:** `project.start_url` + sitemap URLs discovered at `https://domain/sitemap.xml` and via `robots.txt` `Sitemap:` hints.
* **Scope rules:** domain, subpath, or explicit URL list; respect `allowed_paths` / `blocked_paths`.
* **Robots compliance:** parse `robots.txt`; respect `Disallow`, per‑agent rules; support `X-Robots-Tag` headers.
* **Concurrency:** token bucket per host; global max in config; retries with jitter; 429/503 backoff.
* **HTTP client:** Symfony HttpClient with timeouts, redirects off after 10 hops, size cap, user‑agent string including run id.
* **Artifacts:** store raw HTML & headers to object storage; optional full‑page screenshot (headless Chrome) for top N pages.
* **Dedup:** hash of normalized URL + `ETag`/`Last-Modified` hints to skip unchanged pages on incremental audits.

---

## 8) Checks Library (v1)

**Technical**

* HTTP status (200/3xx, 4xx/5xx), redirect chains, mixed content, HSTS, gzip/brotli, cache headers, canonical correctness, hreflang syntax, robots meta (`noindex`, `nofollow`), X‑Robots‑Tag, duplicate titles/descriptions (sitewide), sitemap/robots validity, broken internal/external links, pagination rels, structured data presence (JSON‑LD parseability), mobile friendliness (viewport), image format & size (AVIF/WebP), 404/410 handling, www/non‑www & http/https canonicalization, server response time, TLS version, cookie bloat.

**Content / On‑page**

* Title length & uniqueness, meta description presence/length, H1 presence/uniqueness, heading outline, keyword presence in title/H1/URL (from audit focus terms), image `alt`, link anchor clarity, thin content (word count), canonicalized duplicates, Open Graph/Twitter Card tags, indexable pagination.

**Authority (via connectors)**

* Domain Rating / Authority score, referring domains, backlink velocity summary, top anchors, toxic links count.

**UX / Performance**

* Core Web Vitals (FID/INP, LCP, CLS) from CrUX (field) when available; Lighthouse lab metrics (TTI, Speed Index, TBT); font loading issues; render‑blocking resources; unused CSS/JS (Lighthouse flags).

**Accessibility (auto)**

* Alt text missing, color contrast (from Lighthouse), ARIA role issues subset, link purpose.

Each check is versioned and mapped to a **`check.code`** (e.g., `TECH_CANONICAL_VALID`, `CONTENT_TITLE_LENGTH`). Failures create `finding` with evidence payload (e.g., offending URLs, header excerpts, computed values).

---

## 9) Scoring Model

* Category weights (defaults): Technical **40%**, Content **30%**, Authority **20%**, UX **10%**. Override per audit.
* Per‑check weight: `check.default_weight` scaled by severity.
* Score per category: `100 - Σ(weight_of_failed_checks * penalty)` (cap ≥ 0).
* Overall score: weighted average across categories.
* **Diffing:** compare to prior completed run → deltas per category and top 5 improved/regressed findings.

---

## 10) Workflow & State Machine

Use Symfony Workflow (`audit_run`):

* **States:** DRAFT → QUEUED → RUNNING → {FAILED|COMPLETED|CANCELED}
* **Transitions:** `enqueue`, `start`, `fail`, `complete`, `cancel`.
* Workers emit domain events to drive transitions; idempotent by `run_id`.

**Queues (Messenger):**

* `crawl` (CrawlPageMessage)
* `lighthouse` (RunLighthouseMessage)
* `analyze` (AnalyzePageMessage)
* `aggregate` (AggregateAuditMessage)
* `report` (GenerateReportMessage)
* `notify` (EmitWebhookMessage)

Transports: Redis Streams (default) or RabbitMQ for high throughput. Concurrency per transport configurable.

---

## 11) API Design (API Platform)

**Auth**

* JWT bearer (LexikJWTAuthenticationBundle) for first‑party; OAuth 2 for Google connectors.

**Core endpoints** (subset):

* `POST /audits` → create or trigger an audit run; body: `{ project: "/projects/{id}", label?, config?, schedule? }` → `201 Created` with run resource.
* `GET /audits` → list audits (filter by client/project, active schedules).
* `GET /audit-runs?audit={id}&state=...` → list runs.
* `GET /audit-runs/{id}` → run status & summary.
* `POST /audit-runs/{id}/cancel` → cancel queued/running.
* `GET /audit-runs/{id}/findings` (paginated, filter by check/category/severity/page).
* `GET /audit-runs/{id}/metrics`
* `POST /projects` / `GET /projects/{id}` …
* `POST /credentials` (stores encrypted vendor API creds) → masked responses.
* `POST /webhooks` → subscribe to events (`audit.run.completed`, `audit.run.failed`).
* `GET /reports/{id}` → presigned download URL.

**Minimal example: Create run**

```json
POST /audits
{
  "project": "/projects/42",
  "label": "August crawl",
  "config": {
    "maxPages": 2000,
    "sampleForLighthouse": 30,
    "focusKeywords": ["tulsa injury lawyer", "oklahoma attorney"],
    "respectCanonical": true
  }
}
```

→ `201` body includes `audit_run` with links to status.

**Webhooks:** HMAC SHA256 signature in `X-CR-Signature`. Retries with exponential backoff. Example event body:

```json
{
  "type": "audit.run.completed",
  "runId": "ar_01HZZ...",
  "projectId": 42,
  "overallScore": 81.2,
  "deltas": {"technical": +4.3, "content": -1.2},
  "report": {"pdf": "s3://.../report.pdf", "json": "s3://.../report.json"}
}
```

---

## 12) Reporting & Exports

* **Formats:** JSON (full), HTML dashboard, PDF (print), CSV (pages, findings, metrics).
* **Storage:** S3 path: `tenants/{tenant}/{project}/{run}/report.{ext}`; presigned URL TTL configurable.
* **Redaction:** do not expose headers/cookies that contain secrets.
* **Top sections:** Summary & scorecards, Prioritized fixes (by effort × impact), Findings table, Core Web Vitals, Coverage vs previous run, Appendix (evidence samples).

---

## 13) Configuration & Secrets

* `APP_ENV`, `APP_SECRET` (Symfony)
* `DB_DSN` (Postgres)
* `REDIS_DSN` (cache + messenger)
* `OBJECT_STORE_*` (S3 endpoint, bucket, access/secret)
* `GSC_CLIENT_ID/SECRET`, `PSI_API_KEY`
* `MAX_CONCURRENCY`, `HTTP_TIMEOUT_MS`, `SCREENSHOTS_ENABLED`
* Key rotation schedule & KMS master key id.

---

## 14) Security & Compliance

* **RLS + per‑tenant encryption** of credentials; AES‑GCM with nonce per row; rotate keys.
* **Input validation & SSRF protection**: whitelist protocols, disallow internal IP ranges, block `file:`/`ftp:`.
* **Rate limiting** per user/tenant on mutation endpoints.
* **Data retention:** configurable purge policy for artifacts/screenshots.
* **PII handling:** HTML snapshots may contain emails/phones → restrict access to tenant users only.

---

## 15) Deployment & Scaling

* **Docker Compose (dev):** `nginx`, `php-fpm`, `postgres`, `redis`, `minio` (S3), `chrome` (headless), `worker`.
* **Kubernetes (prod):** HPA‑enabled `api` and `worker` deployments; Redis/RabbitMQ as managed; Postgres managed (e.g., RDS).
* **Autoscaling:** scale workers on queue depth; separate pools per queue (crawl heavy, lighthouse CPU heavy).
* **SLOs:** API p95 < 200ms; run orchestration overhead < 1% of total job time; webhook delivery success ≥ 99.9%.

---

## 16) Observability

* **Logs:** JSON via Monolog; correlation id = run id.
* **Metrics:** pages crawled/sec, avg response time, % robots‑blocked, finding counts by severity, Lighthouse durations, webhook failures.
* **Tracing:** OpenTelemetry instrumentation for queue handlers and HTTP calls.

---

## 17) Testing Strategy

* **Unit:** rules engine per check with fixtures.
* **Integration:** crawl sandbox with WireMock; PSI/GSC connectors with mocked servers; RLS tests.
* **E2E:** spin ephemeral env with seeded site; assert scores & report artifacts.
* **Performance:** load test 10k‑page crawl; ensure memory stable; no head-of-line blocking.
* **Contract tests:** for webhooks and public API (OpenAPI schema via API Platform).

---

## 18) CLI & Operations

* `bin/console audit:run {auditId} [--max-pages=] [--force]`
* `bin/console audit:schedule:tick` (if not using native Scheduler)
* `bin/console audit:rules:list`
* `bin/console audit:replay-webhooks {runId}`

---

## 19) Example Components (Code Sketches)

**Messenger messages**

```php
final class CrawlPageMessage { public function __construct(public string $runId, public string $url) {} }
final class RunLighthouseMessage { public function __construct(public string $runId, public string $url) {} }
final class AnalyzePageMessage { public function __construct(public string $runId, public string $pageId) {} }
final class AggregateAuditMessage { public function __construct(public string $runId) {} }
final class GenerateReportMessage { public function __construct(public string $runId, public string $format) {} }
final class EmitWebhookMessage { public function __construct(public string $runId, public string $event) {} }
```

**Service interfaces**

```php
interface Crawler { public function fetch(string $url, array $opts = []): FetchedResource; }
interface Analyzer { public function analyze(string $runId, Page $page): array /* findings */; }
interface Scorer   { public function score(string $runId): Scorecard; }
interface Reporter { public function build(string $runId, string $format): ReportRef; }
```

**messenger.yaml (excerpt)**

```yaml
framework:
  messenger:
    transports:
      crawl: '%env(MESSENGER_TRANSPORT_DSN_CRAWL)%'
      lighthouse: '%env(MESSENGER_TRANSPORT_DSN_LIGHTHOUSE)%'
      analyze: '%env(MESSENGER_TRANSPORT_DSN_ANALYZE)%'
      aggregate: '%env(MESSENGER_TRANSPORT_DSN_AGG)%'
      report: '%env(MESSENGER_TRANSPORT_DSN_REPORT)%'
      notify: '%env(MESSENGER_TRANSPORT_DSN_NOTIFY)%'
    routing:
      'App\\Message\\CrawlPageMessage': crawl
      'App\\Message\\RunLighthouseMessage': lighthouse
      'App\\Message\\AnalyzePageMessage': analyze
      'App\\Message\\AggregateAuditMessage': aggregate
      'App\\Message\\GenerateReportMessage': report
      'App\\Message\\EmitWebhookMessage': notify
```

**Entity sketch (AuditRun)**

```php
#[ORM\Entity]
class AuditRun {
  #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'uuid')] private Uuid $id;
  #[ORM\ManyToOne] private Audit $audit;
  #[ORM\Column(enumType: AuditRunState::class)] private AuditRunState $state;
  #[ORM\Column(type: 'json')] private array $config = [];
  #[ORM\Column(nullable: true)] private ?\DateTimeImmutable $startedAt = null;
  #[ORM\Column(nullable: true)] private ?\DateTimeImmutable $finishedAt = null;
  // ... getters/setters
}
```

**Rules engine pattern**

```php
interface CheckRule { public function code(): string; public function run(PageContext $ctx): FindingResult; }
final class TitleLengthRule implements CheckRule { /* ... */ }
// Registry composes all CheckRule services via autoconfiguration + tags
```

---

## 20) Rollout Plan

1. **MVP (4–6 weeks):** crawl + on‑page/technical basics, PSI integration, scoring/report JSON, HTML report, manual trigger.
2. **v1:** scheduling, PDF export, webhooks, diffs between runs, Lighthouse sampling.
3. **v1.1:** GSC connector, backlink vendor adapters, RLS hardened, CSV exports.
4. **v1.2:** Accessibility enhancements, custom check authoring per tenant, UI hooks.

---

## 21) Risk Register & Mitigations

* **Quota limits (PSI/GSC):** cache + batch + exponential backoff, per‑tenant quotas.
* **Heavy pages / memory:** stream to disk, size caps, parse selectively.
* **Robots false positives:** log agent name, provide override per audit.
* **Lighthouse resource cost:** sample strategy (top templates + highest traffic pages if GSC available).
* **Multi‑tenant leakage:** enforce RLS in tests, security review before GA.

---

## 22) Acceptance Criteria (v1)

* Can run an audit on a 1k‑page site in < 45 minutes with default sampling, using 10 workers.
* Report shows ≥ 30 distinct checks with evidence and remediation hints.
* Re‑running on unchanged site yields identical scores (±1%).
* Webhook is delivered with HMAC signature and presigned report URLs.
