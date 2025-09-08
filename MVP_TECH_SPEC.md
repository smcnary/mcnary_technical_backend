# MVP Tech Spec — Tulsa-SEO (AI-First Local SEO Auditor)

## 1) Product Vision & Scope

**Problem:** Local SMBs (esp. law firms) struggle to understand and prioritize SEO work.

**Solution (MVP):** A self-serve audit + lightweight rank tracker + simple client dashboard that turns SEO best practices into a prioritized, dollar-impact to-do list — "run an audit, pick a tier, watch your numbers move."

### MVP Outcomes
- User completes an Audit Wizard (domain + business info) and gets:
  - Site Health Score (0–100) with prioritized issues
  - Top 5 quick wins (fix time + impact)
  - Keyword snapshot (up to 10 tracked terms; basic mobile/geo)
- Client Dashboard shows: Health, Issues, Keywords, PageSpeed/Core Web Vitals summaries, and a weekly email report.
- Checkout: tiered subscription, card on file, invoice history.
- Multi-tenant: Agency → Clients; or Direct SMBs.

---

## 2) User Roles & Permissions

- **Platform Admin:** global settings, billing plans, support tools.
- **Agency Admin:** manage agency account, invite staff, manage client workspaces.
- **Client Owner:** manage their site, billing, keywords, audits.
- **Client Member:** read-only dashboard + mark items complete.

**Role mapping (RBAC):** `ROLE_ADMIN`, `ROLE_AGENCY_ADMIN`, `ROLE_CLIENT_OWNER`, `ROLE_CLIENT_MEMBER`.

---

## 3) Core Features (MVP)

### A. Audit Wizard (Self-Serve)

**Inputs:**
- Domain, business name/category, city/zip, competitors (optional), 10 seed keywords.
- Google Search Console connect (optional, OAuth).

**Process:**
- Crawl up to 200 internal pages (respect robots.txt; concurrent queue).
- **Checks (examples):**
  - **Technical:** status codes, canonical, robots, sitemap, indexability, CWV (via PageSpeed API), SSL/HSTS, mobile-friendly, redirect chains, duplicate titles/H1s.
  - **On-Page:** title/meta length & uniqueness, H1 presence, word count, basic E-E-A-T signals (entity presence), schema.org presence (Org/LocalBusiness/FAQ/Article), image alt coverage.
  - **Local:** NAP consistency on site, presence of LocalBusiness schema, embedded map, location pages, GMB link (manual input for MVP).
  - **Content Gaps:** compare seed keywords vs. titles/H1s; find missing target pages.

**Scoring:**
- Weighted rubric per category → Site Health Score (0–100).
- Each issue gets severity (P1/P2/P3), effort (S/M/L), impact score, and suggested fix.

**Output:**
- Audit report saved; dashboard cards updated.
- Top 5 quick wins list (auto-generated).

### B. Rank Tracker (Lightweight)
- Track up to 10 keywords per site; weekly refresh.
- Location: user-selected city + 25-mile radius (MVP: city-level).
- Device: desktop + mobile (mobile optional in MVP if cost-sensitive).
- Third-party provider adapter (e.g., SerpApi / DataForSEO). Pluggable.

### C. Client Dashboard
- **Health Overview:** score trend, last audit date, pages crawled.
- **Issues:** sortable table (category, severity, page count) + mark resolved.
- **Keywords:** rank table with trend arrows; simple chart.
- **Performance:** PageSpeed (field/lab from PSI) for home + top 5 pages.
- **Reports:** one-click PDF export; weekly email summary.

### D. Billing & Plans
**Tiers (example):**
- **Starter:** 1 site, 200-page crawl, 10 keywords, weekly ranks.
- **Growth:** 3 sites, 1k-page crawl, 50 keywords, twice-weekly ranks.
- **Agency:** 10+ sites, 5k-page crawl, 200 keywords, daily ranks.
- Stripe subscriptions, customer portal, invoices, webhooks (invoices paid/failed).

### E. Notifications
- Weekly email: Health score delta, new critical issues, keyword winners/losers.
- Optional SMS for "score dropped >10" (Twilio) — Growth+.

---

## 4) Non-Functional Requirements

- **Performance:** audit queue processes 200-page crawl < 10 minutes per site (parallelized workers).
- **Reliability:** retries for HTTP errors; idempotent jobs; dead-letter queue.
- **Security:** JWT, tenant isolation by organization_id/client_id; rate-limit public endpoints; secret rotation.
- **Compliance:** consent for site crawling; honor robots.txt; store 12 months of rank history.
- **Telemetry:** request logs, job metrics, error rates, external API spend.

---

