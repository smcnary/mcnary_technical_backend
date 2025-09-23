# Leadgen Execution Interface

This document describes the admin-only interface for running leadgen campaigns directly from the SEO CRM application.

## Overview

The Leadgen Execution Interface allows system administrators to:
- Configure and execute lead generation campaigns
- Monitor campaign progress and results
- Import generated leads directly into the CRM
- Track campaign costs and performance metrics

## Access Control

- **Admin Only**: Requires `ROLE_SYSTEM_ADMIN` permission
- **Access Points**:
  - Main Dashboard: `/dashboard?tab=leadgen`
  - Admin Portal: `/admin/leadgen`

## Interface Components

### 1. Campaign Configuration Form

#### Basic Configuration
- **Campaign Name**: Unique identifier for the campaign
- **Vertical**: Business vertical (local_services, b2b_saas, ecommerce, healthcare, real_estate, other)
- **Client Assignment**: Optional assignment to specific client

#### Geographic Settings
- **City**: Target city for lead generation
- **Region**: State/province code
- **Country**: Country code (default: US)
- **Radius**: Search radius in kilometers (default: 30)

#### Filters
- **Minimum Rating**: Minimum business rating to include
- **Keywords**: Required keywords for businesses
- **Exclude Keywords**: Keywords to exclude from results
- **Max Results**: Maximum number of leads to generate

#### Sources & Budget
- **Data Sources**: Available lead sources (Google Places, Yelp, Facebook, LinkedIn)
- **Max Budget**: Maximum cost in USD
- **Scheduling**: Enable/disable campaign scheduling

### 2. Campaign Execution

#### Execution Process
1. **Validation**: Client-side validation of required fields
2. **API Call**: Backend service calls leadgen service
3. **Processing**: Leadgen service generates leads
4. **Import**: Generated leads are imported into CRM
5. **Results**: Display execution results and statistics

#### Progress Monitoring
- Real-time execution progress indicator
- Estimated completion time
- Current processing status

### 3. Results Display

#### Campaign Results
- **Leads Generated**: Total leads found by leadgen service
- **Leads Imported**: New leads added to CRM
- **Leads Updated**: Existing leads updated with new data
- **Leads Skipped**: Duplicate leads skipped
- **Execution Time**: Campaign duration
- **Cost**: Actual cost incurred

#### Error Handling
- Detailed error messages for failed operations
- Validation error display
- Service connectivity issues

## Backend Architecture

### 1. LeadgenExecutionService

**Location**: `backend/src/Service/LeadgenExecutionService.php`

**Key Methods**:
- `executeCampaign()`: Main execution method
- `validateCampaignConfig()`: Configuration validation
- `prepareLeadgenRequest()`: Request preparation
- `callLeadgenService()`: Service communication
- `processLeadgenResults()`: Result processing

### 2. LeadgenController

**Location**: `backend/src/Controller/Api/V1/LeadgenController.php`

**Endpoints**:
- `POST /api/v1/admin/leadgen/execute`: Execute campaign
- `GET /api/v1/admin/leadgen/verticals`: Get available verticals
- `GET /api/v1/admin/leadgen/sources`: Get available sources
- `GET /api/v1/admin/leadgen/template`: Get campaign template
- `GET /api/v1/admin/leadgen/status/{campaignId}`: Get campaign status

### 3. Service Integration

**Leadgen Service Communication**:
- HTTP client communication with leadgen service
- JSON request/response format
- Error handling and timeout management
- Result processing and lead import

## Frontend Architecture

### 1. LeadgenExecution Component

**Location**: `frontend/src/components/admin/LeadgenExecution.tsx`

**Features**:
- Campaign configuration form
- Real-time validation
- Progress monitoring
- Results display
- Error handling

### 2. API Integration

**Methods**:
- `executeLeadgenCampaign()`: Execute campaign
- `getLeadgenVerticals()`: Get verticals
- `getLeadgenSources()`: Get sources
- `getLeadgenTemplate()`: Get template
- `getLeadgenCampaignStatus()`: Get status

### 3. State Management

**Loading States**:
- Campaign execution progress
- Data loading indicators
- Error state management

## Usage Instructions

### 1. Access the Interface

**Option A: Main Dashboard**
1. Navigate to the main dashboard
2. Click on the "Leadgen" tab (admin only)
3. Configure and execute campaigns

