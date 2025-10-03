# Google Maps Platform API Keys Configuration

This document outlines the API keys required for the leadgen system and how to configure them properly.

## Required API Keys

### Google Maps Platform APIs

The leadgen system integrates with multiple Google Maps Platform APIs. All APIs require a single Google Maps Platform API key.

#### Core APIs (Required)
- **Places API (Nearby Search)** - Primary source for business discovery
- **Places API (Place Details)** - Enriches business data with contact information
- **Places API (Text Search)** - Alternative search method for enhanced coverage

#### Optional APIs (Recommended for Enhanced Functionality)
- **Geocoding API** - Address validation and geocoding
- **Geolocation API** - IP-based location detection
- **Directions API** - Distance calculations between locations
- **Distance Matrix API** - Multi-point distance calculations
- **Routes API** - Optimized route planning for sales territories
- **Address Validation API** - Address verification and standardization

## API Key Setup

### 1. Google Cloud Console Configuration

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create or select a project
3. Enable the required APIs:
   ```
   - Places API
   - Geocoding API
   - Geolocation API
   - Directions API
   - Distance Matrix API
   - Routes API
   - Address Validation API
   ```

### 2. Create API Key

1. Navigate to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the generated API key

### 3. Configure API Key Restrictions (Recommended)

For security, configure API key restrictions:

#### Application Restrictions
- **HTTP referrers**: Restrict to your domain
- **IP addresses**: Restrict to your server IPs
- **Android apps**: For mobile applications
- **iOS apps**: For iOS applications

#### API Restrictions
- Select "Restrict key" and choose:
  - Places API
  - Geocoding API
  - Geolocation API
  - Directions API
  - Distance Matrix API
  - Routes API
  - Address Validation API

## Environment Configuration

### Environment Variables

Set the following environment variable in your `.env` file:

```bash
# Google Maps Platform API Key
GOOGLE_PLACES_API_KEY=AIzaSyD0nflqNl0BGRb15qDDnqq4FlkASbV-_bs
```

### Alternative Configuration Methods

#### 1. Environment Variable (Recommended)
```bash
export GOOGLE_PLACES_API_KEY=AIzaSyD0nflqNl0BGRb15qDDnqq4FlkASbV-_bs
```

#### 2. Direct Configuration in Code
```typescript
import { GooglePlacesAdapter } from './src/adapters/google-places';

const adapter = new GooglePlacesAdapter('AIzaSyD0nflqNl0BGRb15qDDnqq4FlkASbV-_bs');
```

#### 3. Configuration File
```typescript
// config/api-keys.ts
export const API_KEYS = {
  GOOGLE_PLACES: process.env.GOOGLE_PLACES_API_KEY || 'your-default-key'
};
```

## Usage Examples

### Basic Usage
```typescript
import { GooglePlacesAdapter } from './src/adapters/google-places';

// Initialize with API key
const adapter = new GooglePlacesAdapter(process.env.GOOGLE_PLACES_API_KEY!);

// Plan queries for a campaign
const queryPlan = await adapter.planQueries(campaignSpec);

// Fetch results
const results = await adapter.fetchPage(queryPlan.sources[0]);
```

### Error Handling
```typescript
try {
  const adapter = new GooglePlacesAdapter(apiKey);
  const results = await adapter.fetchPage(query);
} catch (error) {
  if (error.message.includes('API key')) {
    console.error('Invalid or missing API key');
  } else if (error.message.includes('quota')) {
    console.error('API quota exceeded');
  } else {
    console.error('API request failed:', error.message);
  }
}
```

## Rate Limits and Quotas

### Free Tier Limits
- **Places API (Nearby Search)**: 1,000 requests/day
- **Places API (Place Details)**: 100,000 requests/day
- **Places API (Text Search)**: 1,000 requests/day
- **Geocoding API**: 40,000 requests/day
- **Geolocation API**: 40,000 requests/day

### Paid Tier Limits
- Higher limits available with billing enabled
- Custom quotas can be set in Google Cloud Console

### Monitoring Usage
1. Go to Google Cloud Console > APIs & Services > Quotas
2. Monitor usage for each API
3. Set up billing alerts for cost control

## Security Best Practices

### 1. API Key Security
- Never commit API keys to version control
- Use environment variables for configuration
- Rotate keys regularly
- Monitor usage for anomalies

### 2. Request Restrictions
- Implement rate limiting in your application
- Use HTTPS for all API requests
- Validate and sanitize input parameters
- Implement proper error handling

### 3. Cost Management
- Monitor API usage and costs
- Set up billing alerts
- Use caching to reduce API calls
- Implement efficient query strategies

## Troubleshooting

### Common Issues

#### 1. "API key not valid" Error
- Verify API key is correct
- Check if APIs are enabled in Google Cloud Console
- Ensure API key has proper permissions

#### 2. "Quota exceeded" Error
- Check daily quota limits
- Monitor usage in Google Cloud Console
- Consider upgrading to paid tier

#### 3. "Request denied" Error
- Verify API restrictions settings
- Check if IP address is whitelisted
- Ensure referrer restrictions are correct

#### 4. "ZERO_RESULTS" Response
- Verify search parameters
- Check if location coordinates are correct
- Try broader search terms

### Debug Mode
Enable debug logging to troubleshoot issues:

```typescript
// Enable debug logging
process.env.LOG_LEVEL = 'debug';

// The adapter will log detailed information about API requests
const adapter = new GooglePlacesAdapter(apiKey);
```

## Cost Optimization

### 1. Efficient Querying
- Use specific location coordinates
- Implement proper radius limits
- Cache results when possible
- Batch requests efficiently

### 2. Smart Caching
```typescript
// Implement caching to reduce API calls
const cache = new Map();

async function getCachedPlaceDetails(placeId: string) {
  if (cache.has(placeId)) {
    return cache.get(placeId);
  }
  
  const details = await adapter.getPlaceDetails(placeId);
  cache.set(placeId, details);
  return details;
}
```

### 3. Rate Limiting
```typescript
// Implement rate limiting to stay within quotas
import { RateLimiter } from 'limiter';

const limiter = new RateLimiter(100, 'second'); // 100 requests per second

async function rateLimitedRequest(url: string) {
  await limiter.removeTokens(1);
  return fetch(url);
}
```

## Support and Resources

### Documentation
- [Google Places API Documentation](https://developers.google.com/maps/documentation/places/web-service/overview)
- [Google Maps Platform APIs](https://developers.google.com/maps/documentation)
- [API Key Best Practices](https://developers.google.com/maps/api-key-best-practices)

### Support Channels
- [Google Maps Platform Support](https://developers.google.com/maps/support)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/google-places-api)
- [Google Cloud Support](https://cloud.google.com/support)

### Community
- [Google Maps Platform Community](https://www.google.com/url?q=https://developers.google.com/maps/community)
- [GitHub Issues](https://github.com/googlemaps/google-maps-services-js/issues)
