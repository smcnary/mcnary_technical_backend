import { CanonicalLead, Vertical } from '../types/canonical';
import { SourceAdapter } from '../adapters/google-places';
export declare class NormalizationEngine {
    private adapters;
    registerAdapter(adapter: SourceAdapter): void;
    normalizeLeads(rawItems: Array<{
        provider: string;
        item: any;
        providerKey: string;
    }>, vertical: Vertical): Promise<CanonicalLead[]>;
    private generateHash;
    scoreLeads(leads: CanonicalLead[]): CanonicalLead[];
}
//# sourceMappingURL=normalization.d.ts.map