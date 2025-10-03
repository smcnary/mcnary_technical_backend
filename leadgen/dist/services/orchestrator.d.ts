import { CampaignSpec, CampaignRun } from '../types/campaign';
import { CanonicalLead } from '../types/canonical';
import { SourceAdapter } from '../adapters/google-places';
export declare class CampaignOrchestrator {
    private adapters;
    private normalizationEngine;
    private csvExporter;
    private activeRuns;
    private runHistory;
    constructor();
    registerAdapter(adapter: SourceAdapter): void;
    runCampaign(campaignSpec: CampaignSpec): Promise<CampaignRun>;
    private planQueries;
    private executeQueries;
    private extractProviderKey;
    private generateRunId;
    private delay;
    getRunStatus(runId: string): CampaignRun | undefined;
    getAllRuns(): CampaignRun[];
    getExportFilesForRun(runId: string): string[];
    getRunHistory(): CampaignRun[];
    getExportStats(leads: CanonicalLead[]): {
        totalLeads: number;
        leadsWithWebsite: number;
        leadsWithPhone: number;
        leadsWithEmail: number;
        averageScore: number;
        verticalBreakdown: Record<string, number>;
    };
}
//# sourceMappingURL=orchestrator.d.ts.map