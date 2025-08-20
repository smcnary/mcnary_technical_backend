# CounselRank.legal — Frontend Wireframes, UX, Business Rules & API Map (v1)

## Scope
Public marketing site + Client portal. Attribute-based backend from prior spec. All endpoints are under `/api/v1`.

---

## Global UI Foundations

### Typography & Layout
- Clean, high-contrast headings
- Max content width 1200px
- Comfortable line-height
- Generous whitespace

### Color & Theme
- Professional legal palette (navy/ink, slate, white) with accent (teal/cyan)
- High-contrast states for accessibility (WCAG AA+)

### Navigation
- Sticky header
- Skip-to-content link
- Responsive drawer on mobile

### Feedback
- Toasts for success/info/error
- Inline validation
- Skeleton loaders
- Empty states

### A11y
- Landmarks (header/nav/main/aside/footer)
- Focus rings
- Proper aria-* for disclosure/accordion, dialogs, and charts

### State Management
- Query caching for lists
- Optimistic updates where safe (lead notes, task status)
- Suspense-friendly loaders

---

## Public Site

### 1) Header / Top Navigation

```
+-----------------------------------------------------------------------+
| Logo        Services  Pricing  Case Studies  Blog  FAQ  About  Contact|
|                                                 [Client Login] [CTA]  |
+-----------------------------------------------------------------------+
```

**UX**
- Desktop mega-menu for Services; mobile hamburger → full-screen drawer
- CTA = "Book Demo" opens modal scheduler or scrolls to contact form

**Business Rules**
- Highlight current page
- Show "Client Login" only if not authenticated

**Endpoints**
- None (static). If showing dynamic menu: `GET /pages?slug=...` for CMS-driven nav labels

### 2) Hero + Primary CTA

```
+---------------------------------------------------------------+
|  Headline: Legal SEO that wins cases                          |
|  Subhead: Local + AI-first SEO for law firms                  |
|  [Book Demo]  [See Pricing]                                   |
|  Trust badges  ★★★★★  (Google, Clutch, etc.)                  |
+---------------------------------------------------------------+
```

**UX**
- Primary CTA sticky on mobile
- Trust badges collapse into carousel on small screens

**Business Rules**
- CTA logs marketing event; if user is authenticated (client), change CTA → "Open Portal"

**Endpoints**
- None; trust metrics optional via `GET /reviews?client_id=AGENCY&platform=Google` (aggregated badges)

### 3) Services Overview

```
+------------------+  +------------------+  +------------------+
| Local SEO        |  | Content & AEO    |  | GBP & Reviews    |
| bullets...       |  | bullets...       |  | bullets...       |
| [Learn more]     |  | [Learn more]     |  | [Learn more]     |
+------------------+  +------------------+  +------------------+
```

**UX**
- Cards with concise bullets and iconography
- Hover → subtle lift; keyboard focus visible

**Business Rules**
- Content editable via CMS or DB; order by priority

**Endpoints**
- `GET /packages` (for mapping into detailed pricing later)
- Optional CMS pages: `GET /pages?slug=services-local-seo` etc.

### 4) Pricing (Packages)

```
+-----------+     +-----------+     +-----------+
| Starter   |     | Growth    |     | Premium   |
| $3k/mo    |     | $6k/mo    |     | $12k/mo   |
| ✓ features|     | ✓         |     | ✓         |
| [Select]  |     | [Select]  |     | [Select]  |
+-----------+     +-----------+     +-----------+
```

**UX**
- Highlight middle plan
- Toggle monthly/annual
- Feature comparison table

**Business Rules**
- Selecting plan → opens contact/lead capture with chosen `package_id`

**Endpoints**
- `GET /packages`
- (Portal/Billing later) `POST /subscriptions`

### 5) FAQ

```
FAQ
[▸] How long until results?
[▾] What is AEO/GEO?
      Answer text…
[▸] Do you work with PI firms?
```

