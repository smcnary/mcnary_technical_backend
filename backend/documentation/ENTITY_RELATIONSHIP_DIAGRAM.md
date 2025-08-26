# 🔗 Entity Relationship Diagram

## 📋 Overview

This document provides a visual representation of the database relationships and entity connections in the CounselRank.legal platform. It complements the [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) document with clear relationship mappings.

## 🏗️ High-Level Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Organization  │    │     Agency      │    │     Client      │
│                 │    │                 │    │                 │
│ • id (UUID)     │◄──►│ • id (UUID)     │◄──►│ • id (UUID)     │
│ • name          │    │ • name          │    │ • name          │
│ • slug          │    │ • description   │    │ • website_url   │
│ • status        │    │ • website_url   │    │ • industry      │
└─────────────────┘    │ • status        │    │ • status        │
                       └─────────────────┘    └─────────────────┘
                                │                       │
                                │                       │
                                ▼                       ▼
                       ┌─────────────────┐    ┌─────────────────┐
                       │      User       │    │   AuditIntake   │
                       │                 │    │                 │
                       │ • id (UUID)     │    │ • id (UUID)     │
                       │ • email         │    │ • website_url   │
                       │ • role          │    │ • cms           │
                       │ • status        │    │ • tech_stack    │
                       └─────────────────┘    │ • status        │
                                              └─────────────────┘
```

## 🔐 Multi-Tenancy Structure

```
Organization (1) ──► (N) Tenant (1) ──► (N) User
     │                      │
     │                      │
     ▼                      ▼
(N) Agency (1) ──► (N) Client (1) ──► (N) UserClientAccess
     │                      │
     │                      │
     ▼                      ▼
(N) User (1) ──► (N) OAuthConnection
```

## 📊 Audit System Relationships

```
Client (1) ──► (N) AuditIntake (1) ──► (N) AuditConversionGoal
     │              │
     │              │
     │              ▼
     │         (N) AuditCompetitor
     │              │
     │              ▼
     │         (N) AuditKeyword
     │
     ▼
(N) AuditRun (1) ──► (N) AuditFinding
     │
     │
     ▼
(N) User (initiatedBy)
```

## 🔄 Marketing & Content Flow

```
Client (1) ──► (N) Lead
     │
     │
     ▼
(N) Campaign (1) ──► (N) ContentItem
     │
     │
     ▼
(N) Keyword
```

## 📋 Detailed Relationship Mappings

### 1. Organization Hierarchy

| Entity | Relationship | Target | Cardinality | Description |
|--------|--------------|---------|-------------|-------------|
| `Organization` | `agencies` | `Agency` | 1:N | Organization has many agencies |
| `Organization` | `tenants` | `Tenant` | 1:N | Organization has many tenants |
| `Organization` | `users` | `User` | 1:N | Organization has many users |

### 2. Agency & Client Management

| Entity | Relationship | Target | Cardinality | Description |
|--------|--------------|---------|-------------|-------------|
| `Agency` | `clients` | `Client` | 1:N | Agency manages many clients |
| `Agency` | `users` | `User` | 1:N | Agency has many staff users |
| `Client` | `locations` | `ClientLocation` | 1:N | Client has multiple locations |
| `Client` | `auditIntakes` | `AuditIntake` | 1:N | Client has many audit intakes |
| `Client` | `auditRuns` | `AuditRun` | 1:N | Client has many audit runs |
| `Client` | `auditFindings` | `AuditFinding` | 1:N | Client has many audit findings |
| `Client` | `leads` | `Lead` | 1:N | Client generates many leads |
| `Client` | `campaigns` | `Campaign` | 1:N | Client runs many campaigns |
| `Client` | `keywords` | `Keyword` | 1:N | Client targets many keywords |
| `Client` | `contentItems` | `ContentItem` | 1:N | Client has many content pieces |
| `Client` | `oauthConnections` | `OAuthConnection` | 1:N | Client has many OAuth integrations |

### 3. User Access & Permissions

| Entity | Relationship | Target | Cardinality | Description |
|--------|--------------|---------|-------------|-------------|
| `User` | `clientAccess` | `UserClientAccess` | 1:N | User has access to multiple clients |
| `User` | `requestedAuditIntakes` | `AuditIntake` | 1:N | User requested many audits |
| `User` | `initiatedAuditRuns` | `AuditRun` | 1:N | User initiated many audit runs |
| `User` | `oauthConnections` | `OAuthConnection` | 1:N | User has many OAuth connections |

### 4. Audit System Relationships

| Entity | Relationship | Target | Cardinality | Description |
|--------|--------------|---------|-------------|-------------|
| `AuditIntake` | `goals` | `AuditConversionGoal` | 1:N | Intake has many conversion goals |
| `AuditIntake` | `competitors` | `AuditCompetitor` | 1:N | Intake tracks many competitors |
| `AuditIntake` | `keywords` | `AuditKeyword` | 1:N | Intake targets many keywords |
| `AuditRun` | `findings` | `AuditFinding` | 1:N | Run produces many findings |

### 5. Marketing & Content Relationships

| Entity | Relationship | Target | Cardinality | Description |
|--------|--------------|---------|-------------|-------------|
| `Campaign` | `contentItems` | `ContentItem` | 1:N | Campaign includes many content pieces |
| `Campaign` | `keywords` | `Keyword` | 1:N | Campaign targets many keywords |
| `Lead` | `source` | `Campaign` | N:1 | Lead comes from a campaign |

## 🔗 Foreign Key Constraints

### Cascade Rules

| Table | Column | References | On Delete | On Update |
|-------|--------|------------|-----------|-----------|
| `clients` | `agency_id` | `agencies.id` | CASCADE | CASCADE |
| `users` | `organization_id` | `organizations.id` | CASCADE | CASCADE |
| `users` | `agency_id` | `agencies.id` | SET NULL | CASCADE |
| `users` | `tenant_id` | `tenants.id` | SET NULL | CASCADE |
| `audit_intake` | `client_id` | `clients.id` | CASCADE | CASCADE |
| `audit_intake` | `requested_by` | `users.id` | SET NULL | CASCADE |
| `audit_conversion_goal` | `intake_id` | `audit_intake.id` | CASCADE | CASCADE |
| `audit_competitor` | `intake_id` | `audit_intake.id` | CASCADE | CASCADE |
| `audit_keyword` | `intake_id` | `audit_intake.id` | CASCADE | CASCADE |
| `audit_run` | `client_id` | `clients.id` | CASCADE | CASCADE |
| `audit_run` | `intake_id` | `audit_intake.id` | CASCADE | CASCADE |
| `audit_run` | `initiated_by` | `users.id` | SET NULL | CASCADE |
| `audit_finding` | `client_id` | `clients.id` | CASCADE | CASCADE |
| `audit_finding` | `audit_run_id` | `audit_run.id` | CASCADE | CASCADE |

## 📊 Data Flow Patterns

### 1. Client Onboarding Flow

```
1. Create Client → 2. Create AuditIntake → 3. Define Goals/Competitors/Keywords
     ↓                    ↓                           ↓
