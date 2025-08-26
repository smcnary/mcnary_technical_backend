# 🗄️ Database Schema

## 📋 Overview

This document provides a comprehensive view of the CounselRank.legal platform database schema, including all tables, columns, relationships, and constraints. The schema follows a multi-tenant architecture with proper normalization and security controls.

## 🏗️ Architecture Principles

- **Multi-tenant design** with `tenant_id` and `client_id` scoping
- **UUID primary keys** for security and scalability
- **Proper foreign key relationships** with cascade rules
- **Audit trails** with `created_at` and `updated_at` timestamps
- **Role-based access control** (RBAC) integration
- **JSON fields** for flexible metadata storage (PostgreSQL)

## 🔗 Entity Relationship Diagram

```
Organization (1) ←→ (N) Agency (1) ←→ (N) Client (1) ←→ (N) User
     ↓                    ↓                    ↓                    ↓
  Tenant              UserClientAccess    AuditIntake          AuditRun
     ↓                    ↓                    ↓                    ↓
  User                Lead/Campaign        AuditConversionGoal  AuditFinding
     ↓                    ↓                    ↓
  OAuthConnection    Keyword/Content      AuditCompetitor
     ↓                    ↓                    ↓
  OAuthToken         MediaAsset           AuditKeyword
```

## 📊 Core Tables

### 1. Organizations & Tenancy

#### `organizations`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Organization identifier |
| `name` | VARCHAR(255) | NOT NULL | Organization name |
| `slug` | VARCHAR(255) | UNIQUE | URL-friendly identifier |
| `status` | VARCHAR(32) | DEFAULT 'active' | Organization status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `tenants`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Tenant identifier |
| `organization_id` | UUID | FOREIGN KEY | Organization reference |
| `name` | VARCHAR(255) | NOT NULL | Tenant name |
| `domain` | VARCHAR(255) | UNIQUE | Tenant domain |
| `status` | VARCHAR(32) | DEFAULT 'active' | Tenant status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `agencies`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Agency identifier |
| `organization_id` | UUID | FOREIGN KEY | Organization reference |
| `name` | VARCHAR(255) | NOT NULL | Agency name |
| `slug` | VARCHAR(255) | UNIQUE | URL-friendly identifier |
| `description` | TEXT | NULLABLE | Agency description |
| `website_url` | VARCHAR(255) | NULLABLE | Agency website |
| `status` | VARCHAR(32) | DEFAULT 'active' | Agency status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

### 2. Client Management

#### `clients`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Client identifier |
| `agency_id` | UUID | FOREIGN KEY | Agency reference |
| `name` | VARCHAR(255) | NOT NULL | Client name |
| `slug` | VARCHAR(255) | UNIQUE | URL-friendly identifier |
| `description` | TEXT | NULLABLE | Client description |
| `website_url` | VARCHAR(255) | NULLABLE | Client website |
| `phone` | VARCHAR(255) | NULLABLE | Contact phone |
| `email` | VARCHAR(255) | NULLABLE | Contact email |
| `address` | VARCHAR(255) | NULLABLE | Physical address |
| `city` | VARCHAR(255) | NULLABLE | City |
| `state` | VARCHAR(255) | NULLABLE | State/Province |
| `postal_code` | VARCHAR(10) | NULLABLE | Postal code |
| `country` | VARCHAR(255) | NULLABLE | Country |
| `industry` | VARCHAR(255) | DEFAULT 'law' | Industry type |
| `status` | VARCHAR(32) | DEFAULT 'active' | Client status |
| `metadata` | JSONB | NULLABLE | Additional client data |
| `google_business_profile` | JSONB | NULLABLE | GBP configuration |
| `google_search_console` | JSONB | NULLABLE | GSC configuration |
| `google_analytics` | JSONB | NULLABLE | GA configuration |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `client_locations`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Location identifier |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `name` | VARCHAR(255) | NOT NULL | Location name |
| `address` | VARCHAR(255) | NOT NULL | Physical address |
| `city` | VARCHAR(255) | NOT NULL | City |
| `state` | VARCHAR(255) | NOT NULL | State/Province |
| `postal_code` | VARCHAR(10) | NOT NULL | Postal code |
| `country` | VARCHAR(255) | NOT NULL | Country |
| `phone` | VARCHAR(255) | NULLABLE | Location phone |
| `status` | VARCHAR(32) | DEFAULT 'active' | Location status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

