# User Registration Access Fix for Tenant Clients

## Problem Description

Previously, only users with `ROLE_AGENCY_ADMIN` could create, list, and update users. This prevented tenant clients (who have `ROLE_CLIENT_ADMIN` or `ROLE_CLIENT_STAFF`) from managing users within their own client scope.

## Changes Made

### 1. Updated User Entity API Platform Security

**File**: `backend/src/Entity/User.php`

**Before**:
```php
#[ApiResource(security: "is_granted('ROLE_AGENCY_ADMIN') or object == user")]
```

**After**:
```php
#[ApiResource(
    security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.clientId == user.clientId) or object == user"
)]
```

**What this means**: Client admins can now access user resources through API Platform, but only for users within their own client scope.

### 2. Updated UserController Security

**File**: `backend/src/Controller/Api/V1/UserController.php`

#### createUser Method
- **Before**: Only `ROLE_AGENCY_ADMIN` could create users
- **After**: Both `ROLE_AGENCY_ADMIN` and `ROLE_CLIENT_ADMIN` can create users
- **Security restrictions for client admins**:
  - Can only create users within their own client scope
  - Can only create client users (`ROLE_CLIENT_ADMIN`, `ROLE_CLIENT_STAFF`)
  - Cannot create agency users

#### listUsers Method
- **Before**: Only `ROLE_AGENCY_ADMIN` could list users
- **After**: Both `ROLE_AGENCY_ADMIN` and `ROLE_CLIENT_ADMIN` can list users
- **Security restrictions for client admins**:
  - Can only see users within their own client scope
  - Results are automatically filtered by `client_id`

#### updateUser Method
- **Before**: Only `ROLE_AGENCY_ADMIN` could update users
- **After**: Both `ROLE_AGENCY_ADMIN` and `ROLE_CLIENT_ADMIN` can update users
- **Security restrictions for client admins**:
  - Can only update users within their own client scope
  - Can only update client users
  - Cannot change `client_id` field
  - Can only assign client roles

## Security Model

### Agency Admins
- Full access to all users across all clients
- Can create, read, update, and delete any user
- Can assign any role to users

### Client Admins
- Limited access to users within their own client scope
- Can create, read, and update client users only
- Cannot access agency users
- Cannot change client associations
- Cannot assign agency roles

### Client Staff
- No user management permissions
- Can only view their own user profile

## API Endpoints Affected

- `POST /api/v1/users` - Create user
- `GET /api/v1/users` - List users
- `PATCH /api/v1/users/{id}` - Update user
- `GET /api/v1/me` - Get current user profile

## Testing the Changes

### For Agency Admins
1. Login with an agency admin account
2. All user management operations should work as before
3. Can manage users across all clients

### For Client Admins
1. Login with a client admin account
2. Can create new users within their client scope
3. Can list and update users within their client scope
4. Cannot access users from other clients
5. Cannot create or modify agency users

### For Client Staff
1. Login with a client staff account
2. Can only view their own profile
3. Cannot access user management endpoints

## Example Usage

### Creating a User as Client Admin

```bash
POST /api/v1/users
Authorization: Bearer <client_admin_jwt_token>
Content-Type: application/json

{
    "email": "newuser@client.com",
    "name": "New User",
    "role": "ROLE_CLIENT_STAFF",
    "client_id": "client-uuid-here",
    "status": "invited"
}
```

**Note**: The `client_id` must match the client admin's own `client_id`, otherwise the request will be rejected.

## Security Considerations

1. **Client Isolation**: Client admins are completely isolated from other clients' data
2. **Role Restrictions**: Client admins cannot escalate privileges by creating agency users
3. **Audit Trail**: All user management operations are logged and traceable
4. **Input Validation**: All inputs are validated and sanitized
5. **JWT Authentication**: All endpoints require valid JWT authentication

## Future Enhancements

1. **User Invitation System**: Implement email-based user invitations
2. **Bulk Operations**: Add support for bulk user creation/updates
3. **Advanced Filtering**: Add more sophisticated filtering options for user lists
4. **User Activity Logging**: Track user login and activity patterns
5. **Role Hierarchy**: Implement more granular role permissions

## Troubleshooting

### Common Issues

1. **"Access denied" errors**: Ensure the user has the correct role (`ROLE_AGENCY_ADMIN` or `ROLE_CLIENT_ADMIN`)
2. **"Client not found" errors**: Verify the `client_id` exists and matches the user's client
3. **"Cannot create agency users" errors**: Client admins can only create client users
4. **"User not found" errors**: Verify the user ID exists and is within the user's scope

### Debug Information

Check the application logs for detailed error messages and stack traces. The security system provides comprehensive logging for all access control decisions.
