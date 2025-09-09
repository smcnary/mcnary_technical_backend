# OAuth Provider Setup Guide

## Overview

This guide covers the complete setup for OAuth providers (Google and Microsoft) for the CounselRank.legal authentication system.

## Table of Contents

1. [Google OAuth Setup](#google-oauth-setup)
2. [Microsoft OAuth Setup](#microsoft-oauth-setup)
3. [Environment Configuration](#environment-configuration)
4. [API Endpoints](#api-endpoints)
5. [Security Considerations](#security-considerations)
6. [Troubleshooting](#troubleshooting)
7. [Production Deployment](#production-deployment)

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
   - `http://localhost:8000/api/v1/auth/google/callback`
   - `https://yourdomain.com/api/v1/auth/google/callback`
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

#### Google OAuth Login Initiation
- **URL**: `GET /api/v1/auth/google`
- **Description**: Initiates Google OAuth flow
- **Response**: Redirects to Google OAuth consent screen

#### Google OAuth Callback
- **URL**: `GET /api/v1/auth/google/callback`
- **Description**: Handles Google OAuth callback
- **Parameters**: `code`, `state` (from Google)
- **Response**: Redirects to frontend with JWT token

#### Link Google Account
- **URL**: `POST /api/v1/auth/google/link`
- **Description**: Links existing Google account to current user
- **Headers**: `Authorization: Bearer <jwt_token>`
- **Body**: `{"access_token": "google_access_token"}`

#### Unlink Google Account
- **URL**: `POST /api/v1/auth/google/unlink`
- **Description**: Unlinks Google account from current user
- **Headers**: `Authorization: Bearer <jwt_token>`

### Microsoft OAuth Endpoints

#### Microsoft OAuth Login Initiation
- **URL**: `GET /api/v1/auth/microsoft`
- **Description**: Initiates Microsoft OAuth flow
- **Response**: Redirects to Microsoft OAuth consent screen

#### Microsoft OAuth Callback
- **URL**: `GET /api/v1/auth/microsoft/callback`
- **Description**: Handles Microsoft OAuth callback
- **Parameters**: `code`, `state` (from Microsoft)
- **Response**: Redirects to frontend with JWT token

#### Link Microsoft Account
- **URL**: `POST /api/v1/auth/microsoft/link`
- **Description**: Links existing Microsoft account to current user
- **Headers**: `Authorization: Bearer <jwt_token>`
- **Body**: `{"access_token": "microsoft_access_token"}`

#### Unlink Microsoft Account
- **URL**: `POST /api/v1/auth/microsoft/unlink`
- **Description**: Unlinks Microsoft account from current user
- **Headers**: `Authorization: Bearer <jwt_token>`

## Security Considerations

### General Security
1. **State Parameter**: The OAuth flow includes a state parameter to prevent CSRF attacks
2. **HTTPS**: Always use HTTPS in production
3. **Client Secrets**: Never expose the client secret in frontend code
4. **Token Storage**: Store OAuth tokens securely in the database
5. **Scope Limitation**: Only request necessary scopes

### Google-Specific Security
1. **Domain Verification**: For production, verify your domain ownership
2. **CORS Configuration**: Ensure proper CORS configuration for cross-origin requests
3. **Redirect URI Validation**: Ensure redirect URIs match exactly

### Microsoft-Specific Security
1. **Client Secrets**: Never commit client secrets to version control
2. **Redirect URIs**: Use HTTPS in production and restrict to your domains
3. **Permissions**: Only request the minimum permissions needed
4. **Token Storage**: JWT tokens are stored securely in localStorage

## Troubleshooting

### Common Issues

#### Google OAuth Issues
1. **Invalid Redirect URI**: Ensure the redirect URI in Google Cloud Console matches exactly
2. **Missing Scopes**: Verify all required scopes are enabled in OAuth consent screen
3. **Domain Verification**: For production, verify your domain ownership
4. **CORS Issues**: Ensure proper CORS configuration for cross-origin requests

#### Microsoft OAuth Issues
1. **Invalid client Error**: Verify your `MICROSOFT_OAUTH_CLIENT_ID` is correct
2. **Redirect URI mismatch Error**: Ensure the redirect URI in Azure matches exactly
3. **Insufficient permissions Error**: Verify you've granted admin consent for required permissions
4. **AADSTS50011 Error**: Usually means the redirect URI doesn't match exactly

### Debug Steps
1. Check backend logs for detailed error messages
2. Verify environment variables are loaded correctly
3. Test the OAuth provider API endpoints manually
4. Check OAuth provider configuration (Google Cloud Console / Azure Portal)
5. Enable debug logging in Symfony application

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

### Production Checklist
1. **Domain Verification**: Verify your domain in OAuth provider consoles
2. **HTTPS**: Ensure all OAuth endpoints use HTTPS
3. **Environment Variables**: Set production environment variables securely
4. **Monitoring**: Monitor OAuth authentication logs
5. **Rate Limiting**: Implement rate limiting for OAuth endpoints
6. **Client Secrets**: Rotate client secrets regularly
7. **Redirect URIs**: Update redirect URIs for production domains

## Testing

### Manual Testing
1. Test Google OAuth flow end-to-end
2. Test Microsoft OAuth flow end-to-end
3. Test account linking functionality
4. Test account unlinking functionality
5. Test error handling for invalid credentials
6. Test redirect URI validation

### Automated Testing
```bash
# Test OAuth endpoints
curl -X GET "http://localhost:8000/api/v1/auth/google"
curl -X GET "http://localhost:8000/api/v1/auth/microsoft"

# Test OAuth callbacks (with valid code)
curl -X GET "http://localhost:8000/api/v1/auth/google/callback?code=valid_code&state=valid_state"
```

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** September 9, 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅
