# OpenPhone Integration for SEO CRM

This document describes the OpenPhone integration implementation for the CounselRank SEO CRM system.

## Overview

The OpenPhone integration allows the SEO CRM to:
- Connect phone numbers to clients
- Automatically log calls and messages
- Sync contact information
- Make outbound calls and send messages
- Track call and message history

## Backend Implementation

### Entities

#### OpenPhoneIntegration
- **Location**: `backend/src/Entity/OpenPhoneIntegration.php`
- **Purpose**: Stores phone number integrations for clients
- **Key Fields**:
  - `client`: Reference to the client
  - `phoneNumber`: The OpenPhone number
  - `displayName`: Human-readable name for the number
  - `autoLogCalls`: Whether to automatically log calls
  - `autoLogMessages`: Whether to automatically log messages
  - `syncContacts`: Whether to sync contacts
  - `isDefault`: Whether this is the default integration for the client

#### OpenPhoneCallLog
- **Location**: `backend/src/Entity/OpenPhoneCallLog.php`
- **Purpose**: Stores call history and metadata
- **Key Fields**:
  - `openPhoneCallId`: Unique identifier from OpenPhone
  - `direction`: 'inbound' or 'outbound'
  - `status`: 'answered', 'missed', 'voicemail', 'busy', 'failed'
  - `duration`: Call duration in seconds
  - `recordingUrl`: URL to call recording
  - `transcript`: Call transcript if available

#### OpenPhoneMessageLog
- **Location**: `backend/src/Entity/OpenPhoneMessageLog.php`
- **Purpose**: Stores message history and metadata
- **Key Fields**:
  - `openPhoneMessageId`: Unique identifier from OpenPhone
  - `direction`: 'inbound' or 'outbound'
  - `status`: 'sent', 'delivered', 'failed', 'pending'
  - `content`: Message text content
  - `attachments`: Array of attachment metadata

### Services

#### OpenPhoneApiService
- **Location**: `backend/src/Service/OpenPhoneApiService.php`
- **Purpose**: Handles all OpenPhone API interactions
- **Key Methods**:
  - `getPhoneNumbers()`: Fetch available phone numbers
  - `makeCall()`: Initiate outbound calls
  - `sendMessage()`: Send SMS messages
  - `syncCallLogs()`: Sync call history
  - `syncMessageLogs()`: Sync message history
  - `processCallWebhook()`: Handle call webhook events
  - `processMessageWebhook()`: Handle message webhook events

### Controllers

#### OpenPhoneController
- **Location**: `backend/src/Controller/Api/V1/OpenPhoneController.php`
- **Purpose**: REST API endpoints for OpenPhone integration
- **Endpoints**:
  - `GET /api/v1/openphone/phone-numbers`: List available phone numbers
  - `GET /api/v1/openphone/integrations`: List client integrations
  - `POST /api/v1/openphone/integrations`: Create new integration
  - `PUT /api/v1/openphone/integrations/{id}`: Update integration
  - `DELETE /api/v1/openphone/integrations/{id}`: Delete integration
  - `POST /api/v1/openphone/calls`: Make outbound call
  - `POST /api/v1/openphone/messages`: Send message
  - `POST /api/v1/openphone/integrations/{id}/sync`: Sync integration data
  - `GET /api/v1/openphone/call-logs`: Get call logs
  - `GET /api/v1/openphone/message-logs`: Get message logs

#### OpenPhoneWebhookController
- **Location**: `backend/src/Controller/Api/V1/OpenPhoneWebhookController.php`
- **Purpose**: Handle webhook events from OpenPhone
- **Endpoints**:
  - `POST /api/v1/openphone/webhooks/calls`: Handle call events
  - `POST /api/v1/openphone/webhooks/messages`: Handle message events
  - `POST /api/v1/openphone/webhooks/contacts`: Handle contact events
  - `POST /api/v1/openphone/webhooks/status`: Handle status events

## Frontend Implementation

### Services

#### OpenPhoneApiService
- **Location**: `frontend/src/services/openPhoneApi.ts`
- **Purpose**: Frontend API client for OpenPhone integration
- **Features**:
  - TypeScript interfaces for all data structures
  - HTTP client with error handling
  - Promise-based API methods

