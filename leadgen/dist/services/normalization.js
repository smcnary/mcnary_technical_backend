"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.NormalizationEngine = void 0;
const uuid_1 = require("uuid");
class NormalizationEngine {
    constructor() {
        this.adapters = new Map();
    }
    registerAdapter(adapter) {
        this.adapters.set(adapter.name, adapter);
    }
    async normalizeLeads(rawItems, vertical) {
        const normalizedLeads = [];
        for (const { provider, item, providerKey } of rawItems) {
            const adapter = this.adapters.get(provider);
            if (!adapter) {
                console.warn(`No adapter found for provider: ${provider}`);
                continue;
            }
            try {
                const normalized = adapter.normalize(item);
                const leadId = (0, uuid_1.v4)();
                const sourceRecord = {
                    provider,
                    provider_key: providerKey,
                    raw: item,
                    fetched_at: new Date(),
                    hash: this.generateHash(item)
                };
                const canonicalLead = {
                    lead_id: leadId,
                    source_records: [sourceRecord],
                    legal_entity: normalized.legal_entity || {
                        name: '',
                        alt_names: [],
                        registration_id: null,
                        jurisdictions: []
                    },
                    brand: normalized.brand || { name: null },
                    vertical,
                    website: normalized.website || null,
                    domains: normalized.domains || [],
                    emails: normalized.emails || [],
                    phones: normalized.phones || [],
                    address: normalized.address || null,
                    geo: normalized.geo || null,
                    social: normalized.social || {
                        linkedin: null,
                        twitter: null,
                        facebook: null,
                        instagram: null
                    },
                    firmographics: normalized.firmographics || {
                        employees_range: null,
                        revenue_range: null,
                        founded_year: null
                    },
                    tech_signals: normalized.tech_signals || [],
                    reviews: normalized.reviews || {
                        count: 0,
                        rating: null,
                        last_reviewed_at: null
                    },
                    hours: normalized.hours || [],
                    tags: normalized.tags || [],
                    lead_score: normalized.lead_score || null,
                    score_explanations: normalized.score_explanations || [],
                    provenance: normalized.provenance || [],
                    created_at: new Date(),
                    updated_at: new Date()
                };
                normalizedLeads.push(canonicalLead);
            }
            catch (error) {
                console.error(`Error normalizing item from ${provider}:`, error);
            }
        }
        return normalizedLeads;
    }
    generateHash(obj) {
        const str = JSON.stringify(obj, Object.keys(obj).sort());
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash.toString(16);
    }
    scoreLeads(leads) {
        return leads.map(lead => {
            let score = 0;
            const explanations = [];
            if (lead.reviews.rating && lead.reviews.rating >= 4.0) {
                score += 20;
                explanations.push(`High rating: ${lead.reviews.rating}`);
            }
            if (lead.reviews.count >= 10) {
                score += 15;
                explanations.push(`Good review count: ${lead.reviews.count}`);
            }
            if (lead.website) {
                score += 10;
                explanations.push('Has website');
            }
            if (lead.phones.length > 0) {
                score += 10;
                explanations.push('Has phone number');
            }
            if (lead.emails.length > 0) {
                score += 15;
                explanations.push('Has email');
            }
            if (lead.geo) {
                score += 5;
                explanations.push('Has location data');
            }
            score = Math.min(score, 100);
            return {
                ...lead,
                lead_score: score,
                score_explanations: explanations,
                updated_at: new Date()
            };
        });
    }
}
exports.NormalizationEngine = NormalizationEngine;
//# sourceMappingURL=normalization.js.map