### 3. User Management

#### `users`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | User identifier |
| `organization_id` | UUID | FOREIGN KEY | Organization reference |
| `agency_id` | UUID | FOREIGN KEY | Agency reference |
| `tenant_id` | UUID | FOREIGN KEY | Tenant reference |
| `client_id` | UUID | NULLABLE | Client reference |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | User email |
| `password_hash` | VARCHAR(255) | NULLABLE | Hashed password |
| `first_name` | VARCHAR(255) | NULLABLE | First name |
| `last_name` | VARCHAR(255) | NULLABLE | Last name |
| `status` | VARCHAR(32) | DEFAULT 'invited' | User status |
| `role` | VARCHAR(32) | NOT NULL | User role |
| `last_login_at` | TIMESTAMP | NULLABLE | Last login timestamp |
| `metadata` | JSONB | NULLABLE | Additional user data |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `user_client_access`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Access record identifier |
| `user_id` | UUID | FOREIGN KEY | User reference |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `permissions` | JSONB | NOT NULL | Access permissions |
| `status` | VARCHAR(32) | DEFAULT 'active' | Access status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

### 4. Audit System

#### `audit_intake`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY DEFAULT gen_random_uuid() | Intake identifier |
| `client_id` | UUID | FOREIGN KEY NOT NULL | Client reference |
| `requested_by_id` | UUID | FOREIGN KEY NULLABLE | User who requested |
| `website_url` | VARCHAR(255) | NOT NULL | Website to audit |
| `cms` | VARCHAR(64) | NOT NULL DEFAULT 'custom' | Content management system |
| `hosting_provider` | VARCHAR(128) | NULLABLE | Hosting provider |
| `tech_stack` | JSONB | NULLABLE | Technology stack details (e.g., {"framework":"Symfony 7","php":"8.3","db":"PostgreSQL 16"}) |
| `has_google_analytics` | BOOLEAN | NOT NULL DEFAULT false | GA integration flag |
| `has_search_console` | BOOLEAN | NOT NULL DEFAULT false | GSC integration flag |
| `has_google_business_profile` | BOOLEAN | NOT NULL DEFAULT false | GBP integration flag |
| `has_tag_manager` | BOOLEAN | NOT NULL DEFAULT false | GTM integration flag |
| `ga_property_id` | VARCHAR(255) | NULLABLE | Google Analytics property ID |
| `gsc_property` | VARCHAR(255) | NULLABLE | Search Console property |
| `gbp_location_ids` | JSONB | NULLABLE | GBP location IDs (e.g., ["123456789012345678901", ...]) |
| `gtm_container_id` | VARCHAR(255) | NULLABLE | GTM container ID |
| `markets` | JSONB | NULLABLE | Target markets (e.g., ["Tulsa, OK", "Broken Arrow, OK"]) |
| `primary_services` | JSONB | NULLABLE | Primary services offered (e.g., ["DUI", "Criminal Defense", ...]) |
| `target_audience` | JSONB | NULLABLE | Target audience metadata |
| `paid_channels` | JSONB | NULLABLE | Paid marketing channels (e.g., {"google_ads":true,"meta":false}) |
| `notes` | TEXT | NULLABLE | Additional notes |
| `status` | VARCHAR(24) | NOT NULL DEFAULT 'draft' | Intake status |
| `created_at` | TIMESTAMPTZ | NOT NULL DEFAULT now() | Creation timestamp |
| `updated_at` | TIMESTAMPTZ | NOT NULL DEFAULT now() | Last update timestamp |

