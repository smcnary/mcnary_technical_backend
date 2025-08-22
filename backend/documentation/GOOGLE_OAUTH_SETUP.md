# Google OAuth Setup Guide

This guide explains how to set up Google OAuth authentication for the CounselRank.legal backend application.

## Prerequisites

- Google Cloud Platform account
- Access to Google Cloud Console
- Domain verification (for production use)

## Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API and Google OAuth2 API

## Step 2: Configure OAuth Consent Screen

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

## Step 3: Create OAuth 2.0 Credentials

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

## Step 4: Configure Environment Variables

1. Copy the environment template:
   ```bash
   cp env-template.txt .env.local
   ```

2. Update `.env.local` with your Google OAuth credentials:
   ```bash
   # Google OAuth Configuration
   GOOGLE_OAUTH_CLIENT_ID="your_actual_client_id"
   GOOGLE_OAUTH_CLIENT_SECRET="your_actual_client_secret"
   GOOGLE_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/google/callback"
   
   # Frontend URL
   APP_FRONTEND_URL="http://localhost:3000"
   ```

## Step 5: Update Frontend Configuration

Update your frontend application to use the correct backend OAuth endpoints:

```typescript
// Example frontend OAuth configuration
const GOOGLE_OAUTH_URL = 'http://localhost:8000/api/v1/auth/google';
const GOOGLE_OAUTH_CALLBACK_URL = 'http://localhost:8000/api/v1/auth/google/callback';
```

## Step 6: Test the Integration

1. Start your backend server:
   ```bash
   symfony server:start
   ```

2. Navigate to the Google OAuth endpoint:
   ```
   http://localhost:8000/api/v1/auth/google
   ```

3. You should be redirected to Google's OAuth consent screen
4. After authorization, you'll be redirected back with a JWT token

## API Endpoints

### Google OAuth Login Initiation
- **URL**: `GET /api/v1/auth/google`
- **Description**: Initiates Google OAuth flow
- **Response**: Redirects to Google OAuth consent screen

### Google OAuth Callback
- **URL**: `GET /api/v1/auth/google/callback`
- **Description**: Handles Google OAuth callback
- **Parameters**: `code`, `state` (from Google)
- **Response**: Redirects to frontend with JWT token

### Link Google Account
- **URL**: `POST /api/v1/auth/google/link`
- **Description**: Links existing Google account to current user
- **Headers**: `Authorization: Bearer <jwt_token>`
- **Body**: `{"access_token": "google_access_token"}`

### Unlink Google Account
- **URL**: `POST /api/v1/auth/google/unlink`
- **Description**: Unlinks Google account from current user
- **Headers**: `Authorization: Bearer <jwt_token>`

## Security Considerations

1. **State Parameter**: The OAuth flow includes a state parameter to prevent CSRF attacks
2. **HTTPS**: Always use HTTPS in production
3. **Client Secret**: Never expose the client secret in frontend code
4. **Token Storage**: Store OAuth tokens securely in the database
5. **Scope Limitation**: Only request necessary scopes

## Troubleshooting

### Common Issues

1. **Invalid Redirect URI**: Ensure the redirect URI in Google Cloud Console matches exactly
2. **Missing Scopes**: Verify all required scopes are enabled in OAuth consent screen
3. **Domain Verification**: For production, verify your domain ownership
4. **CORS Issues**: Ensure proper CORS configuration for cross-origin requests

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

1. **Domain Verification**: Verify your domain in Google Cloud Console
2. **HTTPS**: Ensure all OAuth endpoints use HTTPS
3. **Environment Variables**: Set production environment variables securely
4. **Monitoring**: Monitor OAuth authentication logs
5. **Rate Limiting**: Implement rate limiting for OAuth endpoints

## Support

For issues related to:
- Google OAuth setup: Check Google Cloud Console documentation
- Backend implementation: Check Symfony logs and API responses
- Frontend integration: Verify redirect URIs and error handling
