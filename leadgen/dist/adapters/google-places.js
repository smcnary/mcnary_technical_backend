"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.GooglePlacesAdapter = void 0;
const node_fetch_1 = __importDefault(require("node-fetch"));
const canonical_1 = require("../types/canonical");
class GooglePlacesAdapter {
    constructor(apiKey) {
        this.name = 'google_places';
        this.supportsVerticals = [canonical_1.Vertical.LOCAL_SERVICES, canonical_1.Vertical.HEALTHCARE, canonical_1.Vertical.REAL_ESTATE];
        this.baseUrl = 'https://maps.googleapis.com/maps/api/place';
        this.apiKey = apiKey;
    }
    async planQueries(input) {
        const queries = [];
        const queryParams = {
            key: this.apiKey,
            fields: 'place_id,name,formatted_address,geometry,types,rating,user_ratings_total,formatted_phone_number,website,opening_hours,business_status'
        };
        if (input.geo.lat && input.geo.lon) {
            queryParams.location = `${input.geo.lat},${input.geo.lon}`;
            queryParams.radius = input.geo.radius_km * 1000;
        }
        else if (input.geo.city && input.geo.region) {
            const coordinates = this.getCityCoordinates(input.geo.city, input.geo.region);
            if (coordinates) {
                queryParams.location = `${coordinates.lat},${coordinates.lng}`;
                queryParams.radius = input.geo.radius_km * 1000;
            }
            else {
                throw new Error(`Could not find coordinates for city: ${input.geo.city}, ${input.geo.region}`);
            }
        }
        if (input.filters.keywords && input.filters.keywords.length > 0) {
            queryParams.keyword = input.filters.keywords.join(' ');
        }
        if (input.filters.types && input.filters.types.length > 0) {
            queryParams.type = input.filters.types[0];
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
        const estimated_cost = this.estimateCost(queries.length);
        const estimated_leads = this.estimateLeads(input);
        return {
            sources: queries,
            estimated_cost,
            estimated_leads
        };
    }
    async fetchPage(query, cursor) {
        const params = new URLSearchParams({
            ...query.query_params,
            pagetoken: cursor || ''
        });
        const url = `${this.baseUrl}/nearbysearch/json?${params}`;
        try {
            const response = await (0, node_fetch_1.default)(url);
            const data = await response.json();
            if (data.status !== 'OK' && data.status !== 'ZERO_RESULTS') {
                throw new Error(`Google Places API error: ${data.status}`);
            }
            const enrichedItems = await this.enrichPlacesWithDetails(data.results || []);
            return {
                items: enrichedItems,
                next_cursor: data.next_page_token,
                has_more: !!data.next_page_token,
                total_estimated: data.results?.length || 0
            };
        }
        catch (error) {
            console.error('Error fetching Google Places data:', error);
            throw error;
        }
    }
    async enrichPlacesWithDetails(places) {
        const enrichedPlaces = [];
        for (const place of places) {
            try {
                const detailsUrl = `${this.baseUrl}/details/json?place_id=${place.place_id}&fields=formatted_phone_number,international_phone_number,website,formatted_address,opening_hours&key=${this.apiKey}`;
                const detailsResponse = await (0, node_fetch_1.default)(detailsUrl);
                const detailsData = await detailsResponse.json();
                if (detailsData.status === 'OK' && detailsData.result) {
                    const enrichedPlace = {
                        ...place,
                        formatted_phone_number: detailsData.result.formatted_phone_number,
                        international_phone_number: detailsData.result.international_phone_number,
                        website: detailsData.result.website,
                        formatted_address: detailsData.result.formatted_address || place.formatted_address,
                        opening_hours: detailsData.result.opening_hours || place.opening_hours
                    };
                    enrichedPlaces.push(enrichedPlace);
                }
                else {
                    enrichedPlaces.push(place);
                }
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            catch (error) {
                console.error(`Error fetching details for place ${place.place_id}:`, error);
                enrichedPlaces.push(place);
            }
        }
        return enrichedPlaces;
    }
    normalize(item) {
        const provenance = [];
        const normalized = {
            website: item.website || null,
            phones: item.formatted_phone_number || item.international_phone_number ? [{
                    value: item.international_phone_number || item.formatted_phone_number,
                    type: 'main',
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
    rateLimit() {
        return {
            requests_per_second: 10,
            burst_limit: 100,
            daily_quota: 100000
        };
    }
    estimateCost(queryCount) {
        return queryCount * 0.017;
    }
    estimateLeads(input) {
        const baseArea = Math.PI * Math.pow(input.geo.radius_km, 2);
        const densityFactor = input.filters.min_rating ? 0.3 : 0.5;
        return Math.floor(baseArea * densityFactor);
    }
    extractDomain(website) {
        try {
            const url = new URL(website);
            return url.hostname.replace('www.', '');
        }
        catch {
            return website.replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
        }
    }
    parseBusinessHours(openingHours) {
        if (!openingHours?.weekday_text) {
            return [];
        }
        const hours = [];
        const dayMap = {
            'Sunday': 0,
            'Monday': 1,
            'Tuesday': 2,
            'Wednesday': 3,
            'Thursday': 4,
            'Friday': 5,
            'Saturday': 6
        };
        openingHours.weekday_text.forEach((dayText) => {
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
    getCityCoordinates(city, region) {
        const cityMap = {
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
    async geocodeCity(city, region, country) {
        try {
            const address = `${city}, ${region}, ${country}`;
            const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(address)}&key=${this.apiKey}`;
            const response = await (0, node_fetch_1.default)(geocodeUrl);
            const data = await response.json();
            if (data.status === 'OK' && data.results && data.results.length > 0) {
                const location = data.results[0].geometry.location;
                return {
                    lat: location.lat,
                    lng: location.lng
                };
            }
            return null;
        }
        catch (error) {
            console.error('Error geocoding city:', error);
            return null;
        }
    }
}
exports.GooglePlacesAdapter = GooglePlacesAdapter;
//# sourceMappingURL=google-places.js.map