**Foreign Key Constraints:**
- `client_id` → `client(id)` ON DELETE CASCADE
- `requested_by_id` → `user(id)` ON DELETE SET NULL

**Indexes:**
- `idx_audit_intake_client` ON `client_id`
- `idx_audit_intake_status` ON `status`
- `idx_audit_intake_website` ON `website_url`
- `idx_audit_intake_markets_gin` ON `markets` USING GIN
- `idx_audit_intake_services_gin` ON `primary_services` USING GIN

#### `audit_conversion_goal`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY DEFAULT gen_random_uuid() | Goal identifier |
| `intake_id` | UUID | FOREIGN KEY NOT NULL | Intake reference |
| `type` | VARCHAR(32) | NOT NULL DEFAULT 'form' | Goal type (form\|call\|purchase\|visit\|download\|other) |
| `kpi` | VARCHAR(128) | NOT NULL | Key performance indicator (e.g., "Leads per week") |
| `baseline` | DOUBLE PRECISION | NULLABLE | Current baseline value |
| `value_per_conversion` | NUMERIC(10,2) | NULLABLE | Value per conversion |

**Foreign Key Constraints:**
- `intake_id` → `audit_intake(id)` ON DELETE CASCADE

**Indexes:**
- `idx_goal_intake` ON `intake_id`

#### `audit_competitor`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY DEFAULT gen_random_uuid() | Competitor identifier |
| `intake_id` | UUID | FOREIGN KEY NOT NULL | Intake reference |
| `name` | VARCHAR(255) | NOT NULL | Competitor name |
| `website_url` | VARCHAR(255) | NULLABLE | Competitor website |

**Foreign Key Constraints:**
- `intake_id` → `audit_intake(id)` ON DELETE CASCADE

**Indexes:**
- `idx_competitor_intake` ON `intake_id`

#### `audit_keyword`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY DEFAULT gen_random_uuid() | Keyword identifier |
| `intake_id` | UUID | FOREIGN KEY NOT NULL | Intake reference |
| `phrase` | VARCHAR(255) | NOT NULL | Target keyword phrase |
| `intent` | VARCHAR(16) | NOT NULL DEFAULT 'local' | Search intent (informational\|transactional\|navigational\|local) |
| `priority` | SMALLINT | NOT NULL DEFAULT 3 | Priority level (1-5) |

**Foreign Key Constraints:**
- `intake_id` → `audit_intake(id)` ON DELETE CASCADE

**Indexes:**
- `idx_keyword_intake` ON `intake_id`
- `idx_keyword_priority` ON `priority`
- `idx_keyword_phrase` ON `phrase`

#### `audit_run`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY DEFAULT gen_random_uuid() | Run identifier |
| `client_id` | UUID | FOREIGN KEY NOT NULL | Client reference |
| `intake_id` | UUID | FOREIGN KEY NOT NULL | Intake reference |
| `initiated_by_id` | UUID | FOREIGN KEY NULLABLE | User who initiated |
| `status` | VARCHAR(16) | NOT NULL DEFAULT 'queued' | Run status (queued\|running\|completed\|failed) |
| `scope` | JSONB | NULLABLE | Audit scope configuration (e.g., {"technical":true,"onpage":true,...}) |
| `started_at` | TIMESTAMPTZ | NULLABLE | Start timestamp |
| `completed_at` | TIMESTAMPTZ | NULLABLE | Completion timestamp |
| `tool_versions` | JSONB | NULLABLE | Tool versions used in audit |
| `result_summary` | TEXT | NULLABLE | Summary of audit results |
| `totals` | JSONB | NULLABLE | Counts by severity/category, etc. |

**Foreign Key Constraints:**
- `client_id` → `client(id)` ON DELETE CASCADE
- `intake_id` → `audit_intake(id)` ON DELETE CASCADE
- `initiated_by_id` → `user(id)` ON DELETE SET NULL