**Option B: Admin Portal**
1. Navigate to `/admin/leadgen`
2. Use the dedicated admin interface
3. Access additional admin features

### 2. Configure a Campaign

1. **Basic Settings**:
   - Enter campaign name
   - Select business vertical
   - Optionally assign to client

2. **Geographic Settings**:
   - Enter target city and region
   - Set search radius
   - Specify country

3. **Filters**:
   - Set minimum rating threshold
   - Add required keywords
   - Add exclusion keywords
   - Set maximum results

4. **Sources & Budget**:
   - Select data sources
   - Set maximum budget
   - Configure scheduling

### 3. Execute Campaign

1. Click "Execute Campaign" button
2. Monitor progress indicator
3. Review results when complete
4. Check imported leads in CRM

### 4. Monitor Results

- View execution statistics
- Check cost and duration
- Review any errors
- Access generated leads

## Configuration Examples

### Example 1: Local Attorneys Campaign

```json
{
  "name": "Tulsa Attorneys Campaign",
  "vertical": "local_services",
  "geo": {
    "city": "Tulsa",
    "region": "OK",
    "country": "US",
    "radius_km": 30
  },
  "filters": {
    "min_rating": 3.5,
    "keywords": ["attorney", "lawyer", "legal"],
    "exclude_keywords": ["criminal", "defense"],
    "max_results": 100
  },
  "sources": ["google_places", "yelp"],
  "budget": {
    "max_cost_usd": 50
  }
}
```

### Example 2: Healthcare Providers Campaign

```json
{
  "name": "Denver Healthcare Campaign",
  "vertical": "healthcare",
  "geo": {
    "city": "Denver",
    "region": "CO",
    "country": "US",
    "radius_km": 50
  },
  "filters": {
    "min_rating": 4.0,
    "keywords": ["doctor", "clinic", "medical"],
    "exclude_keywords": ["veterinary", "animal"],
    "max_results": 75
  },
  "sources": ["google_places"],
  "budget": {
    "max_cost_usd": 30
  }
}
```

## Error Handling

### Common Errors

1. **Validation Errors**:
   - Missing required fields
   - Invalid field values
   - Configuration conflicts

2. **Service Errors**:
   - Leadgen service unavailable
   - Network connectivity issues
   - Service timeout errors

3. **Import Errors**:
   - Duplicate lead handling
   - Data format issues
   - Database constraints

### Error Resolution

1. **Check Configuration**: Ensure all required fields are filled
2. **Verify Service**: Confirm leadgen service is running
3. **Review Logs**: Check backend logs for detailed errors
4. **Retry Operation**: Attempt execution again after fixes

## Performance Considerations

### Optimization Tips

1. **Batch Size**: Limit max_results to reasonable numbers
2. **Geographic Scope**: Use appropriate radius for target area
3. **Keyword Selection**: Use specific, relevant keywords
4. **Source Selection**: Choose most relevant data sources

### Monitoring

1. **Execution Time**: Track campaign duration
2. **Cost Tracking**: Monitor budget usage
3. **Success Rate**: Measure lead generation success
4. **Quality Metrics**: Assess lead quality and relevance

## Security Considerations

### Access Control

- Admin-only access enforced at multiple levels
- API endpoint protection with role-based permissions
- Frontend route protection

### Data Security

- Secure communication with leadgen service
- Input validation and sanitization
- Error message sanitization

## Troubleshooting

### Common Issues

1. **Service Not Responding**:
   - Check leadgen service status
   - Verify network connectivity
   - Review service logs

2. **Configuration Errors**:
   - Validate all required fields
   - Check field value ranges
   - Review configuration format

3. **Import Failures**:
   - Check database connectivity
   - Verify lead data format
   - Review constraint violations

### Debug Steps

1. Check browser console for frontend errors
2. Review backend logs for service errors
3. Test API endpoints directly
4. Verify leadgen service status
5. Check database connectivity

## Future Enhancements

### Planned Features

1. **Campaign Templates**: Save and reuse configurations
2. **Scheduled Campaigns**: Automated campaign execution
3. **Advanced Analytics**: Detailed performance metrics
4. **Bulk Operations**: Multiple campaign execution
5. **Integration APIs**: Third-party service integration

### Performance Improvements

1. **Async Processing**: Background campaign execution
2. **Caching**: Configuration and result caching
3. **Optimization**: Improved service communication
4. **Monitoring**: Enhanced progress tracking
