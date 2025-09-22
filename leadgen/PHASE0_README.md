# LeadGen - Multi-Source Lead Generator

## Phase 0 Implementation

This is the Phase 0 implementation of the Multi-Source Lead Generator as specified in the tech spec.

### Features Implemented

- ✅ Canonical lead schema and data models
- ✅ Google Places source adapter with API integration
- ✅ Normalization engine to convert Google Places data to canonical format
- ✅ Basic scoring system (rules-based)
- ✅ CSV export functionality
- ✅ Campaign orchestrator for running lead generation campaigns
- ✅ REST API endpoints for campaign management

### Getting Started

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Set up environment variables:**
   ```bash
   cp env.example .env
   # Edit .env and add your Google Places API key
   ```

3. **Build the project:**
   ```bash
   npm run build
   ```

4. **Start the server:**
   ```bash
   npm start
   ```

   Or for development:
   ```bash
   npm run dev
   ```

### API Endpoints

- `GET /health` - Health check
- `POST /v1/campaigns` - Create and run a campaign
- `GET /v1/campaigns/:runId/status` - Get campaign run status
- `GET /v1/campaigns/runs` - Get all campaign runs
- `GET /v1/verticals` - Get supported verticals
- `GET /v1/sources` - Get supported data sources
- `GET /v1/campaigns/example` - Get example campaign configuration

### Example Usage

1. **Start the server:**
   ```bash
   npm run dev
   ```

2. **Create a campaign using curl:**
   ```bash
   curl -X POST http://localhost:3000/v1/campaigns \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Denver Plumbers Test",
       "vertical": "local_services",
       "geo": {
         "city": "Denver",
         "region": "CO",
         "country": "US",
         "radius_km": 25
       },
       "filters": {
         "min_rating": 4.0,
         "keywords": ["plumber", "plumbing"]
       },
       "sources": ["google_places"],
       "enrichment": [],
       "budget": {
         "max_cost_usd": 50
       },
       "schedule": {
         "enabled": false
       },
       "destinations": []
     }'
   ```

3. **Check the campaign status:**
   ```bash
   curl http://localhost:3000/v1/campaigns/{run_id}/status
   ```

4. **Find exported CSV files in the `./exports` directory**

### Project Structure

```
src/
├── adapters/          # Source adapters (Google Places)
├── api/              # REST API server
├── services/         # Core services (orchestrator, normalization, CSV export)
├── types/           # TypeScript type definitions
├── utils/           # Utility functions
└── index.ts         # Main entry point
```

### Next Steps (Phase 1)

- Add Yelp Fusion adapter
- Implement Clearbit enrichment
- Add deduplication logic
- Create HubSpot destination adapter
- Build admin console UI

### Development

- **Linting:** `npm run lint`
- **Testing:** `npm test`
- **Build:** `npm run build`