**Indexes:**
- `idx_run_client` ON `client_id`
- `idx_run_intake` ON `intake_id`
- `idx_run_status` ON `status`
- `idx_run_started_at` ON `started_at`

#### `audit_finding`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY DEFAULT gen_random_uuid() | Finding identifier |
| `client_id` | UUID | FOREIGN KEY NOT NULL | Client reference |
| `audit_run_id` | UUID | FOREIGN KEY NOT NULL | Audit run reference |
| `title` | VARCHAR(255) | NOT NULL | Finding title |
| `description` | TEXT | NOT NULL | Finding description |
| `severity` | VARCHAR(16) | NOT NULL DEFAULT 'medium' | Issue severity (low\|medium\|high\|critical) |
| `status` | VARCHAR(16) | NOT NULL DEFAULT 'open' | Finding status (open\|in_progress\|resolved\|ignored) |
| `category` | VARCHAR(64) | NULLABLE | Finding category (technical\|onpage\|offpage\|local\|analytics) |
| `location` | VARCHAR(255) | NULLABLE | Issue location (URL, template, "GBP", etc.) |
| `impact` | TEXT | NULLABLE | Business impact |
| `recommendation` | TEXT | NULLABLE | Recommended fix |
| `impact_score` | SMALLINT | NOT NULL DEFAULT 3 | Impact score (1-5) |
| `effort_score` | SMALLINT | NOT NULL DEFAULT 3 | Effort score (1-5) |
| `priority_score` | SMALLINT | NOT NULL DEFAULT 3 | Priority score (1-5) |

**Foreign Key Constraints:**
- `client_id` → `client(id)` ON DELETE CASCADE
- `audit_run_id` → `audit_run(id)` ON DELETE CASCADE

**Indexes:**
- `idx_finding_run` ON `audit_run_id`
- `idx_finding_client` ON `client_id`
- `idx_finding_severity` ON `severity`
- `idx_finding_status` ON `status`
- `idx_finding_category` ON `category`
- `idx_finding_priority` ON `priority_score DESC`

### 5. Marketing & Content

#### `leads`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Lead identifier |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `name` | VARCHAR(255) | NOT NULL | Lead name |
| `email` | VARCHAR(255) | NOT NULL | Lead email |
| `phone` | VARCHAR(255) | NULLABLE | Lead phone |
| `company` | VARCHAR(255) | NULLABLE | Company name |
| `message` | TEXT | NOT NULL | Lead message |
| `source` | VARCHAR(64) | NULLABLE | Lead source |
| `status` | VARCHAR(32) | DEFAULT 'new' | Lead status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `campaigns`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Campaign identifier |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `name` | VARCHAR(255) | NOT NULL | Campaign name |
| `description` | TEXT | NULLABLE | Campaign description |
| `type` | VARCHAR(64) | NOT NULL | Campaign type |
| `status` | VARCHAR(32) | DEFAULT 'active' | Campaign status |
| `start_date` | DATE | NULLABLE | Start date |
| `end_date` | DATE | NULLABLE | End date |
| `budget` | DECIMAL(10,2) | NULLABLE | Campaign budget |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `keywords`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Keyword identifier |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `phrase` | VARCHAR(255) | NOT NULL | Target keyword |
| `intent` | VARCHAR(32) | DEFAULT 'informational' | Search intent |
| `difficulty` | SMALLINT | NULLABLE | Keyword difficulty (1-100) |
| `search_volume` | INTEGER | NULLABLE | Monthly search volume |
| `status` | VARCHAR(32) | DEFAULT 'active' | Keyword status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `content_items`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Content identifier |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `title` | VARCHAR(255) | NOT NULL | Content title |
| `content` | TEXT | NOT NULL | Content body |
| `type` | VARCHAR(64) | NOT NULL | Content type |
| `status` | VARCHAR(32) | DEFAULT 'draft' | Content status |
| `seo_title` | VARCHAR(255) | NULLABLE | SEO title |
| `meta_description` | TEXT | NULLABLE | Meta description |
| `slug` | VARCHAR(255) | UNIQUE | URL slug |
| `published_at` | TIMESTAMP | NULLABLE | Publication date |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

