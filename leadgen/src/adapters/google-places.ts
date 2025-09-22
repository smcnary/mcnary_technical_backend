import fetch from 'node-fetch';
import { Vertical, CanonicalLead, ProvenanceEntry } from '../types/canonical';
import { CampaignSpec, QueryPlan, ProviderQuery, Page, RateLimitPolicy } from '../types/campaign';

export interface SourceAdapter {
  name: string;
  supportsVerticals: Vertical[];
  planQueries(input: CampaignSpec): Promise<QueryPlan>;
  fetchPage(query: ProviderQuery, cursor?: string): Promise<Page<any>>;
  normalize(item: any): Partial<CanonicalLead> & { provenance: ProvenanceEntry[] };
  rateLimit(): RateLimitPolicy;
}

export interface GooglePlacesItem {
  place_id: string;
  name: string;
  formatted_address: string;
  geometry: {
    location: {
      lat: number;
      lng: number;
    };
  };
  types: string[];
  rating?: number;
  user_ratings_total?: number;
  formatted_phone_number?: string;
  international_phone_number?: string;
  website?: string;
  opening_hours?: {
    open_now?: boolean;
    weekday_text?: string[];
  };
  business_status?: string;
  vicinity?: string;
}

export interface GooglePlacesResponse {
  results: GooglePlacesItem[];
  next_page_token?: string;
  status: string;
}

export class GooglePlacesAdapter implements SourceAdapter {
  name = 'google_places';
  supportsVerticals = [Vertical.LOCAL_SERVICES, Vertical.HEALTHCARE, Vertical.REAL_ESTATE];
  
  private apiKey: string;
  private baseUrl = 'https://maps.googleapis.com/maps/api/place';

  constructor(apiKey: string) {
    this.apiKey = apiKey;
  }

  async planQueries(input: CampaignSpec): Promise<QueryPlan> {
    const queries: ProviderQuery[] = [];
    
    // Build search query based on campaign spec
    const queryParams: Record<string, any> = {
      key: this.apiKey,
      fields: 'place_id,name,formatted_address,geometry,types,rating,user_ratings_total,formatted_phone_number,website,opening_hours,business_status'
    };

    // Add location-based search
    if (input.geo.lat && input.geo.lon) {
      queryParams.location = `${input.geo.lat},${input.geo.lon}`;
      queryParams.radius = input.geo.radius_km * 1000; // Convert km to meters
    } else if (input.geo.city && input.geo.region) {
      // Use hardcoded coordinates for known cities (until Geocoding API is enabled)
      const coordinates = this.getCityCoordinates(input.geo.city, input.geo.region);
      if (coordinates) {
        queryParams.location = `${coordinates.lat},${coordinates.lng}`;
        queryParams.radius = input.geo.radius_km * 1000;
      } else {
        throw new Error(`Could not find coordinates for city: ${input.geo.city}, ${input.geo.region}`);
      }
    }

    // Add filters
    if (input.filters.keywords && input.filters.keywords.length > 0) {
      queryParams.keyword = input.filters.keywords.join(' ');
    }

    if (input.filters.types && input.filters.types.length > 0) {
      queryParams.type = input.filters.types[0]; // Google Places only supports one type
    }

    if (input.filters.min_rating) {
      queryParams.minprice = Math.floor(input.filters.min_rating);
    }

    if (input.filters.open_now) {
      queryParams.opennow = true;
    }

    queries.push({
      provider: this.name,
      query_params: queryParams,
      pagination: {
        has_more: true
      }
    });

    // Estimate cost and leads
    const estimated_cost = this.estimateCost(queries.length);
    const estimated_leads = this.estimateLeads(input);

    return {
      sources: queries,
      estimated_cost,
      estimated_leads
    };
  }

  async fetchPage(query: ProviderQuery, cursor?: string): Promise<Page<GooglePlacesItem>> {
    const params = new URLSearchParams({
      ...query.query_params,
      pagetoken: cursor || ''
    });

    const url = `${this.baseUrl}/nearbysearch/json?${params}`;
    
    try {
      const response = await fetch(url);
      const data = await response.json() as GooglePlacesResponse;

      if (data.status !== 'OK' && data.status !== 'ZERO_RESULTS') {
        throw new Error(`Google Places API error: ${data.status}`);
      }

      // Fetch detailed information for each place to get phone numbers and websites
      const enrichedItems = await this.enrichPlacesWithDetails(data.results || []);

      return {
        items: enrichedItems,
        next_cursor: data.next_page_token,
        has_more: !!data.next_page_token,
        total_estimated: data.results?.length || 0
      };
    } catch (error) {
      console.error('Error fetching Google Places data:', error);
      throw error;
    }
  }

  private async enrichPlacesWithDetails(places: GooglePlacesItem[]): Promise<GooglePlacesItem[]> {
    const enrichedPlaces: GooglePlacesItem[] = [];
    
    for (const place of places) {
      try {
        // Fetch detailed information for this place
        const detailsUrl = `${this.baseUrl}/details/json?place_id=${place.place_id}&fields=formatted_phone_number,international_phone_number,website,formatted_address,opening_hours&key=${this.apiKey}`;
        
        const detailsResponse = await fetch(detailsUrl);
        const detailsData = await detailsResponse.json() as any;
        
        if (detailsData.status === 'OK' && detailsData.result) {
          // Merge the detailed information with the basic place data
          const enrichedPlace: GooglePlacesItem = {
            ...place,
            formatted_phone_number: detailsData.result.formatted_phone_number,
            international_phone_number: detailsData.result.international_phone_number,
            website: detailsData.result.website,
            formatted_address: detailsData.result.formatted_address || place.formatted_address,
            opening_hours: detailsData.result.opening_hours || place.opening_hours
          };
          enrichedPlaces.push(enrichedPlace);
        } else {
          // If details fetch fails, use the original place data
          enrichedPlaces.push(place);
        }
        
        // Add a small delay to respect rate limits
        await new Promise(resolve => setTimeout(resolve, 100));
        
      } catch (error) {
        console.error(`Error fetching details for place ${place.place_id}:`, error);
        // If details fetch fails, use the original place data
        enrichedPlaces.push(place);
      }
    }
    
    return enrichedPlaces;
  }

