import { GooglePlacesAdapter } from '../adapters/google-places';
import { NormalizationEngine } from '../services/normalization';
import { CSVExporter } from '../services/csv-exporter';
import { Vertical } from '../types/canonical';

// Mock Google Places API response for testing
const mockGooglePlacesItem = {
  place_id: 'test_place_123',
  name: 'Test Plumbing Co.',
  formatted_address: '123 Main St, Denver, CO 80202, USA',
  geometry: {
    location: {
      lat: 39.7392,
      lng: -104.9903
    }
  },
  types: ['plumber', 'establishment'],
  rating: 4.5,
  user_ratings_total: 25,
  formatted_phone_number: '+1-303-555-0123',
  website: 'https://testplumbing.com',
  opening_hours: {
    open_now: true,
    weekday_text: [
      'Monday: 8:00 AM – 5:00 PM',
      'Tuesday: 8:00 AM – 5:00 PM',
      'Wednesday: 8:00 AM – 5:00 PM',
      'Thursday: 8:00 AM – 5:00 PM',
      'Friday: 8:00 AM – 5:00 PM',
      'Saturday: 9:00 AM – 3:00 PM',
      'Sunday: Closed'
    ]
  },
  business_status: 'OPERATIONAL'
};

describe('LeadGen Phase 0 Tests', () => {
  let googlePlacesAdapter: GooglePlacesAdapter;
  let normalizationEngine: NormalizationEngine;
  let csvExporter: CSVExporter;

  beforeEach(() => {
    googlePlacesAdapter = new GooglePlacesAdapter('test_api_key');
    normalizationEngine = new NormalizationEngine();
    csvExporter = new CSVExporter('./test-exports');
    normalizationEngine.registerAdapter(googlePlacesAdapter);
  });

  test('Google Places adapter should normalize data correctly', () => {
    const normalized = googlePlacesAdapter.normalize(mockGooglePlacesItem);
    
    expect(normalized.legal_entity?.name).toBe('Test Plumbing Co.');
    expect(normalized.website).toBe('https://testplumbing.com');
    expect(normalized.phones).toHaveLength(1);
    expect(normalized.phones?.[0]?.value).toBe('+1-303-555-0123');
    expect(normalized.geo?.lat).toBe(39.7392);
    expect(normalized.geo?.lon).toBe(-104.9903);
    expect(normalized.reviews?.rating).toBe(4.5);
    expect(normalized.reviews?.count).toBe(25);
    expect(normalized.provenance).toHaveLength(4); // name, phone, website, rating
  });

  test('Normalization engine should create canonical leads', async () => {
    const rawItems = [{
      provider: 'google_places',
      item: mockGooglePlacesItem,
      providerKey: 'test_place_123'
    }];

    const leads = await normalizationEngine.normalizeLeads(rawItems, Vertical.LOCAL_SERVICES);
    
    expect(leads).toHaveLength(1);
    expect(leads[0].lead_id).toBeDefined();
    expect(leads[0].vertical).toBe(Vertical.LOCAL_SERVICES);
    expect(leads[0].source_records).toHaveLength(1);
    expect(leads[0].source_records[0].provider).toBe('google_places');
  });

  test('Scoring should assign appropriate scores', async () => {
    const rawItems = [{
      provider: 'google_places',
      item: mockGooglePlacesItem,
      providerKey: 'test_place_123'
    }];

    const leads = await normalizationEngine.normalizeLeads(rawItems, Vertical.LOCAL_SERVICES);
    const scoredLeads = normalizationEngine.scoreLeads(leads);
    
    expect(scoredLeads[0].lead_score).toBeGreaterThan(0);
    expect(scoredLeads[0].score_explanations).toContain('High rating: 4.5');
    expect(scoredLeads[0].score_explanations).toContain('Good review count: 25');
    expect(scoredLeads[0].score_explanations).toContain('Has website');
    expect(scoredLeads[0].score_explanations).toContain('Has phone number');
  });

  test('CSV exporter should generate valid CSV', async () => {
    const rawItems = [{
      provider: 'google_places',
      item: mockGooglePlacesItem,
      providerKey: 'test_place_123'
    }];

    const leads = await normalizationEngine.normalizeLeads(rawItems, Vertical.LOCAL_SERVICES);
    const scoredLeads = normalizationEngine.scoreLeads(leads);
    
    const csvContent = csvExporter['generateCSV'](scoredLeads, false, false);
    
    expect(csvContent).toContain('lead_id,company_name,vertical');
    expect(csvContent).toContain('Test Plumbing Co.');
    expect(csvContent).toContain('local_services');
    expect(csvContent).toContain('https://testplumbing.com');
  });

  test('Export stats should provide correct metrics', async () => {
    const rawItems = [{
      provider: 'google_places',
      item: mockGooglePlacesItem,
      providerKey: 'test_place_123'
    }];

    const leads = await normalizationEngine.normalizeLeads(rawItems, Vertical.LOCAL_SERVICES);
    const stats = csvExporter.getExportStats(leads);
    
    expect(stats.totalLeads).toBe(1);
    expect(stats.leadsWithWebsite).toBe(1);
    expect(stats.leadsWithPhone).toBe(1);
    expect(stats.leadsWithEmail).toBe(0); // No email in mock data
    expect(stats.verticalBreakdown[Vertical.LOCAL_SERVICES]).toBe(1);
  });
});
