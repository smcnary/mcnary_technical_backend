export declare enum Vertical {
    LOCAL_SERVICES = "local_services",
    B2B_SAAS = "b2b_saas",
    ECOMMERCE = "ecommerce",
    HEALTHCARE = "healthcare",
    REAL_ESTATE = "real_estate",
    OTHER = "other"
}
export declare enum EmailType {
    GENERIC = "generic",
    ROLE = "role",
    PERSONAL = "personal"
}
export declare enum PhoneType {
    MAIN = "main",
    MOBILE = "mobile",
    FAX = "fax"
}
export interface Email {
    value: string;
    type: EmailType;
    verified: boolean | null;
    provider: string | null;
}
export interface Phone {
    value: string;
    type: PhoneType;
    provider: string | null;
}
export interface Address {
    line1: string;
    line2: string | null;
    city: string;
    region: string;
    postal: string;
    country: string;
}
export interface GeoLocation {
    lat: number;
    lon: number;
}
export interface SocialProfiles {
    linkedin: string | null;
    twitter: string | null;
    facebook: string | null;
    instagram: string | null;
}
export interface Firmographics {
    employees_range: string | null;
    revenue_range: string | null;
    founded_year: number | null;
}
export interface Reviews {
    count: number;
    rating: number | null;
    last_reviewed_at: Date | null;
}
export interface BusinessHour {
    dow: number;
    open: string;
    close: string;
}
export interface ProvenanceEntry {
    field: string;
    provider: string;
    confidence: number;
    note?: string;
}
export interface SourceRecord {
    provider: string;
    provider_key: string;
    raw: any;
    fetched_at: Date;
    hash: string;
}
export interface LegalEntity {
    name: string;
    alt_names: string[];
    registration_id: string | null;
    jurisdictions: string[];
}
export interface Brand {
    name: string | null;
}
export interface CanonicalLead {
    lead_id: string;
    source_records: SourceRecord[];
    legal_entity: LegalEntity;
    brand: Brand;
    vertical: Vertical;
    website: string | null;
    domains: string[];
    emails: Email[];
    phones: Phone[];
    address: Address | null;
    geo: GeoLocation | null;
    social: SocialProfiles;
    firmographics: Firmographics;
    tech_signals: string[];
    reviews: Reviews;
    hours: BusinessHour[];
    tags: string[];
    lead_score: number | null;
    score_explanations: string[];
    provenance: ProvenanceEntry[];
    created_at: Date;
    updated_at: Date;
}
//# sourceMappingURL=canonical.d.ts.map