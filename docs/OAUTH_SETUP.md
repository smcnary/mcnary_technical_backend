# OAuth Provider Setup Guide

This comprehensive guide covers setting up OAuth authentication providers for CounselRank.legal, including Google OAuth and Microsoft OAuth SSO.

## Table of Contents

1. [Overview](#overview)
2. [Google OAuth Setup](#google-oauth-setup)
3. [Microsoft OAuth Setup](#microsoft-oauth-setup)
4. [Environment Configuration](#environment-configuration)
5. [API Endpoints](#api-endpoints)
6. [Security Considerations](#security-considerations)
7. [Troubleshooting](#troubleshooting)
8. [Production Deployment](#production-deployment)

## Overview

The OAuth system supports multiple providers to give users flexible authentication options:

- **Google OAuth**: For Google Business Profile, Search Console, Analytics integration
- **Microsoft OAuth**: For Microsoft 365, Azure AD integration

### OAuth Flow
1. User initiates OAuth connection
2. Redirect to provider authorization
3. Provider returns authorization code
4. Exchange code for access/refresh tokens
5. Store tokens securely
6. Use tokens for API access

## Google OAuth Setup

### Prerequisites
- Google Cloud Platform account
- Access to Google Cloud Console
- Domain verification (for production use)

### Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API and Google OAuth2 API

### Step 2: Configure OAuth Consent Screen

1. In Google Cloud Console, go to "APIs & Services" > "OAuth consent screen"
2. Choose "External" user type (unless you have a Google Workspace)
3. Fill in the required information:
   - App name: "CounselRank.legal"
   - User support email: your email
   - Developer contact information: your email
4. Add scopes:
   - `openid`
   - `email`
   - `profile`
5. Add test users if needed
6. Save and continue

### Step 3: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Choose "Web application" as the application type
4. Set the following:
   - Name: "CounselRank Backend"
   - Authorized redirect URIs:
     - `http://localhost:8000/api/v1/auth/google/callback` (development)
     - `https://yourdomain.com/api/v1/auth/google/callback` (production)
5. Click "Create"
6. Note down the Client ID and Client Secret

## Microsoft OAuth Setup

### Prerequisites
- Microsoft Azure account with admin access
- Application already configured with Google OAuth (for reference)

### Step 1: Create Azure App Registration

#### 1.1 Access Azure Portal
1. Go to [Azure Portal](https://portal.azure.com)
2. Sign in with your Microsoft account
3. Navigate to "Azure Active Directory" > "App registrations"

#### 1.2 Create New App Registration
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

#### 1.3 Configure Authentication
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

#### 1.4 Configure API Permissions
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

#### 1.5 Get Client Credentials
1. Go to "Overview" in the left sidebar
2. Copy the **Application (client) ID**
3. Go to "Certificates & secrets" in the left sidebar
4. Click "New client secret"
5. Add a description (e.g., "OAuth Client Secret")
6. Choose expiration (recommend 24 months for development)
7. Click "Add"
8. **IMPORTANT**: Copy the secret value immediately (you won't see it again)

## Environment Configuration

### Backend Configuration
Add these variables to your `backend/.env.local` file:

```bash
# Google OAuth Configuration
GOOGLE_OAUTH_CLIENT_ID="your_google_client_id_here"
GOOGLE_OAUTH_CLIENT_SECRET="your_google_client_secret_here"
GOOGLE_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/google/callback"

# Microsoft OAuth Configuration
MICROSOFT_OAUTH_CLIENT_ID="your_microsoft_client_id_here"
MICROSOFT_OAUTH_CLIENT_SECRET="your_microsoft_client_secret_here"
MICROSOFT_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/microsoft/callback"

# Frontend URL
APP_FRONTEND_URL="http://localhost:3000"
```

### Frontend Configuration
The frontend will automatically use the backend URL from `NEXT_PUBLIC_API_BASE_URL`.

```typescript
// Example frontend OAuth configuration
const GOOGLE_OAUTH_URL = 'http://localhost:8000/api/v1/auth/google';
const MICROSOFT_OAUTH_URL = 'http://localhost:8000/api/v1/auth/microsoft';
```

## API Endpoints

### Google OAuth Endpoints
- `GET /api/v1/auth/google` - Initiate Google OAuth flow
- `GET /api/v1/auth/google/callback` - Handle Google OAuth callback
- `POST /api/v1/auth/google/link` - Link Google account to existing user
- `POST /api/v1/auth/google/unlink` - Unlink Google account

### Microsoft OAuth Endpoints
- `GET /api/v1/auth/microsoft` - Initiate Microsoft OAuth flow
- `GET /api/v1/auth/microsoft/callback` - Handle Microsoft OAuth callback
- `POST /api/v1/auth/microsoft/link` - Link Microsoft account to existing user
- `POST /api/v1/auth/microsoft/unlink` - Unlink Microsoft account

### OAuth Flow Details

#### Google OAuth Login Initiation
- **URL**: `GET /api/v1/auth/google`
- **Description**: Initiates Google OAuth flow
- **Response**: Redirects to Google OAuth consent screen

#### Google OAuth Callback
- **URL**: `GET /api/v1/auth/google/callback`
- **Description**: Handles Google OAuth callback
- **Parameters**: `code`, `state` (from Google)
- **Response**: Redirects to frontend with JWT token

#### Microsoft OAuth Login Initiation
- **URL**: `GET /api/v1/auth/microsoft`
- **Description**: Initiates Microsoft OAuth flow
- **Response**: Redirects to Microsoft OAuth consent screen

#### Microsoft OAuth Callback
- **URL**: `GET /api/v1/auth/microsoft/callback`
- **Description**: Handles Microsoft OAuth callback
- **Parameters**: `code`, `state` (from Microsoft)
- **Response**: Redirects to frontend with JWT token

## Security Considerations

### General Security
1. **State Parameter**: The OAuth flow includes a state parameter to prevent CSRF attacks
2. **HTTPS**: Always use HTTPS in production
3. **Client Secret**: Never expose the client secret in frontend code
4. **Token Storage**: Store OAuth tokens securely in the database
5. **Scope Limitation**: Only request necessary scopes

### Google OAuth Security
- Use secure redirect URIs
- Implement proper state parameter validation
- Monitor for suspicious OAuth activity
- Regularly rotate client secrets

### Microsoft OAuth Security
- Use HTTPS in production
- Implement proper redirect URI validation
- Monitor Azure AD sign-in logs
- Use strong client secrets

## Testing the Integration

### Start the Servers
```bash
# Backend (from backend/ directory)
php -S localhost:8000 -t public/

# Frontend (from frontend/ directory)
npm run dev
```

### Test Google OAuth
1. Navigate to `http://localhost:8000/api/v1/auth/google`
2. You should be redirected to Google's OAuth consent screen
3. After authorization, you'll be redirected back with a JWT token

### Test Microsoft OAuth
1. Go to `http://localhost:3000/login`
2. Click "Sign in with Microsoft"
3. You should be redirected to Microsoft's login page
4. After successful authentication, you'll be redirected to the client dashboard

## Troubleshooting

### Google OAuth Issues

#### 1. "Invalid Redirect URI" Error
- Ensure the redirect URI in Google Cloud Console matches exactly
- Check for trailing slashes or protocol mismatches

#### 2. "Missing Scopes" Error
- Verify all required scopes are enabled in OAuth consent screen
- Check that `openid`, `email`, and `profile` scopes are added

#### 3. "Domain Verification" Error
- For production, verify your domain ownership in Google Cloud Console
- Ensure the domain matches your production URL

### Microsoft OAuth Issues

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
3. Test the OAuth provider API endpoints manually
4. Check OAuth provider configuration in their respective consoles

### Debug Mode
Enable debug logging in your Symfony application to troubleshoot OAuth issues:

```yaml
# config/packages/dev/monolog.yaml
monolog:
    handlers:
        main:
            level: debug
```

## Production Deployment

### Environment Variables
```bash
# Production Google OAuth Configuration
GOOGLE_OAUTH_CLIENT_ID="your_production_google_client_id"
GOOGLE_OAUTH_CLIENT_SECRET="your_production_google_client_secret"
GOOGLE_OAUTH_REDIRECT_URI="https://yourdomain.com/api/v1/auth/google/callback"

# Production Microsoft OAuth Configuration
MICROSOFT_OAUTH_CLIENT_ID="your_production_microsoft_client_id"
MICROSOFT_OAUTH_CLIENT_SECRET="your_production_microsoft_client_secret"
MICROSOFT_OAUTH_REDIRECT_URI="https://yourdomain.com/api/v1/auth/microsoft/callback"

# Production Frontend URL
APP_FRONTEND_URL="https://yourdomain.com"
```

### Security Checklist
1. **Domain Verification**: Verify your domain in both Google Cloud Console and Azure Portal
2. **HTTPS**: Ensure all OAuth endpoints use HTTPS
3. **Environment Variables**: Set production environment variables securely
4. **Monitoring**: Monitor OAuth authentication logs
5. **Rate Limiting**: Implement rate limiting for OAuth endpoints
6. **Client Secret Rotation**: Regularly rotate OAuth client secrets

### Performance Optimization
- Implement OAuth token caching
- Use Redis for session storage
- Monitor OAuth endpoint response times
- Implement proper error handling and retry logic

## Next Steps

After successful OAuth setup:

1. **Test Integration**: Verify both Google and Microsoft OAuth flows work correctly
2. **User Experience**: Implement seamless OAuth login in your frontend
3. **Account Linking**: Allow users to link multiple OAuth providers
4. **Token Management**: Implement refresh token logic for long-term access
5. **Analytics**: Track OAuth usage and success rates
6. **Admin Interface**: Add OAuth configuration to admin dashboard

## Support

For issues related to:
- **Google OAuth setup**: Check [Google Cloud Console documentation](https://developers.google.com/identity/protocols/oauth2)
- **Microsoft OAuth setup**: Check [Microsoft Graph documentation](https://docs.microsoft.com/en-us/graph/auth/)
- **Backend implementation**: Check Symfony logs and API responses
- **Frontend integration**: Verify redirect URIs and error handling

---

**Last Updated:** January 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