## 5) Architecture

### Backend
- Symfony 7.3, PHP 8.2+, API Platform 4.1
- PostgreSQL 16, Doctrine ORM 3.5
- Redis (cache, rate-limit), Symfony Messenger with RabbitMQ (or Redis Streams) for jobs
- HTTP Client for crawling & PSI; Provider adapters for rank APIs
- JWT Auth (LexikJWT), CORS (Nelmio)
- OpenAPI auto-docs

### Frontend
- Next.js 14 (App Router) + React 18
- Tailwind CSS + shadcn/ui + lucide-react
- State: Zustand (simple)
- Charts: recharts
- Forms: react-hook-form + zod

### Integrations
- Google PageSpeed Insights API (CWV & lab data)
- SerpApi/DataForSEO (rank tracking)
- Stripe (subscriptions, billing portal, webhooks)
- Postmark/SendGrid (transactional email)
- Twilio (SMS, later tier)

### Environments & CI/CD
- Docker Compose for dev; GH Actions CI (lint, static analysis, unit tests, E2E)
- Deploy: Fly.io or Render for speed, or AWS ECS/Fargate
- Secrets via GitHub OIDC → cloud secret manager
- DB migrations via Doctrine Migrations

---

## 6) Data Model (MVP)

### Core Entities
- `Organization(id, name, plan, stripe_customer_id, created_at)`
- `Agency(id, organization_id, name)`
- `Client(id, organization_id, agency_id?, name, domain, city, state, country)`
- `User(id, organization_id, email, password_hash?, role, status, last_login_at)`
- `Subscription(id, organization_id, plan, status, stripe_subscription_id, current_period_end)`
- `Audit(id, client_id, started_at, finished_at, pages_crawled, health_score)`
- `AuditIssue(id, audit_id, category, severity, effort, impact, title, description, fix_hint, affected_pages_count, status)`
- `Page(id, client_id, url, last_crawled_at, status_code, indexable, canonical, word_count)`
- `CheckResult(id, page_id, check_key, passed, data_json, severity)`
- `Keyword(id, client_id, phrase, location, device)`
- `KeywordRank(id, keyword_id, position, url, search_volume?, captured_at)`
- `Integration(id, client_id, provider, access_token?, refresh_token?, meta_json)`
- `WebhookEvent(id, provider, type, payload_json, received_at)`
- `Job(id, type, payload_json, status, attempts, last_error?, created_at, processed_at)`

*(Use UUIDv4 for ids; composite unique constraints where needed, e.g., (client_id, phrase, location, device).)*

---

## 7) API Endpoints (MVP)

### Auth
- `POST /auth/login` → JWT
- `POST /auth/register` → create org + owner
- `POST /auth/invite` (agency/client scope)

### Clients & Setup
- `GET /clients` / `POST /clients`
- `POST /clients/{id}/start-audit` (enqueue crawl+checks)
- `GET /clients/{id}/audits?limit=1` (latest)

### Audit Data
- `GET /audits/{id}`
- `GET /audits/{id}/issues`
- `PATCH /audit-issues/{id}` (mark resolved/ignored)
- `GET /clients/{id}/pages?limit=...`

### Keywords
- `GET /clients/{id}/keywords`
- `POST /clients/{id}/keywords` (bulk add up to 10)
- `GET /keywords/{id}/ranks?range=90d`

### Performance
- `GET /clients/{id}/performance` (PSI snapshots for top pages)

### Billing
- `GET /billing/portal-link` (Stripe portal)
- Webhooks: `POST /webhooks/stripe`

### Integrations
- `GET /integrations/gsc/oauth-url`
- `POST /integrations/gsc/callback`

---

## 8) Audit Engine — Checks & Scoring

### Weights (example total 100):
- **Technical 40** (indexability 12, CWV 10, HTTPS/HSTS 4, mobile 6, status/redirects 8)
- **On-Page 35** (titles 10, metas 6, H1 6, word count 5, schema 8)
- **Local 25** (LocalBusiness schema 8, NAP on site 7, location page 5, GMB link 5)

### Scoring Method:
- Each check returns pass / fail / partial + affected_pages_count.
- Deduct weighted points proportionally to coverage (e.g., 40% pages missing H1 → 0.4 × H1 weight).
- Cap deductions per category; floor score at 0; round to integer.

**Quick Wins Ranking** = impact_score / effort_factor, tie-break by affected_pages_count.

---

## 9) Crawling Strategy (MVP)
- Seed with home page; in-domain only; max 200 URLs; BFS with normalization.
- Respect robots.txt; honor nofollow if present.
- Fetch with Symfony HTTP Client; parse with DOMDocument + XPath.
- Rate limit: max 4 concurrent per host; global concurrency via Messenger workers.
- Store raw HTML hash for change detection (avoid big blobs: keep optional).