### 6. OAuth & Integrations

#### `oauth_connections`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Connection identifier |
| `client_id` | UUID | FOREIGN KEY | Client reference |
| `provider` | VARCHAR(64) | NOT NULL | OAuth provider |
| `provider_user_id` | VARCHAR(255) | NOT NULL | Provider user ID |
| `access_token` | TEXT | NOT NULL | Access token |
| `refresh_token` | TEXT | NULLABLE | Refresh token |
| `expires_at` | TIMESTAMP | NULLABLE | Token expiration |
| `scope` | JSON | NULLABLE | OAuth scopes |
| `status` | VARCHAR(32) | DEFAULT 'active' | Connection status |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Last update timestamp |

#### `oauth_tokens`
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Token identifier |
| `connection_id` | UUID | FOREIGN KEY | OAuth connection reference |
| `token_type` | VARCHAR(32) | NOT NULL | Token type |
| `token_value` | TEXT | NOT NULL | Token value |
| `expires_at` | TIMESTAMP | NULLABLE | Expiration timestamp |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |

## 🗄️ SQL Table Creation

### Database Setup

```sql
-- Enable UUID generation extension
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- NOTE: This assumes existing tables:
--   "user"(id uuid primary key)
--   client(id uuid primary key)
-- If your identifiers differ, adjust FK references below.
```

### Complete SQL Schema

