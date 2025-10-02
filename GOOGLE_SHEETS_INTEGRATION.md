# Google Sheets Integration for Leads Import

This document describes the Google Sheets integration feature that allows importing leads directly from Google Sheets into the CRM system.

## Overview

The Google Sheets integration provides a seamless way to import lead data from Google Sheets into the leads management system. It supports flexible column mapping, data validation, and automatic lead creation.

## Features

- **Direct Google Sheets API Integration**: Fetches data directly from Google Sheets using the official API
- **Flexible Column Mapping**: Automatically maps common column names to Lead entity fields
- **Data Validation**: Validates email addresses and required fields
- **Duplicate Handling**: Option to overwrite existing leads or skip duplicates
- **Client Assignment**: Optionally assign imported leads to specific clients
- **Error Reporting**: Detailed error reporting for failed imports
- **Progress Tracking**: Real-time import progress and results

## Backend Implementation

### GoogleSheetsService

The `GoogleSheetsService` class handles all Google Sheets API interactions:

```php
// Key methods:
- fetchSheetData($spreadsheetId, $range, $accessToken)
- importLeadsFromSheet($sheetData, $clientId, $sourceId, $overwriteExisting)
- getAccessToken($refreshToken)
- extractSpreadsheetId($url)
```

### API Endpoint

**POST** `/api/v1/leads/google-sheets-import`

**Request Body:**
```json
{
  "spreadsheet_url": "https://docs.google.com/spreadsheets/d/...",
  "range": "A:Z",
  "refresh_token": "your_oauth_refresh_token",
  "client_id": "optional_client_uuid",
  "source_id": "optional_source_uuid",
  "overwrite_existing": false
}
```

**Response:**
```json
{
  "message": "Google Sheets import completed",
  "imported": 5,
  "updated": 2,
  "skipped": 1,
  "total_processed": 8,
  "errors": []
}
```

## Frontend Implementation

### GoogleSheetsImportModal Component

A React modal component that provides a user-friendly interface for Google Sheets import:

- **URL Validation**: Validates Google Sheets URL format
- **OAuth Token Input**: Secure input for refresh token
- **Range Selection**: Optional range specification
- **Client Assignment**: Dropdown for client selection
- **Options**: Overwrite existing leads checkbox
- **Progress Tracking**: Real-time import progress
- **Error Display**: Detailed error reporting

### Integration with Leads Management

The Google Sheets import is integrated into the main leads management interface with an "Import Sheets" button alongside CSV and Leadgen import options.

## Column Mapping

The system automatically maps common column names to Lead entity fields:

| Sheet Column | Lead Field | Required |
|-------------|------------|----------|
| name, full_name, fullname, contact_name | full_name | Yes |
| email, email_address | email | Yes |
| phone, phone_number, telephone | phone | No |
| firm, company, business, law_firm | firm | No |
| website, url, web_site | website | No |
| city | city | No |
| state | state | No |
| zip, zip_code, postal_code | zip_code | No |
| message, notes, comments | message | No |
| practice_areas, practice_area, services, specialties | practice_areas | No |

## Authentication Setup

### Google Cloud Console Setup

1. Go to [Google Cloud Console](https://console.developers.google.com)
2. Create a new project or select existing one
3. Enable Google Sheets API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs
6. Download credentials JSON

### Getting Refresh Token

1. Use Google OAuth 2.0 Playground
2. Select Google Sheets API v4 scope
3. Authorize and get refresh token
4. Use refresh token in the import form

### Environment Variables

Add to your `.env` file:
```env
GOOGLE_OAUTH_CLIENT_ID=your_client_id
GOOGLE_OAUTH_CLIENT_SECRET=your_client_secret
GOOGLE_OAUTH_REDIRECT_URI=http://localhost:8000/api/v1/auth/google/callback
```

## Usage Instructions

### For Users

1. **Prepare Your Google Sheet**:
   - Create a Google Sheet with headers in the first row
   - Include columns like Name, Email, Phone, Firm, etc.
   - Make sure the sheet is accessible (shared or public)

2. **Get OAuth Token**:
   - Follow the authentication setup guide
   - Obtain a refresh token from Google OAuth playground

3. **Import Leads**:
   - Go to Leads Management page
   - Click "Import Sheets" button
   - Enter Google Sheets URL
   - Enter refresh token
   - Select options (client, overwrite, etc.)
   - Click "Import from Sheets"

### For Developers

1. **Backend Testing**:
   ```bash
   # Test the service directly
   php bin/console app:test-google-sheets-import
   ```

2. **API Testing**:
   ```bash
   curl -X POST http://localhost:8000/api/v1/leads/google-sheets-import \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_JWT_TOKEN" \
     -d '{
       "spreadsheet_url": "https://docs.google.com/spreadsheets/d/...",
       "refresh_token": "your_refresh_token"
     }'
   ```

## Error Handling

The system provides comprehensive error handling:

- **Invalid URL**: Validates Google Sheets URL format
- **Authentication Errors**: Handles expired or invalid tokens
- **API Errors**: Catches and reports Google Sheets API errors
- **Data Validation**: Validates required fields and email format
- **Duplicate Handling**: Reports skipped duplicates
- **Row-level Errors**: Reports specific row errors with line numbers

## Security Considerations

- **Token Security**: Refresh tokens are handled securely and not stored
- **Access Control**: Only admin users can import leads
- **Data Validation**: All input data is validated and sanitized
- **Error Messages**: Error messages don't expose sensitive information

## Performance Considerations

- **Batch Processing**: Processes leads in batches to avoid memory issues
- **Caching**: Uses appropriate caching for repeated operations
- **Rate Limiting**: Respects Google Sheets API rate limits
- **Progress Tracking**: Provides real-time progress updates

## Troubleshooting

### Common Issues

1. **"Invalid Google Sheets URL"**
   - Ensure URL is a valid Google Sheets URL
   - Check that the sheet is accessible

2. **"Failed to obtain access token"**
   - Verify refresh token is valid
   - Check OAuth credentials in environment

3. **"No data found in sheet"**
   - Ensure sheet has data in the specified range
   - Check that headers are in the first row

4. **"Missing required fields"**
   - Ensure sheet has "name" and "email" columns
   - Check column name variations (see mapping table)

### Debug Mode

Enable debug mode in the frontend to see detailed error messages and API responses.

## Future Enhancements

- **Automatic Column Detection**: Auto-detect column types and suggest mappings
- **Template Generation**: Generate Google Sheets templates with proper headers
- **Scheduled Imports**: Set up automatic recurring imports
- **Advanced Filtering**: Filter data before import
- **Data Transformation**: Apply data transformations during import
- **Webhook Integration**: Real-time sync with Google Sheets changes

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

