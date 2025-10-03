import { Vertical, CanonicalLead, ProvenanceEntry } from '../types/canonical';
import { CampaignSpec, QueryPlan, ProviderQuery, Page, RateLimitPolicy } from '../types/campaign';
export interface SourceAdapter {
    name: string;
    supportsVerticals: Vertical[];
    planQueries(input: CampaignSpec): Promise<QueryPlan>;
    fetchPage(query: ProviderQuery, cursor?: string): Promise<Page<any>>;
    normalize(item: any): Partial<CanonicalLead> & {
        provenance: ProvenanceEntry[];
    };
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
export declare class GooglePlacesAdapter implements SourceAdapter {
    name: string;
    supportsVerticals: Vertical[];
    private apiKey;
    private baseUrl;
    constructor(apiKey: string);
    planQueries(input: CampaignSpec): Promise<QueryPlan>;
    fetchPage(query: ProviderQuery, cursor?: string): Promise<Page<GooglePlacesItem>>;
    private enrichPlacesWithDetails;
    normalize(item: GooglePlacesItem): Partial<CanonicalLead> & {
        provenance: ProvenanceEntry[];
    };
    rateLimit(): RateLimitPolicy;
    private estimateCost;
    private estimateLeads;
    private extractDomain;
    private parseBusinessHours;
    private getCityCoordinates;
    private geocodeCity;
}
//# sourceMappingURL=google-places.d.ts.map