```sql
-- ==========================
-- AUDIT INTAKE
-- ==========================
CREATE TABLE IF NOT EXISTS audit_intake (
  id                  uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  client_id           uuid NOT NULL,
  requested_by_id     uuid NULL,
  website_url         varchar(255) NOT NULL,
  cms                 varchar(64)   NOT NULL DEFAULT 'custom',
  hosting_provider    varchar(128),
  tech_stack          jsonb,                       -- {"framework":"Symfony 7","php":"8.3","db":"PostgreSQL 16"}
  has_google_analytics      boolean NOT NULL DEFAULT false,
  has_search_console        boolean NOT NULL DEFAULT false,
  has_google_business_profile boolean NOT NULL DEFAULT false,
  has_tag_manager           boolean NOT NULL DEFAULT false,
  ga_property_id      varchar(255),
  gsc_property        varchar(255),
  gbp_location_ids    jsonb,                       -- ["123456789012345678901", ...]
  gtm_container_id    varchar(255),
  markets             jsonb,                       -- ["Tulsa, OK", "Broken Arrow, OK"]
  primary_services    jsonb,                       -- ["DUI", "Criminal Defense", ...]
  target_audience     jsonb,                       -- arbitrary audience metadata
  paid_channels       jsonb,                       -- {"google_ads":true,"meta":false}
  notes               text,
  status              varchar(24) NOT NULL DEFAULT 'draft',
  created_at          timestamptz NOT NULL DEFAULT now(),
  updated_at          timestamptz NOT NULL DEFAULT now(),
  CONSTRAINT fk_audit_intake_client
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
  CONSTRAINT fk_audit_intake_requested_by
    FOREIGN KEY (requested_by_id) REFERENCES "user"(id) ON DELETE SET NULL
);

-- Helpful indexes
CREATE INDEX IF NOT EXISTS idx_audit_intake_client      ON audit_intake(client_id);
CREATE INDEX IF NOT EXISTS idx_audit_intake_status      ON audit_intake(status);
CREATE INDEX IF NOT EXISTS idx_audit_intake_website     ON audit_intake(website_url);
CREATE INDEX IF NOT EXISTS idx_audit_intake_markets_gin ON audit_intake USING gin (markets);
CREATE INDEX IF NOT EXISTS idx_audit_intake_services_gin ON audit_intake USING gin (primary_services);

-- ==========================
-- AUDIT CONVERSION GOAL
-- ==========================
CREATE TABLE IF NOT EXISTS audit_conversion_goal (
  id                    uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  intake_id             uuid NOT NULL,
  type                  varchar(32)  NOT NULL DEFAULT 'form',  -- form|call|purchase|visit|download|other
  kpi                   varchar(128) NOT NULL,                 -- e.g. "Leads per week"
  baseline              double precision,
  value_per_conversion  numeric(10,2),
  CONSTRAINT fk_goal_intake
    FOREIGN KEY (intake_id) REFERENCES audit_intake(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_goal_intake ON audit_conversion_goal(intake_id);

-- ==========================
-- AUDIT COMPETITOR
-- ==========================
CREATE TABLE IF NOT EXISTS audit_competitor (
  id           uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  intake_id    uuid NOT NULL,
  name         varchar(255) NOT NULL,
  website_url  varchar(255),
  CONSTRAINT fk_competitor_intake
    FOREIGN KEY (intake_id) REFERENCES audit_intake(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_competitor_intake ON audit_competitor(intake_id);

-- ==========================
-- AUDIT KEYWORD
-- ==========================
CREATE TABLE IF NOT EXISTS audit_keyword (
  id         uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  intake_id  uuid NOT NULL,
  phrase     varchar(255) NOT NULL,
  intent     varchar(16)  NOT NULL DEFAULT 'local',  -- informational|transactional|navigational|local
  priority   smallint     NOT NULL DEFAULT 3,        -- 1..5
  CONSTRAINT fk_keyword_intake
    FOREIGN KEY (intake_id) REFERENCES audit_intake(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_keyword_intake   ON audit_keyword(intake_id);
CREATE INDEX IF NOT EXISTS idx_keyword_priority ON audit_keyword(priority);
CREATE INDEX IF NOT EXISTS idx_keyword_phrase   ON audit_keyword(phrase);

-- ==========================
-- AUDIT RUN
-- ==========================
CREATE TABLE IF NOT EXISTS audit_run (
  id             uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  client_id      uuid NOT NULL,
  intake_id      uuid NOT NULL,
  initiated_by_id uuid NULL,
  status         varchar(16) NOT NULL DEFAULT 'queued',  -- queued|running|completed|failed
  scope          jsonb,                                  -- {"technical":true,"onpage":true,...}
  started_at     timestamptz,
  completed_at   timestamptz,
  tool_versions  jsonb,
  result_summary text,
  totals         jsonb,                                  -- counts by severity/category, etc.
  CONSTRAINT fk_run_client        FOREIGN KEY (client_id)      REFERENCES client(id)      ON DELETE CASCADE,
  CONSTRAINT fk_run_intake        FOREIGN KEY (intake_id)      REFERENCES audit_intake(id) ON DELETE CASCADE,
  CONSTRAINT fk_run_initiated_by  FOREIGN KEY (initiated_by_id) REFERENCES "user"(id)     ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_run_client     ON audit_run(client_id);
CREATE INDEX IF NOT EXISTS idx_run_intake     ON audit_run(intake_id);
CREATE INDEX IF NOT EXISTS idx_run_status     ON audit_run(status);
CREATE INDEX IF NOT EXISTS idx_run_started_at ON audit_run(started_at);

-- ==========================
-- AUDIT FINDING
-- ==========================
CREATE TABLE IF NOT EXISTS audit_finding (
  id              uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  client_id       uuid NOT NULL,
  audit_run_id    uuid NOT NULL,
  title           varchar(255) NOT NULL,
  description     text NOT NULL,
  severity        varchar(16)  NOT NULL DEFAULT 'medium',      -- low|medium|high|critical
  status          varchar(16)  NOT NULL DEFAULT 'open',        -- open|in_progress|resolved|ignored
  category        varchar(64),                                  -- technical|onpage|offpage|local|analytics
  location        varchar(255),                                 -- URL, template, "GBP", etc.
  impact          text,
  recommendation  text,
  impact_score    smallint NOT NULL DEFAULT 3,
  effort_score    smallint NOT NULL DEFAULT 3,
  priority_score  smallint NOT NULL DEFAULT 3,
  CONSTRAINT fk_finding_client   FOREIGN KEY (client_id)    REFERENCES client(id)    ON DELETE CASCADE,
  CONSTRAINT fk_finding_run      FOREIGN KEY (audit_run_id) REFERENCES audit_run(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_finding_run         ON audit_finding(audit_run_id);
CREATE INDEX IF NOT EXISTS idx_finding_client      ON audit_finding(client_id);
CREATE INDEX IF NOT EXISTS idx_finding_severity    ON audit_finding(severity);
CREATE INDEX IF NOT EXISTS idx_finding_status      ON audit_finding(status);
CREATE INDEX IF NOT EXISTS idx_finding_category    ON audit_finding(category);
CREATE INDEX IF NOT EXISTS idx_finding_priority    ON audit_finding(priority_score DESC);
```

