# Leadgen Integration with SEO CRM

This document explains how to integrate leads from the leadgen service into the SEO CRM application with statistics tracking.

## Overview

The integration allows you to:
- Import leads from the leadgen service via JSON upload
- Track statistics for dials, contacts, interviews, and applications
- View detailed interaction history for each lead
- Monitor lead progression through the sales pipeline

## Backend Components

### 1. LeadgenIntegrationService
- **Location**: `backend/src/Service/LeadgenIntegrationService.php`
- **Purpose**: Handles conversion of leadgen data to CRM format and statistics tracking
- **Key Methods**:
  - `importLeadgenData()`: Imports leads from leadgen service
  - `trackLeadStatistics()`: Records interaction events
  - `getLeadStatistics()`: Retrieves statistics for a lead

### 2. LeadEvent Entity
- **Location**: `backend/src/Entity/LeadEvent.php`
- **Purpose**: Tracks individual interactions with leads
- **Fields**:
  - `type`: phone_call, email, meeting, note, application
  - `direction`: inbound, outbound
  - `duration`: call duration in seconds
  - `notes`: interaction notes
  - `outcome`: positive, neutral, negative
  - `next_action`: follow-up action

### 3. API Endpoints
- `POST /api/v1/leads/leadgen-import`: Import leadgen data
- `GET /api/v1/leads/{id}/events`: Get lead events
- `GET /api/v1/leads/{id}/statistics`: Get lead statistics
- `POST /api/v1/leads/{id}/events`: Create new event

## Frontend Components

### 1. LeadgenImportModal
- **Location**: `frontend/src/components/leads/LeadgenImportModal.tsx`
- **Purpose**: Upload interface for leadgen JSON files
- **Features**:
  - JSON file validation
  - Client assignment
  - Sample data download
  - Import progress tracking

### 2. LeadStatistics
- **Location**: `frontend/src/components/leads/LeadStatistics.tsx`
- **Purpose**: Display and manage lead interaction statistics
- **Features**:
  - Statistics overview (calls, emails, meetings, applications)
  - Event history timeline
  - Add new events form
  - Duration tracking

### 3. Updated LeadsManagement
- **Location**: `frontend/src/components/leads/LeadsManagement.tsx`
- **New Features**:
  - Leadgen import button
  - Click-to-view statistics
  - Enhanced lead display

## Data Format

### Leadgen Input Format
The system expects leadgen data in this format:

```json
[
  {
    "lead_id": "unique-id",
    "legal_entity": {
      "name": "Company Name",
      "alt_names": ["Alternative Name"],
      "registration_id": "12345",
      "jurisdictions": ["US"]
    },
    "vertical": "local_services",
    "website": "https://example.com",
    "emails": [
      {
        "value": "contact@example.com",
        "type": "generic",
        "verified": true,
        "provider": "gmail"
      }
    ],
    "phones": [
      {
        "value": "(555) 123-4567",
        "type": "main",
        "provider": "verizon"
      }
    ],
    "address": {
      "line1": "123 Main St",
      "line2": "Suite 100",
      "city": "New York",
      "region": "NY",
      "postal": "10001",
      "country": "US"
    },
    "reviews": {
      "count": 25,
      "rating": 4.5,
      "last_reviewed_at": "2024-01-15T10:30:00Z"
    },
    "lead_score": 85,
    "tags": ["Personal Injury", "Criminal Defense"],
    "tech_signals": ["WordPress", "Google Analytics"],
    "firmographics": {
      "employees_range": "10-50",
      "revenue_range": "$1M-$5M",
      "founded_year": 2010
    }
  }
]
```

### Converted CRM Format
The system converts leadgen data to this format:

```json
{
  "id": "uuid",
  "full_name": "Company Name",
  "email": "contact@example.com",
  "phone": "(555) 123-4567",
  "firm": "Company Name",
  "website": "https://example.com",
  "city": "New York",
  "state": "NY",
  "zip_code": "10001",
  "practice_areas": ["Personal Injury", "Criminal Defense"],
  "status": "new_lead",
  "utm_json": {
    "leadgen_data": { /* original leadgen data */ },
    "imported_at": "2024-01-15T10:30:00Z",
    "lead_score": 85,
    "vertical": "local_services"
  }
}
```

## Usage Instructions

### 1. Import Leadgen Data

1. Navigate to the Leads Management page
2. Click "Import Leadgen" button
3. Select a JSON file with leadgen data
4. Optionally assign to a client
5. Click "Import Leads"

### 2. Track Lead Statistics

1. Click on any lead in the leads list
2. View statistics overview (calls, emails, meetings, applications)
3. Click "Add Event" to record new interactions
4. Fill in event details:
   - Type: Phone Call, Email, Meeting, Note, Application
   - Direction: Inbound or Outbound
   - Duration: For phone calls (in seconds)
   - Notes: Interaction details
   - Outcome: Positive, Neutral, Negative
   - Next Action: Follow-up steps

### 3. Monitor Lead Progress

- View total statistics in the overview cards
- Track individual lead progression
- Monitor call duration and frequency
- Review interaction history

## Data Conversion Script

A Node.js script is provided to convert various leadgen formats:

```bash
cd leadgen
node convert-leadgen.js input-file.json [output-file.json]
```

The script:
- Handles different input formats
- Extracts primary contact information
- Converts to the expected format
- Provides validation and error handling

## Database Migration

Run the migration to create the lead_events table:

```bash
cd backend
php bin/console doctrine:migrations:migrate
```

## Statistics Tracking

The system automatically tracks:
- **Phone Calls**: Number of calls and total duration
- **Emails**: Number of email interactions
- **Meetings**: Number of scheduled/completed meetings
- **Applications**: Number of applications received
- **Last Contact**: Most recent interaction date

## Lead Status Progression

The system automatically updates lead status based on events:
- `new_lead` → `contacted` (after first phone call)
- `contacted` → `interview_scheduled` (after meeting scheduled)
- Any status → `application_received` (after application event)

## API Integration

### Import Leadgen Data
```javascript
const result = await apiService.importLeadgenData(leads, clientId, sourceId);
```

### Get Lead Statistics
```javascript
const stats = await apiService.getLeadStatistics(leadId);
```

### Create Lead Event
```javascript
const event = await apiService.createLeadEvent(leadId, {
  type: 'phone_call',
  direction: 'outbound',
  duration: 300,
  notes: 'Initial contact call',
  outcome: 'positive',
  next_action: 'Send follow-up email'
});
```

## Troubleshooting

### Common Issues

1. **Invalid JSON Format**: Ensure the JSON file is valid and follows the expected structure
2. **Missing Email**: Each lead must have at least one valid email address
3. **Permission Errors**: Ensure you have ROLE_AGENCY_ADMIN permissions
4. **Database Errors**: Run migrations if the lead_events table doesn't exist

### Error Messages

- "No valid email found": Lead data is missing email information
- "Invalid JSON format": The uploaded file is not valid JSON
- "Lead not found": The specified lead ID doesn't exist
- "Validation failed": Event data doesn't meet validation requirements

## Future Enhancements

Potential improvements:
- Bulk event creation
- Automated lead scoring based on interactions
- Integration with external CRM systems
- Advanced reporting and analytics
- Email template integration
- Call recording integration