  normalize(item: GooglePlacesItem): Partial<CanonicalLead> & { provenance: ProvenanceEntry[] } {
    const provenance: ProvenanceEntry[] = [];

    // Extract basic information
    const normalized: Partial<CanonicalLead> = {
      website: item.website || null,
      phones: item.formatted_phone_number || item.international_phone_number ? [{
        value: item.international_phone_number || item.formatted_phone_number!,
        type: 'main' as any,
        provider: this.name
      }] : [],
      geo: item.geometry ? {
        lat: item.geometry.location.lat,
        lon: item.geometry.location.lng
      } : null,
      reviews: {
        count: item.user_ratings_total || 0,
        rating: item.rating || null,
        last_reviewed_at: null
      },
      tags: item.types || [],
      tech_signals: [],
      emails: [],
      domains: item.website ? [this.extractDomain(item.website)] : [],
      social: {
        linkedin: null,
        twitter: null,
        facebook: null,
        instagram: null
      },
      firmographics: {
        employees_range: null,
        revenue_range: null,
        founded_year: null
      },
      hours: this.parseBusinessHours(item.opening_hours),
      legal_entity: {
        name: item.name,
        alt_names: [],
        registration_id: null,
        jurisdictions: []
      },
      brand: {
        name: item.name
      }
    };

    // Add provenance entries
    if (item.name) {
      provenance.push({
        field: 'legal_entity.name',
        provider: this.name,
        confidence: 1.0,
        note: 'Direct from Google Places API'
      });
    }

    if (item.formatted_phone_number) {
      provenance.push({
        field: 'phones',
        provider: this.name,
        confidence: 0.95,
        note: 'Phone number from Google Places'
      });
    }

    if (item.website) {
      provenance.push({
        field: 'website',
        provider: this.name,
        confidence: 0.9,
        note: 'Website from Google Places'
      });
    }

    if (item.rating) {
      provenance.push({
        field: 'reviews.rating',
        provider: this.name,
        confidence: 0.85,
        note: 'Rating from Google Places'
      });
    }

    return {
      ...normalized,
      provenance
    };
  }

  rateLimit(): RateLimitPolicy {
    return {
      requests_per_second: 10,
      burst_limit: 100,
      daily_quota: 100000
    };
  }

  private estimateCost(queryCount: number): number {
    // Google Places API pricing: $0.017 per request for nearby search
    return queryCount * 0.017;
  }

  private estimateLeads(input: CampaignSpec): number {
    // Rough estimation based on area and filters
    const baseArea = Math.PI * Math.pow(input.geo.radius_km, 2);
    const densityFactor = input.filters.min_rating ? 0.3 : 0.5; // Higher rating = fewer results
    return Math.floor(baseArea * densityFactor);
  }

  private extractDomain(website: string): string {
    try {
      const url = new URL(website);
      return url.hostname.replace('www.', '');
    } catch {
      return website.replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
    }
  }

  private parseBusinessHours(openingHours?: any): any[] {
    if (!openingHours?.weekday_text) {
      return [];
    }

    const hours: any[] = [];
    const dayMap: Record<string, number> = {
      'Sunday': 0,
      'Monday': 1,
      'Tuesday': 2,
      'Wednesday': 3,
      'Thursday': 4,
      'Friday': 5,
      'Saturday': 6
    };

    openingHours.weekday_text.forEach((dayText: string) => {
      const [dayName, timeRange] = dayText.split(': ');
      const dayOfWeek = dayMap[dayName];
      
      if (dayOfWeek !== undefined && timeRange !== 'Closed') {
        const [open, close] = timeRange.split(' – ');
        hours.push({
          dow: dayOfWeek,
          open: open || '00:00',
          close: close || '23:59'
        });
      }
    });

    return hours;
  }

  private getCityCoordinates(city: string, region: string): {lat: number, lng: number} | null {
    // Hardcoded coordinates for common cities (until Geocoding API is enabled)
    const cityMap: Record<string, Record<string, {lat: number, lng: number}>> = {
      'Tulsa': {
        'OK': { lat: 36.1540, lng: -95.9928 }
      },
      'Denver': {
        'CO': { lat: 39.7392, lng: -104.9903 }
      },
      'Austin': {
        'TX': { lat: 30.2672, lng: -97.7431 }
      },
      'New York': {
        'NY': { lat: 40.7128, lng: -74.0060 }
      },
      'Los Angeles': {
        'CA': { lat: 34.0522, lng: -118.2437 }
      }
    };
    
    return cityMap[city]?.[region] || null;
  }

  private async geocodeCity(city: string, region: string, country: string): Promise<{lat: number, lng: number} | null> {
    try {
      const address = `${city}, ${region}, ${country}`;
      const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(address)}&key=${this.apiKey}`;
      
      const response = await fetch(geocodeUrl);
      const data = await response.json() as any;
      
      if (data.status === 'OK' && data.results && data.results.length > 0) {
        const location = data.results[0].geometry.location;
        return {
          lat: location.lat,
          lng: location.lng
        };
      }
      
      return null;
    } catch (error) {
      console.error('Error geocoding city:', error);
      return null;
    }
  }
}