**UX**
- Keyboard-friendly accordion
- Deep-link to a specific question via hash

**Business Rules**
- Ordered by `order_index`

**Endpoints**
- `GET /faqs`

### 6) About

```
+-----------------------------------------------------+
| Mission, team photos, values, process steps         |
| Timeline of wins, certifications, partnerships      |
+-----------------------------------------------------+
```

**UX**
- Rich content from CMS
- Light/dark friendly images

**Business Rules**
- Editable via Page entity

**Endpoints**
- `GET /pages?slug=about`

### 7) Explainer Video Section

```
+-------------------------------+
|  [▶] 16:9 video placeholder  |
|  Caption & key takeaways      |
+-------------------------------+
```

**UX**
- Lazy-load
- Transcript toggle for accessibility & SEO

**Business Rules**
- Track play/complete events

**Endpoints**
- `GET /media-assets/:id`

### 8) Contact / Lead Capture

```
+----------------------------------------+
|  Name  [__________]                    |
|  Email [__________]                    |
|  Phone [__________]                    |
|  Firm   [__________]  Practice [v]     |
|  Message [__________________________]  |
|  [ I agree to terms ]                  |
|  [ I'm interested in: plan radios ]    |
|  hCaptcha                              |
|  [Submit]                              |
+----------------------------------------+
```

**UX**
- Real-time validation
- Success screen with calendar link
- Privacy/consent copy

**Business Rules**
- Required: name+one of (email, phone), consent checkbox, hCaptcha
- UTM captured from querystring + referrer and stored on Lead

**Endpoints**
- `POST /leads` (Processor validates hCaptcha, enriches UTM, optional `package_id`)

### 9) Footer
- Quick links, address, social, newsletter signup
- Newsletter → `POST /newsletter-subs` (if added later) or marketing tool

---

## Client Portal

### Layout & Navigation

```
+-----------------+----------------------------------------------+
| Logo            |  Topbar: Client Switcher  Search  Profile    |
| Dashboard       |----------------------------------------------|
| Leads           |  [View renders here]                         |
| Reviews         |                                              |
| Keywords        |                                              |
| Rankings        |                                              |
| Content         |                                              |
| Audits          |                                              |
| Recs/Tasks      |                                              |
| Backlinks       |                                              |
| Citations        |                                              |
| Billing         |                                              |
| Settings        |                                              |
+-----------------+----------------------------------------------+
```

**UX**
- Client switcher dropdown (agency roles only)
- Keyboard shortcuts (/, g l, g r, etc.)
- Persistent filters per view (URL querystring-based)

**Business Rules**
- RBAC: CLIENT_* see only their `client_id`; AGENCY_* can switch

**Endpoints**
- Mostly below per module; global `/me`, `/clients`, `/clients/:id/locations` for context

### A) Auth (Login / Reset)

```
[ Email ] [ Password ] ( Show )        [ Log in ]
Forgot password? → email reset flow
```

**Business Rules**
- JWT on success; refresh token via `/auth/refresh`

**Endpoints**
- `POST /auth/login`
- `POST /auth/refresh`
- `POST /auth/logout`

### B) Dashboard

```
+----- Rank Summary -----+  +----- Leads -----+  +----- Reviews -----+
| Avg Pos | Top 3 | Δ    |  | New | Won | CR  |  | ★ Avg | New | Δ  |
+------------------------+  +-----------------+  +-------------------+
| Trend chart (30 days)  |  | Recent items…   |  | Latest reviews…   |
+------------------------+  +-----------------+  +-------------------+
```

**UX**
- Time range picker
- Cards link to detailed modules

**Business Rules**
- Compute via materialized views if heavy

**Endpoints**
- `GET /rankings/summary?client_id=`
- `GET /leads?client_id=&status=new`
- `GET /reviews?client_id=`

### C) Leads

