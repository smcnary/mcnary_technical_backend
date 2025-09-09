# New Role and Tenancy System Implementation

## Overview

This document describes the implementation of the new role-based access control (RBAC) and tenancy system for the CounselRank.legal platform. The system has been redesigned to provide better security, scalability, and user management capabilities.

## New Role Hierarchy

### 1. ROLE_SYSTEM_ADMIN
- **Full platform control** across all agencies and clients
- **Can create and manage agencies**
- **Can invite agency admins** to manage specific agencies
- **Access to all system-level operations**
- **Not tied to any specific agency** (agency_id is nullable)

### 2. ROLE_AGENCY_ADMIN
- **Manages one specific agency** and all its resources
- **Can create and manage clients** under their agency
- **Can create and manage users** within their agency scope
- **Can assign roles** to users within their agency (except ROLE_SYSTEM_ADMIN)
- **Access to agency-level operations** only

### 3. ROLE_CLIENT_USER
- **End-client seat** with limited access
- **View dashboards and reports** for their assigned client
- **Cannot manage other users** or system resources
- **Tied to a specific client** within an agency

### 4. ROLE_READ_ONLY (Optional)
- **Auditor role** with read-only access
- **Can view data** but cannot modify anything
- **Useful for compliance and oversight** purposes

## New Tenancy Model

### Agency-Based Structure
```
System Admin (no agency)
├── Agency A
│   ├── Agency Admin User
│   ├── Client 1
│   │   ├── Client User 1
│   │   └── Client User 2
│   └── Client 2
│       └── Client User 3
└── Agency B
    ├── Agency Admin User
    └── Client 3
        └── Client User 4
```

### Key Changes from Previous System
- **Replaced Organization with Agency** for better clarity
- **Removed Tenant entity** to simplify the model
- **Direct agency-to-client relationship** instead of complex nesting
- **Clearer separation of concerns** between system, agency, and client levels

## Entity Changes

### 1. New Agency Entity
**File**: `backend/src/Entity/Agency.php`

**Key Features**:
- Unique domain-based identification
- Comprehensive contact information
- Status management (active/inactive)
- Metadata support for extensibility
- One-to-many relationships with users and clients

**API Security**:
- System admins: Full CRUD access
- Agency admins: Read/update access to their own agency only

### 2. Updated User Entity
**File**: `backend/src/Entity/User.php`

**Key Changes**:
- Replaced `organization` with `agency` (nullable for system admins)
- Removed `tenant` relationship
- Updated role constants to new system
- Added role-specific helper methods

**New Methods**:
```php
public function isSystemAdmin(): bool
public function isAgencyAdmin(): bool
public function isClientUser(): bool
public function isReadOnly(): bool
```

### 3. Updated Client Entity
**File**: `backend/src/Entity/Client.php`

**Key Changes**:
- Replaced `organization` with `agency` relationship
- Maintains existing functionality for locations, reviews, etc.
- Agency admins can manage clients within their scope

## API Endpoints

### Agency Management (System Admins Only)
- `GET /api/v1/agencies` - List all agencies
- `POST /api/v1/agencies` - Create new agency
- `PATCH /api/v1/agencies/{id}` - Update agency
- `POST /api/v1/agencies/{id}/invite-admin` - Invite agency admin

### User Management
- `GET /api/v1/users` - List users (filtered by role/agency)
- `POST /api/v1/users` - Create user (role/agency restricted)
- `PATCH /api/v1/users/{id}` - Update user (scope restricted)
- `GET /api/v1/me` - Get current user profile

### Client Management
- Existing client endpoints updated to work with agency-based tenancy
- Agency admins can manage clients within their scope
- System admins have access to all clients

## Security Model

### Access Control Matrix

| Role | Agencies | Clients | Users | System Operations |
|------|----------|---------|-------|-------------------|
| **System Admin** | All (CRUD) | All (CRUD) | All (CRUD) | Full access |
| **Agency Admin** | Own only (R/U) | Own agency only (CRUD) | Own agency only (CRUD) | Agency-level only |
| **Client User** | None | Own client only (R) | Own profile only (R/U) | None |
| **Read Only** | None | Assigned clients (R) | Own profile only (R) | None |

### Security Rules

#### System Admins
- Can access and modify any resource in the system
- Can create new agencies and invite agency admins
- Cannot be restricted by agency or client scope

#### Agency Admins
- **Scope**: Limited to their assigned agency
- **Users**: Can create/update users within their agency
- **Clients**: Can manage clients under their agency
- **Restrictions**: Cannot create system admins or access other agencies