### Components

#### OpenPhoneIntegrationComponent
- **Location**: `frontend/src/components/openphone/OpenPhoneIntegration.tsx`
- **Purpose**: Main UI component for managing OpenPhone integrations
- **Features**:
  - Integration management (create, update, delete)
  - Call and message log viewing
  - Sync functionality
  - Real-time status updates

### Integration with SEO Clients Tab

The OpenPhone integration is integrated into the existing SEO Clients tab:
- **Location**: `frontend/src/components/dashboard/SeoClientsTab.tsx`
- **Changes**:
  - Added OpenPhone tab to the tab grid
  - Integrated OpenPhoneIntegrationComponent
  - Updated grid layout to accommodate new tab

## Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# OpenPhone Integration
OPENPHONE_API_KEY=your_openphone_api_key_here
OPENPHONE_BASE_URL=https://api.openphone.com/v1
OPENPHONE_WEBHOOK_SECRET=your_webhook_secret_here
```

### Service Configuration

The OpenPhone API service is configured in `backend/config/services.yaml`:

```yaml
# OpenPhone API Service
App\Service\OpenPhoneApiService:
    arguments:
        $openPhoneApiKey: '%openphone_api_key%'
        $openPhoneBaseUrl: '%openphone_base_url%'
```

## Database Migration

A database migration has been generated to create the necessary tables:
- `openphone_integrations`
- `openphone_call_logs`
- `openphone_message_logs`

Run the migration with:
```bash
php bin/console doctrine:migrations:migrate
```

## Webhook Setup

To receive real-time updates from OpenPhone, configure webhooks in your OpenPhone dashboard:

1. **Call Events**: `https://yourdomain.com/api/v1/openphone/webhooks/calls`
2. **Message Events**: `https://yourdomain.com/api/v1/openphone/webhooks/messages`
3. **Contact Events**: `https://yourdomain.com/api/v1/openphone/webhooks/contacts`
4. **Status Events**: `https://yourdomain.com/api/v1/openphone/webhooks/status`

## Usage

### Creating an Integration

1. Navigate to the SEO Clients tab
2. Click on the "OpenPhone" tab
3. Click "Add Integration"
4. Select a phone number from the dropdown
5. Configure settings (auto-logging, sync contacts, etc.)
6. Click "Create Integration"

### Making Calls

1. Go to the OpenPhone integration for a client
2. Use the "Make Call" functionality (if implemented in UI)
3. Calls will be automatically logged if auto-logging is enabled

### Viewing Logs

1. Click on an integration
2. Use "View Calls" or "View Messages" buttons
3. View detailed logs with transcripts and recordings

### Syncing Data

1. Click the "Sync" button on any integration
2. The system will fetch recent calls and messages from OpenPhone
3. New data will be stored in the local database

## Security

- All API endpoints require proper authentication
- Webhook endpoints should implement signature verification
- Sensitive data is encrypted in the database
- API keys are stored securely in environment variables

## Error Handling

- Comprehensive error handling in all API calls
- User-friendly error messages in the frontend
- Detailed logging for debugging
- Graceful fallbacks for failed operations

## Future Enhancements

- Contact synchronization
- Advanced call analytics
- Integration with CRM workflows
- Automated follow-up scheduling
- Call recording management
- Message templates
- Bulk operations

## Troubleshooting

### Common Issues

1. **API Key Issues**: Verify the OpenPhone API key is correct and has proper permissions
2. **Webhook Failures**: Check webhook URLs are accessible and properly configured
3. **Sync Problems**: Ensure the integration is active and has proper permissions
4. **Frontend Errors**: Check browser console for JavaScript errors

### Debugging

- Check application logs for detailed error information
- Use the browser developer tools to inspect API calls
- Verify database connections and migrations
- Test webhook endpoints manually

## Support

For issues related to the OpenPhone integration:
1. Check the application logs
2. Verify configuration settings
3. Test API connectivity
4. Contact the development team with specific error details
