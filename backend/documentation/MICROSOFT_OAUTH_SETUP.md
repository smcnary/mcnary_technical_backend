# Microsoft OAuth SSO Setup Guide

This guide explains how to set up Microsoft Single Sign-On (SSO) for the CounselRank.legal application.

## Prerequisites

- Microsoft Azure account with admin access
- Application already configured with Google OAuth (for reference)

## Step 1: Create Azure App Registration

### 1.1 Access Azure Portal
1. Go to [Azure Portal](https://portal.azure.com)
2. Sign in with your Microsoft account
3. Navigate to "Azure Active Directory" > "App registrations"

### 1.2 Create New App Registration
1. Click "New registration"
2. Fill in the details:
   - **Name**: `CounselRank Legal - Microsoft SSO`
   - **Supported account types**: Choose based on your needs:
     - `Accounts in this organizational directory only` (for single tenant)
     - `Accounts in any organizational directory and personal Microsoft accounts` (for multi-tenant)
   - **Redirect URI**: 
     - Type: `Web`
     - URI: `http://localhost:8000/api/v1/auth/microsoft/callback` (for development)
     - URI: `https://yourdomain.com/api/v1/auth/microsoft/callback` (for production)
3. Click "Register"

### 1.3 Configure Authentication
1. Go to "Authentication" in the left sidebar
2. Under "Platform configurations", click "Add a platform"
3. Select "Web"
4. Add your redirect URIs:
   - `http://localhost:8000/api/v1/auth/microsoft/callback`
   - `https://yourdomain.com/api/v1/auth/microsoft/callback`
5. Under "Implicit grant and hybrid flows", ensure these are **unchecked**:
   - Access tokens
   - ID tokens
6. Click "Save"

### 1.4 Configure API Permissions
1. Go to "API permissions" in the left sidebar
2. Click "Add a permission"
3. Select "Microsoft Graph"
4. Choose "Delegated permissions"
5. Select these permissions:
   - `User.Read` (to read user profile)
   - `email` (to get user email)
   - `profile` (to get user name)
6. Click "Add permissions"
7. Click "Grant admin consent" (if you're an admin)

### 1.5 Get Client Credentials
1. Go to "Overview" in the left sidebar
2. Copy the **Application (client) ID**
3. Go to "Certificates & secrets" in the left sidebar
4. Click "New client secret"
5. Add a description (e.g., "OAuth Client Secret")
6. Choose expiration (recommend 24 months for development)
7. Click "Add"
8. **IMPORTANT**: Copy the secret value immediately (you won't see it again)

## Step 2: Configure Environment Variables

### 2.1 Backend Configuration
Add these variables to your `backend/.env.local` file:

```bash
# Microsoft OAuth Configuration
MICROSOFT_OAUTH_CLIENT_ID="your_microsoft_client_id_here"
MICROSOFT_OAUTH_CLIENT_SECRET="your_microsoft_client_secret_here"
MICROSOFT_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/microsoft/callback"
```

### 2.2 Frontend Configuration
The frontend will automatically use the backend URL from `NEXT_PUBLIC_API_BASE_URL`.

## Step 3: Test the Integration

### 3.1 Start the Servers
```bash
# Backend (from backend/ directory)
php -S localhost:8000 -t public/

# Frontend (from frontend/ directory)
npm run dev
```

### 3.2 Test Microsoft SSO
1. Go to `http://localhost:3000/login`
2. Click "Sign in with Microsoft"
3. You should be redirected to Microsoft's login page
4. After successful authentication, you'll be redirected to the client dashboard

## Step 4: Production Deployment

### 4.1 Update Redirect URIs
1. In Azure Portal, update the redirect URI to your production domain
2. Update your production environment variables

### 4.2 Environment Variables
```bash
# Production Microsoft OAuth Configuration
MICROSOFT_OAUTH_CLIENT_ID="your_production_microsoft_client_id"
MICROSOFT_OAUTH_CLIENT_SECRET="your_production_microsoft_client_secret"
MICROSOFT_OAUTH_REDIRECT_URI="https://yourdomain.com/api/v1/auth/microsoft/callback"
```

## Troubleshooting

### Common Issues

#### 1. "Invalid client" Error
- Verify your `MICROSOFT_OAUTH_CLIENT_ID` is correct
- Check that the client secret hasn't expired

#### 2. "Redirect URI mismatch" Error
- Ensure the redirect URI in Azure matches exactly what's in your environment variables
- Check for trailing slashes or protocol mismatches

#### 3. "Insufficient permissions" Error
- Verify you've granted admin consent for the required permissions
- Check that `User.Read`, `email`, and `profile` permissions are added

#### 4. "AADSTS50011" Error
- This usually means the redirect URI doesn't match exactly
- Double-check both Azure configuration and environment variables

### Debug Steps
1. Check backend logs for detailed error messages
2. Verify environment variables are loaded correctly
3. Test the Microsoft Graph API endpoint manually
4. Check Azure App Registration configuration

## Security Considerations

1. **Client Secrets**: Never commit client secrets to version control
2. **Redirect URIs**: Use HTTPS in production and restrict to your domains
3. **Permissions**: Only request the minimum permissions needed
4. **Token Storage**: JWT tokens are stored securely in localStorage
5. **State Parameter**: OAuth state parameter prevents CSRF attacks

## API Endpoints

The following endpoints are available for Microsoft OAuth:

- `GET /api/v1/auth/microsoft` - Initiate Microsoft OAuth flow
- `GET /api/v1/auth/microsoft/callback` - Handle OAuth callback
- `POST /api/v1/auth/microsoft/link` - Link Microsoft account to existing user
- `POST /api/v1/auth/microsoft/unlink` - Unlink Microsoft account

## Next Steps

After successful setup:
1. Test user registration and login flows
2. Implement account linking functionality
3. Add Microsoft-specific user profile fields
4. Consider implementing refresh token logic
5. Add Microsoft OAuth to the admin interface

## Support

If you encounter issues:
1. Check the troubleshooting section above
2. Review Azure App Registration configuration
3. Check backend logs for detailed error messages
4. Verify all environment variables are set correctly