#### Client Users
- **Scope**: Limited to their assigned client
- **Access**: Read-only access to client data and reports
- **Profile**: Can update their own user profile
- **Restrictions**: Cannot manage other users or access agency-level operations

#### Read Only Users
- **Scope**: Limited to assigned clients (if any)
- **Access**: Read-only access to permitted data
- **Profile**: Can view their own profile
- **Restrictions**: No write operations anywhere

## Implementation Details

### Database Schema Changes
- New `agencies` table
- Updated `users` table (organization_id → agency_id, removed tenant_id)
- Updated `clients` table (organization_id → agency_id)
- Foreign key constraints updated accordingly

### Migration Strategy
1. **Phase 1**: Create new Agency entity and table
2. **Phase 2**: Update User and Client entities
3. **Phase 3**: Migrate existing data from Organization to Agency
4. **Phase 4**: Update all references and remove old entities

### Backward Compatibility
- Existing API endpoints maintained where possible
- Role-based filtering ensures proper access control
- Gradual migration path for existing users

## Usage Examples

### Creating an Agency (System Admin)
```bash
POST /api/v1/agencies
Authorization: Bearer <system_admin_jwt_token>
Content-Type: application/json

{
    "name": "Digital Marketing Pro",
    "domain": "digitalmarketingpro.com",
    "description": "Full-service digital marketing agency",
    "website_url": "https://digitalmarketingpro.com",
    "email": "admin@digitalmarketingpro.com",
    "phone": "+1-555-0123",
    "address": "123 Marketing St",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA"
}
```

### Inviting an Agency Admin (System Admin)
```bash
POST /api/v1/agencies/{agency_id}/invite-admin
Authorization: Bearer <system_admin_jwt_token>
Content-Type: application/json

{
    "email": "john@digitalmarketingpro.com",
    "name": "John Smith"
}
```

### Creating a Client User (Agency Admin)
```bash
POST /api/v1/users
Authorization: Bearer <agency_admin_jwt_token>
Content-Type: application/json

{
    "email": "client@lawfirm.com",
    "name": "Client User",
    "role": "ROLE_CLIENT_USER",
    "agency_id": "agency-uuid-here",
    "client_id": "client-uuid-here",
    "status": "invited"
}
```

## Testing the New System

### Test Scenarios

#### 1. System Admin Operations
- Create multiple agencies
- Invite agency admins to each agency
- Access all system resources
- Verify no scope restrictions

#### 2. Agency Admin Operations
- Access only their assigned agency
- Create and manage clients under their agency
- Create and manage users within their agency scope
- Verify cannot access other agencies or create system admins

#### 3. Client User Operations
- Access only their assigned client data
- View dashboards and reports
- Update their own profile
- Verify cannot access agency-level operations

#### 4. Read Only User Operations
- Read-only access to permitted data
- Cannot modify any resources
- Profile viewing only

### Security Testing
- Verify role-based access control
- Test scope isolation between agencies
- Ensure client users cannot access other clients
- Validate system admin cannot be restricted

## Future Enhancements

### Planned Features
1. **Role Hierarchy**: More granular role permissions
2. **Multi-Agency Users**: Users with access to multiple agencies
3. **Client Groups**: Logical grouping of related clients
4. **Audit Logging**: Comprehensive activity tracking
5. **API Rate Limiting**: Role-based rate limiting
6. **SSO Integration**: Single sign-on for enterprise clients

### Scalability Considerations
- Agency-based data partitioning
- Efficient query optimization
- Caching strategies for multi-tenant data
- Horizontal scaling support

## Troubleshooting

### Common Issues

#### 1. "Agency not found" errors
- Verify the agency exists and is active
- Check user's agency assignment
- Ensure proper role permissions

#### 2. "Access denied" errors
- Verify user has the correct role
- Check agency scope restrictions
- Ensure proper JWT authentication

#### 3. "Cannot create system admin" errors
- Only system admins can create other system admins
- Agency admins are restricted to agency-level roles

#### 4. Data isolation issues
- Verify agency_id filtering is working
- Check repository query methods
- Ensure proper security annotations

### Debug Information
- Check application logs for detailed error messages
- Verify role assignments in the database
- Test API endpoints with different user roles
- Use Symfony profiler for detailed request analysis

## Conclusion

The new role and tenancy system provides:
- **Better Security**: Clear role boundaries and scope restrictions
- **Improved Scalability**: Agency-based data partitioning
- **Enhanced User Management**: Streamlined user creation and role assignment
- **Clearer Architecture**: Simplified entity relationships
- **Future-Proof Design**: Extensible for additional roles and permissions

This system maintains backward compatibility while providing a solid foundation for future platform growth and enterprise features.