```
Filters: Status [All|New|Qualified|Won|Lost|Spam]  Source [All|...]  Search
--------------------------------------------------------------------------
| Name        | Source  | Status   | Created        | Last Activity     |
--------------------------------------------------------------------------
| John Doe    | Webform | New      | 2025-08-12     | Note by Alice…    |

[ Lead Detail Drawer ]
Name / contact / message
Timeline (events): notes, calls, emails
[Change Status] [Add Note]
```

**Business Rules**
- Status transitions allowed: new→qualified/contacted→won/lost/spam
- PII masking for staff without permission

**Endpoints**
- `GET /leads?client_id=&status=`
- `PATCH /leads/:id` (status updates)
- `POST /leads/:id/events`

### D) Reviews (GBP/Yelp)

```
Filters: Platform [Google|Yelp]  Rating [1-5]  Has Response [Yes/No]
-------------------------------------------------------------------
| ★ | Author      | Snippet                         | Posted   | Reply |
-------------------------------------------------------------------
| 5 | Jane Client | "Great help after accident…"    | 2025-08… | Reply |

[ Reply Modal ] textarea + suggested templates  [Send]
```

**Business Rules**
- If GBP OAuth connected, allow reply; otherwise disable and prompt connect

**Endpoints**
- `GET /reviews?client_id=&platform=`
- `POST /reviews/:id/respond`
- `POST /reviews/sync`

### E) Keywords & Rankings

```
[+ Add Keywords]  Location: [Tulsa, OK]
List
-------------------------------------------------
| Phrase                         | Target URL    |
-------------------------------------------------
| tulsa personal injury lawyer   | /pi/tulsa     |

[ Keyword Detail ]
Line chart: Position by day  • Impressions • Clicks
Date range picker
```

**Business Rules**
- Deduplicate per (client, phrase, location)
- Daily inserts unique per (keyword_id, date)

**Endpoints**
- `GET /keywords?client_id=`
- `POST /keywords` (bulk supported)
- `GET /rankings?keyword_id=&from=&to=`

### F) Content (Items & Briefs)

```
Tabs: List | Calendar | Kanban (Draft → Review → Published)
-----------------------------------------------------------------
| Title                         | Type  | Status     | Publish |
-----------------------------------------------------------------
| 5 Tips After a Car Accident   | Blog  | Published  | 2025-08 |

[ Drawer ]
Metadata, target keyword/location, internal links
[Open Brief] [Edit Body]
```

**Business Rules**
- Status workflow: draft→brief→in_progress→review→published
- Publish dates in future → queued publish job

**Endpoints**
- `GET /content-items?client_id=&status=`
- `POST /content-items`
- `PATCH /content-items/:id`
- `GET /content-briefs?content_item_id=`
- `POST /content-briefs`

### G) Audits & Recommendations

```
[Run Audit] Tool: [Lighthouse|Crawler]
-----------------------------------------------------------
| Started        | Tool        | Issues (H/M/L) | Status  |
-----------------------------------------------------------
| 2025-08-12 10:00| Lighthouse | 3/5/12         | Done    |

[ Findings List ]
Severity chip • Code • Page URL • Title  [Create Task]

[ Recommendations ]
| Title                                 | Owner | Due | Status |
```

**Business Rules**
- Running audit enqueues job; findings are immutable snapshots
- Recommendations can be created from a finding or standalone

**Endpoints**
- `POST /audits/run`
- `GET /audit-runs?client_id=`
- `GET /audit-findings?audit_run_id=`
- `GET /recommendations?client_id=&status=`
- `PATCH /recommendations/:id`

### H) Backlinks

```
[ Import CSV ]  Filter: Follow [Any|Yes|No]  DR ≥ [ 40 ]
-----------------------------------------------------------------
| Source URL                     | Anchor         | DR | Follow |
-----------------------------------------------------------------
| https://legalblog.com/a...     | PI Tulsa       | 62 |  Yes   |
```