---

## 10) Third-Party Providers (Adapter Pattern)

Create `RankProviderInterface` with implementations:
- `SerpApiProvider` (default; set API key per org)
- `DataForSEOProvider` (optional)

Switch by org setting. Include usage metering per call to enforce plan caps.

---

## 11) UI/UX (MVP Screens)

### Public Marketing
- Hero (AI-First SEO for Local Businesses), Pricing, Case-study tiles (static for MVP), CTA to run audit.

### App
- **Onboarding Wizard** (domain → business info → keywords → plan → payment)
- **Dashboard**
  - Score card + sparkline
  - Quick wins list (5)
  - Issues summary by category (pill chips)
  - Keyword table (position, URL, trend)
  - Performance cards (PSI for 6 pages)
- **Issues View**
  - Filters: category/severity/status
  - Bulk mark resolved; per-issue fix hints
- **Keywords**
  - Add/edit; trend chart (90 days)
- **Settings**
  - Business profile, location, integrations, billing portal

**Design system:** Tailwind + shadcn cards, rounded-2xl, grid layout, minimal color cues (severity badges), accessible contrasts.

---

## 12) Email & Reporting
- **Weekly Summary** (to owners): score delta, new P1s, top keyword movers, 1 recommended action.
- **PDF Export** of latest audit (server render with React PDF or Headless Chrome in a job).

---

## 13) Plan & Usage Limits (Enforced)
- Sites per plan, pages per audit, keywords per site, rank frequency.
- Block actions or queue next cycle when over limit; surface upsell prompts.

---

## 14) Logging, Monitoring, Alerts
- App logs (JSON) → ELK or cloud logs.
- Metrics: jobs processed, avg crawl time, API spend, error rates.
- Alerts: job failures > threshold, rank provider errors, Stripe webhook failures.

---

## 15) Security
- JWT with short TTL + refresh.
- Per-request tenant scoping (doctrine filters or explicit queries).
- Input validation (zod front, Symfony validators back).
- Rate limiting on public endpoints; CSRF for non-API forms if any.
- Store provider keys per organization in encrypted secrets table or KMS.

---

## 16) Delivery Plan — Epics & Milestones

### Milestone 1 — Foundations (1–2 weeks)
- Project skeletons (Symfony, Next.js), auth, RBAC, multi-tenant schema
- Stripe basic subscription flow + webhooks
- CI/CD with migrations & smoke tests

### Milestone 2 — Audit Engine (2 weeks)
- Crawler + checks + scoring
- PSI integration
- Audit storage + Issues UI

### Milestone 3 — Rank Tracker (1–1.5 weeks)
- Provider adapter + weekly scheduler
- Keywords CRUD + trend chart

### Milestone 4 — Dashboard & Reports (1 week)
- Health overview, quick wins, performance cards
- Weekly email + PDF export

### Milestone 5 — Polish & Launch (1 week)
- Empty states, limits enforcement, analytics, onboarding flows
- Docs, support email, status page stub

---

## 17) Testing Strategy
- **Unit:** scoring, check evaluators, provider adapters (mock HTTP).
- **Integration:** crawl 5-page fixture site, PSI stub, Stripe webhook flow.
- **E2E (Playwright):** signup → audit → dashboard → add keywords → see ranks.
- **Load:** queue throughput with 50 concurrent audits.

---

## 18) Risks & Mitigations
- **Crawler complexity** → keep to HTML GET + parse; 200-page hard cap; later add sitemap parsing.
- **Rank provider cost** → strict quotas, batch weekly, show remaining credits.
- **CWV variance** → use PSI API and clearly label "lab vs field".
- **Multi-tenant leaks** → enforce org scoping at repo layer + API tests.

---

## 19) "Done" Criteria (MVP)
- A new SMB can sign up, pay, run an audit, and see a prioritized list with a Health Score and top quick wins.
- They can add up to 10 keywords and get ranks within the next scheduled run.
- They receive a weekly email summary.
- Agency can manage multiple clients under one subscription (or separate, per plan).
- Basic analytics, logging, and error alerts are active.

---

## 20) Project Structure

This MVP is implemented across three main services:

- **`/backend`** - Main Symfony API with authentication, billing, and core business logic
- **`/frontend`** - Next.js React application for the client dashboard and marketing site
- **`/audit-service`** - Dedicated Symfony microservice for crawling, auditing, and scoring

Each service has its own documentation and can be developed/deployed independently while sharing common data models and API contracts.