4. Run Audit → 5. Generate Findings → 6. Create Recommendations
```

### 2. User Access Flow

```
1. Create User → 2. Assign Role → 3. Grant Client Access → 4. Set Permissions
```

### 3. Audit Execution Flow

```
1. Submit Intake → 2. Approve Intake → 3. Initiate Run → 4. Process Results → 5. Generate Findings
```

## 🔍 Query Optimization Patterns

### Common Join Patterns

```sql
-- Get client with all audit information
SELECT c.*, ai.*, ar.*, af.*
FROM clients c
LEFT JOIN audit_intake ai ON c.id = ai.client_id
LEFT JOIN audit_run ar ON c.id = ar.client_id
LEFT JOIN audit_finding af ON ar.id = af.audit_run_id
WHERE c.id = :client_id;

-- Get user with client access
SELECT u.*, uca.*, c.*
FROM users u
JOIN user_client_access uca ON u.id = uca.user_id
JOIN clients c ON uca.client_id = c.id
WHERE u.id = :user_id;
```

### Indexing Strategy

```sql
-- Composite indexes for common queries
CREATE INDEX idx_audit_intake_client_status ON audit_intake(client_id, status);
CREATE INDEX idx_audit_run_client_intake ON audit_run(client_id, intake_id);
CREATE INDEX idx_users_role_organization ON users(role, organization_id);
CREATE INDEX idx_leads_client_status_date ON leads(client_id, status, created_at);
```

## 🚀 Performance Considerations

### 1. Read-Heavy Tables
- **`audit_findings`**: Index on `(client_id, severity, status)`
- **`leads`**: Index on `(client_id, status, created_at)`
- **`users`**: Index on `(organization_id, role)`

### 2. Write-Heavy Tables
- **`audit_runs`**: Partition by `client_id` or date
- **`audit_findings`**: Partition by `client_id` or date
- **`leads`**: Partition by `client_id` or date

### 3. Relationship Tables
- **`user_client_access`**: Index on `(user_id, client_id)`
- **`audit_conversion_goal`**: Index on `(intake_id)`
- **`audit_competitor`**: Index on `(intake_id)`

## 🔒 Security Considerations

### 1. Data Isolation
- All queries must include `client_id` filter
- User access controlled via `user_client_access` table
- Role-based permissions enforced at API level

### 2. Audit Trails
- All entities include `created_at` and `updated_at`
- User actions logged via relationship tracking
- Data changes tracked through versioning

### 3. Access Control
- JWT tokens validated for all requests
- Client scoping enforced at entity level
- Role permissions checked before data access

---

*This diagram should be updated whenever new entities or relationships are added to the system. For the most current schema, refer to the [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) document.*