### Database Triggers

```sql
-- ==========================
-- TRIGGER: keep updated_at fresh on audit_intake
-- (Optional; app can also manage updatedAt)
-- ==========================
DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_proc WHERE proname = 'audit_intake_touch_updated_at'
  ) THEN
    CREATE OR REPLACE FUNCTION audit_intake_touch_updated_at()
    RETURNS trigger AS $f$
    BEGIN
      NEW.updated_at := now();
      RETURN NEW;
    END;
    $f$ LANGUAGE plpgsql;
  END IF;
END$$;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_trigger WHERE tgname = 'trg_audit_intake_touch_updated_at'
  ) THEN
    CREATE TRIGGER trg_audit_intake_touch_updated_at
    BEFORE UPDATE ON audit_intake
    FOR EACH ROW
    EXECUTE FUNCTION audit_intake_touch_updated_at();
  END IF;
END$$;
```

## 🔐 Security & Access Control

### Role-Based Access Control (RBAC)

| Role | Description | Permissions |
|------|-------------|-------------|
| `ROLE_SYSTEM_ADMIN` | System administrator | Full system access |
| `ROLE_AGENCY_ADMIN` | Agency administrator | Agency and client management |
| `ROLE_AGENCY_STAFF` | Agency staff member | Client access, audit management |
| `ROLE_CLIENT_ADMIN` | Client administrator | Client data management |
| `ROLE_CLIENT_STAFF` | Client staff member | Limited client access |
| `ROLE_CLIENT_USER` | Client user | Read-only client access |
| `ROLE_READ_ONLY` | Read-only user | View-only access |

### Multi-Tenancy Security

- **Tenant Isolation**: All data is scoped by `tenant_id`
- **Client Isolation**: User access is restricted to assigned clients
- **API Security**: JWT authentication with role-based permissions
- **Data Encryption**: Sensitive data encrypted at rest

## 📈 Performance Optimization

### Recommended Indexes

