# Technology Stack Detection Integration

This document describes the implementation of Wappalyzer technology stack detection in the SEO Clients CRM system.

## Overview

The technology stack detection feature allows users to analyze the technology stack of lead websites directly from the lead details modal. This provides valuable insights into the technologies used by potential clients, helping with:

- Understanding client technical requirements
- Identifying potential integration opportunities
- Assessing technical complexity for SEO projects
- Competitive analysis

## Architecture

### Frontend Components

1. **TechStackService** (`frontend/src/services/techStackService.ts`)
   - Handles communication with Wappalyzer API
   - Provides utility methods for technology categorization
   - Manages API key configuration

2. **TechStackDisplay** (`frontend/src/components/leads/TechStackDisplay.tsx`)
   - React component for displaying technology stack information
   - Shows technologies grouped by category
   - Includes loading states and error handling
   - Provides confidence scores and external links

3. **LeadDetailsModal** (Enhanced)
   - Integrated tech stack display
   - "Analyze Website" button for on-demand analysis
   - Real-time tech stack updates

### Backend Services

1. **TechStackService** (`backend/src/Service/TechStackService.php`)
   - Symfony service for Wappalyzer API integration
   - Handles URL normalization and API communication
   - Provides utility methods for technology analysis

2. **LeadsController** (Enhanced)
   - New endpoints for tech stack operations:
     - `GET /api/v1/leads/{id}/tech-stack` - Get existing tech stack data
     - `POST /api/v1/leads/{id}/tech-stack` - Analyze website tech stack

## API Endpoints

### Get Technology Stack
```
GET /api/v1/leads/{id}/tech-stack
```

**Response:**
```json
{
  "techStack": {
    "url": "https://example.com",
    "technologies": [
      {
        "name": "WordPress",
        "confidence": 95,
        "version": "6.4",
        "categories": ["CMS"],
        "website": "https://wordpress.org",
        "description": "WordPress is a content management system"
      }
    ],
    "lastAnalyzed": "2024-01-15T10:30:00Z"
  }
}
```

### Analyze Technology Stack
```
POST /api/v1/leads/{id}/tech-stack
```

**Response:**
```json
{
  "techStack": {
    "url": "https://example.com",
    "technologies": [...],
    "lastAnalyzed": "2024-01-15T10:30:00Z"
  }
}
```

## Configuration

### Environment Variables

#### Backend (.env)
```bash
# Wappalyzer API for Technology Stack Detection
WAPPALYZER_API_KEY=your_wappalyzer_api_key_here
```

#### Frontend (.env.local)
```bash
# Wappalyzer API for Technology Stack Detection
NEXT_PUBLIC_WAPPALYZER_API_KEY=your_wappalyzer_api_key_here
```

### Service Configuration

The `TechStackService` is configured in `backend/config/services.yaml`:

```yaml
# Tech Stack Service
App\Service\TechStackService:
    arguments:
        $wappalyzerApiKey: '%wappalyzer_api_key%'
```

## Usage

### For Users

1. **View Lead Details**: Open any lead that has a website URL
2. **Technology Stack Section**: The tech stack section appears below the lead details
3. **Analyze Website**: Click "Analyze Website" to get real-time technology detection
4. **View Results**: Technologies are grouped by category with confidence scores

### For Developers

#### Frontend Integration

```typescript
import { techStackService } from '../services/techStackService';

// Analyze a website
const result = await techStackService.analyzeWebsite('https://example.com');

// Get technology categories
const categories = techStackService.getTechnologyCategories(result.technologies);

// Check for specific technology
const hasWordPress = techStackService.hasTechnology(result.technologies, 'WordPress');
```

#### Backend Integration

```php
use App\Service\TechStackService;

// Inject the service
public function __construct(
    private TechStackService $techStackService
) {}

// Analyze a website
$result = $this->techStackService->analyzeWebsite('https://example.com');

// Get technology summary
$summary = $this->techStackService->getTechStackSummary($result['technologies']);
```

## Technology Categories

The system detects and categorizes technologies into the following categories:

- **Web Servers**: Apache, Nginx, IIS
- **Programming Languages**: PHP, Python, Node.js, Java
- **Databases**: MySQL, PostgreSQL, MongoDB
- **CMS**: WordPress, Drupal, Joomla
- **E-commerce**: WooCommerce, Shopify, Magento
- **JavaScript Frameworks**: React, Vue.js, Angular
- **Analytics**: Google Analytics, Adobe Analytics
- **CDN**: Cloudflare, AWS CloudFront
- **Security**: SSL certificates, security headers
- **Web Hosting**: AWS, Google Cloud, Azure

## Error Handling

The system includes comprehensive error handling:

1. **API Key Missing**: Shows configuration error
2. **Network Issues**: Displays retry option
3. **Invalid URLs**: Validates and normalizes URLs
4. **Rate Limiting**: Handles Wappalyzer API limits
5. **Timeout**: 30-second timeout for API calls

## Performance Considerations

1. **Caching**: Results are not currently cached (future enhancement)
2. **Rate Limiting**: Wappalyzer API has rate limits
3. **Async Processing**: Analysis happens synchronously (could be improved)
4. **Database Storage**: Tech stack data is not persisted (future enhancement)

## Future Enhancements

1. **Database Storage**: Store tech stack results in database
2. **Caching**: Implement Redis caching for frequent requests
3. **Batch Analysis**: Analyze multiple websites simultaneously
4. **Historical Data**: Track technology changes over time
5. **Custom Categories**: Allow custom technology categorization
6. **Integration Insights**: Suggest integration opportunities
7. **Competitive Analysis**: Compare tech stacks across leads

## Security Considerations

1. **API Key Protection**: Store API keys securely
2. **URL Validation**: Validate and sanitize URLs
3. **Rate Limiting**: Implement client-side rate limiting
4. **Error Messages**: Don't expose sensitive information in errors

## Troubleshooting

### Common Issues

1. **"API key not configured"**: Set the WAPPALYZER_API_KEY environment variable
2. **"Analysis failed"**: Check network connectivity and API key validity
3. **"No technologies detected"**: Website might not have detectable technologies
4. **Slow response**: Wappalyzer API can be slow for complex websites

### Debug Mode

Enable debug logging in the backend to see detailed API interactions:

```php
// In TechStackService
$this->logger->debug('Wappalyzer API request', [
    'url' => $url,
    'response' => $data
]);
```

## API Documentation

For complete Wappalyzer API documentation, visit:
https://www.wappalyzer.com/docs/api/v2/lookup/

## Support

For issues related to:
- **Wappalyzer API**: Contact Wappalyzer support
- **Integration Issues**: Check the application logs
- **Configuration**: Verify environment variables are set correctly