**Business Rules**
- Import validates URL format; optional dedupe by (client, source_url)

**Endpoints**
- `GET /backlinks?client_id=`
- `POST /backlinks`
- `POST /backlinks/import`

### I) Citations

```
Filter: Platform [All|Google|Yelp|Avvo|BBB]
----------------------------------------------------------------
| Platform | Listing URL                     | Status   |
----------------------------------------------------------------
| Google   | https://g.page/firm...          | Claimed  |
```

**Business Rules**
- Unique per (client, platform). Status flow: unclaimed→pending→claimed

**Endpoints**
- `GET /citations?client_id=`
- `POST /citations`

### J) Billing

```
Subscription: Growth ($6,000/mo)  Status: Active
[Change Plan]
Invoices
-------------------------------------------
| Date       | Amount   | Status | Download |
-------------------------------------------
```

**Business Rules**
- Plan changes respect proration (Stripe); invoices link to PDF

**Endpoints**
- `GET /packages`
- `GET /subscriptions?client_id=`
- `POST /subscriptions`
- `GET /invoices?subscription_id=`
- Webhooks: `POST /webhooks/stripe`

### K) Settings

```
Tabs: Profile | Users | Locations | Integrations

Profile: name, email, password
Users: invite user (role: CLIENT_ADMIN/CLIENT_STAFF)
Locations: offices list + map
Integrations: Connect Google (GBP/SC/GA4), Stripe
```

**Business Rules**
- Only CLIENT_ADMIN can manage users/integrations
- OAuth connections unique per (client, provider)

**Endpoints**
- `GET /clients/:id`
- `GET /clients/:id/locations` • `POST /clients/:id/locations`
- `GET /users` • `POST /users` • `PATCH /users/:id`
- `GET /oauth-connections` (if exposed) or derived from client context

---

## Analytics & Telemetry (frontend)
- `lead_submitted`, `plan_selected`, `video_played`, `faq_opened`
- Portal: `lead_status_changed`, `review_replied`, `keyword_added`, `audit_run_started`, `recommendation_completed`, `invoice_downloaded`

## Performance Targets
- LCP < 2.5s, INP < 200ms, CLS < 0.1 on key pages
- Images lazy-loaded; prefetch next-route data; cache API GETs

## Security & Privacy (UI)
- Mask PII in tables by default; reveal on hover with permission
- CSRF for cookie flows; JWT in Authorization header for API

---

## Appendix: Endpoint Index (mentioned above)

### Auth
- `POST /auth/login`, `/auth/refresh`, `/auth/logout`

### Me/Users/Clients
- `GET /me`, `GET/POST/PATCH /users`, `GET/POST/PATCH /clients`, `GET /clients/:id/locations`, `POST /clients/:id/locations`

### Public Content
- `GET /pages`, `GET /faqs`, `GET /packages`, `GET /content-items`, `GET /media-assets/:id`

### Leads
- `GET/PATCH /leads`, `POST /leads`, `POST /leads/:id/events`

### Campaign/Keywords/Rankings
- `GET/POST /campaigns`, `GET/POST /keywords`, `GET /rankings`, `GET /rankings/summary`

### Reviews/Citations
- `GET /reviews`, `POST /reviews/:id/respond`, `POST /reviews/sync`, `GET /citations`, `POST /citations`

### Content
- `GET/POST /content-items`, `PATCH /content-items/:id`, `GET/POST /content-briefs`

### Audits/Recommendations
- `POST /audits/run`, `GET /audit-runs`, `GET /audit-findings`, `GET /recommendations`, `PATCH /recommendations/:id`

### Backlinks
- `GET /backlinks`, `POST /backlinks`, `POST /backlinks/import`

### Billing
- `GET /packages`, `GET/POST /subscriptions`, `GET /invoices`, `POST /webhooks/stripe`

### Uploads
- `POST /uploads/sign`, `POST /media-assets`