```sql
-- Primary keys (automatically indexed)
CREATE INDEX idx_clients_agency_id ON clients(agency_id);
CREATE INDEX idx_users_organization_id ON users(organization_id);
CREATE INDEX idx_users_client_id ON users(client_id);
CREATE INDEX idx_audit_intake_client_id ON audit_intake(client_id);
CREATE INDEX idx_audit_intake_requested_by ON audit_intake(requested_by);
CREATE INDEX idx_audit_run_client_id ON audit_run(client_id);
CREATE INDEX idx_audit_finding_client_id ON audit_finding(client_id);
CREATE INDEX idx_leads_client_id ON leads(client_id);
CREATE INDEX idx_leads_status ON leads(status);
CREATE INDEX idx_keywords_client_id ON keywords(client_id);
CREATE INDEX idx_oauth_connections_client_id ON oauth_connections(client_id);

-- Composite indexes for common queries
CREATE INDEX idx_audit_intake_status_client ON audit_intake(status, client_id);
CREATE INDEX idx_leads_status_created ON leads(status, created_at);
CREATE INDEX idx_users_role_organization ON users(role, organization_id);

-- GIN indexes for JSON fields (PostgreSQL specific)
CREATE INDEX idx_audit_intake_markets_gin ON audit_intake USING gin (markets);
CREATE INDEX idx_audit_intake_services_gin ON audit_intake USING gin (primary_services);
CREATE INDEX idx_audit_intake_tech_stack_gin ON audit_intake USING gin (tech_stack);
CREATE INDEX idx_audit_run_scope_gin ON audit_run USING gin (scope);
CREATE INDEX idx_audit_run_totals_gin ON audit_run USING gin (totals);

-- Performance indexes for audit system
CREATE INDEX idx_audit_finding_severity_status ON audit_finding(severity, status);
CREATE INDEX idx_audit_finding_category_priority ON audit_finding(category, priority_score DESC);
CREATE INDEX idx_audit_run_status_started ON audit_run(status, started_at);
CREATE INDEX idx_audit_keyword_intent_priority ON audit_keyword(intent, priority);

### Partitioning Strategy

For high-volume tables, consider partitioning by:
- **Date**: `leads`, `audit_runs`, `audit_findings`
- **Client**: `audit_intake`, `content_items`
- **Status**: `leads`, `audit_findings`

## 🔄 Data Lifecycle

### Audit Data Retention

| Data Type | Retention Period | Archival Strategy |
|-----------|------------------|-------------------|
| Audit Intakes | 7 years | Archive to cold storage |
| Audit Runs | 5 years | Archive to cold storage |
| Audit Findings | 5 years | Archive to cold storage |
| Lead Data | 3 years | Archive to cold storage |
| User Activity | 2 years | Archive to cold storage |
| OAuth Tokens | 1 year | Automatic deletion |

### Backup Strategy

- **Daily**: Full database backup
- **Hourly**: Transaction log backup
- **Weekly**: Point-in-time recovery backup
- **Monthly**: Long-term archival backup

## 🛠️ Maintenance Commands

### Schema Validation
```bash
# Validate entity mapping
php bin/console doctrine:schema:validate

# Check for schema differences
php bin/console doctrine:schema:update --dump-sql

# Update schema (production: use migrations instead)
php bin/console doctrine:schema:update --force
```

### Performance Monitoring
```bash
# Check slow queries
php bin/console doctrine:query:sql "SELECT * FROM pg_stat_statements ORDER BY mean_time DESC LIMIT 10;"

# Analyze table statistics
php bin/console doctrine:query:sql "ANALYZE;"

# Check index usage
php bin/console doctrine:query:sql "SELECT schemaname, tablename, indexname, idx_scan FROM pg_stat_user_indexes;"
```

### Data Management
```bash
# Export schema
php bin/console doctrine:schema:export --format=sql

# Import schema
php bin/console doctrine:schema:import --format=sql

# Clear cache
php bin/console cache:clear
```

## 📚 Related Documentation

- **[DATABASE_GUIDE.md](./DATABASE_GUIDE.md)** - Database setup and management
- **[API_REFERENCE.md](./API_REFERENCE.md)** - API endpoint documentation
- **[ARCHITECTURE.md](./ARCHITECTURE.md)** - System architecture overview
- **[QUICK_START.md](./QUICK_START.md)** - Development setup guide

## 🔍 Schema Version History

| Version | Date | Changes | Migration File |
|---------|------|---------|----------------|
| 1.0.0 | 2025-01-XX | Initial schema with audit system | Version202501XX |
| 0.9.0 | 2025-08-25 | Core entities and OAuth | Version20250825181841 |
| 0.8.0 | 2025-08-16 | User management and RBAC | Version20250816000131 |
| 0.7.0 | 2025-08-10 | Basic client structure | Version20250810171800 |
| 0.6.0 | 2025-08-10 | Foundation entities | Version20250810 |

---

*This schema document is automatically generated and should be updated when entities are modified. For the most current schema, check the Doctrine entity classes and migration files.*
