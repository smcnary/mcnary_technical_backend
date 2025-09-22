import { v4 as uuidv4 } from 'uuid';
import { CanonicalLead, SourceRecord, Vertical } from '../types/canonical';
import { SourceAdapter } from '../adapters/google-places';

export class NormalizationEngine {
  private adapters: Map<string, SourceAdapter> = new Map();

  registerAdapter(adapter: SourceAdapter): void {
    this.adapters.set(adapter.name, adapter);
  }

  async normalizeLeads(
    rawItems: Array<{ provider: string; item: any; providerKey: string }>,
    vertical: Vertical
  ): Promise<CanonicalLead[]> {
    const normalizedLeads: CanonicalLead[] = [];

    for (const { provider, item, providerKey } of rawItems) {
      const adapter = this.adapters.get(provider);
      if (!adapter) {
        console.warn(`No adapter found for provider: ${provider}`);
        continue;
      }

      try {
        const normalized = adapter.normalize(item);
        const leadId = uuidv4();
        
        // Create source record
        const sourceRecord: SourceRecord = {
          provider,
          provider_key: providerKey,
          raw: item,
          fetched_at: new Date(),
          hash: this.generateHash(item)
        };

        // Build complete canonical lead
        const canonicalLead: CanonicalLead = {
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
      } catch (error) {
        console.error(`Error normalizing item from ${provider}:`, error);
        // Continue processing other items
      }
    }

    return normalizedLeads;
  }

  private generateHash(obj: any): string {
    // Simple hash function for change detection
    const str = JSON.stringify(obj, Object.keys(obj).sort());
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
      const char = str.charCodeAt(i);
      hash = ((hash << 5) - hash) + char;
      hash = hash & hash; // Convert to 32-bit integer
    }
    return hash.toString(16);
  }

  // Basic scoring implementation for Phase 0
  scoreLeads(leads: CanonicalLead[]): CanonicalLead[] {
    return leads.map(lead => {
      let score = 0;
      const explanations: string[] = [];

      // Basic scoring rules
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

      // Cap at 100
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
