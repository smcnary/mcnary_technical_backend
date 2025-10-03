"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
exports.CampaignOrchestrator = void 0;
const campaign_1 = require("../types/campaign");
const normalization_1 = require("./normalization");
const csv_exporter_1 = require("./csv-exporter");
const fs = __importStar(require("fs"));
const path = __importStar(require("path"));
class CampaignOrchestrator {
    constructor() {
        this.adapters = new Map();
        this.activeRuns = new Map();
        this.runHistory = [];
        this.normalizationEngine = new normalization_1.NormalizationEngine();
        this.csvExporter = new csv_exporter_1.CSVExporter();
    }
    registerAdapter(adapter) {
        this.adapters.set(adapter.name, adapter);
        this.normalizationEngine.registerAdapter(adapter);
    }
    async runCampaign(campaignSpec) {
        const runId = this.generateRunId();
        const run = {
            id: runId,
            campaign_id: campaignSpec.name,
            status: campaign_1.RunStatus.PENDING,
            started_at: new Date(),
            total_leads: 0,
            successful_leads: 0,
            failed_leads: 0,
            cost_usd: 0
        };
        this.activeRuns.set(runId, run);
        try {
            run.status = campaign_1.RunStatus.RUNNING;
            console.log(`Planning queries for campaign: ${campaignSpec.name}`);
            const queryPlan = await this.planQueries(campaignSpec);
            console.log(`Executing ${queryPlan.sources.length} queries...`);
            const rawItems = await this.executeQueries(queryPlan.sources);
            run.total_leads = rawItems.length;
            console.log(`Normalizing ${rawItems.length} raw items...`);
            const normalizedLeads = await this.normalizationEngine.normalizeLeads(rawItems, campaignSpec.vertical);
            console.log(`Scoring ${normalizedLeads.length} leads...`);
            const scoredLeads = this.normalizationEngine.scoreLeads(normalizedLeads);
            console.log(`Exporting ${scoredLeads.length} leads to CSV...`);
            const now = new Date();
            const dateTimeStr = now.toISOString()
                .replace(/:/g, '-')
                .replace(/\./g, '-')
                .slice(0, 19);
            const filename = `${campaignSpec.name.replace(/\s/g, '_')}_${dateTimeStr}.csv`;
            const exportPath = await this.csvExporter.exportLeads(scoredLeads, {
                filename: filename
            });
            run.export_path = exportPath;
            run.successful_leads = scoredLeads.length;
            run.cost_usd = queryPlan.estimated_cost;
            run.status = campaign_1.RunStatus.COMPLETED;
            run.completed_at = new Date();
            console.log(`Campaign completed successfully. Exported to: ${exportPath}`);
        }
        catch (error) {
            console.error(`Campaign failed:`, error);
            run.status = campaign_1.RunStatus.FAILED;
            run.error_message = error instanceof Error ? error.message : 'Unknown error';
            run.completed_at = new Date();
        }
        if (run.status === campaign_1.RunStatus.COMPLETED || run.status === campaign_1.RunStatus.FAILED) {
            this.runHistory.push(run);
            this.activeRuns.delete(runId);
        }
        return run;
    }
    async planQueries(campaignSpec) {
        const allQueries = [];
        let totalEstimatedCost = 0;
        let totalEstimatedLeads = 0;
        for (const sourceName of campaignSpec.sources) {
            const adapter = this.adapters.get(sourceName);
            if (!adapter) {
                throw new Error(`No adapter found for source: ${sourceName}`);
            }
            const plan = await adapter.planQueries(campaignSpec);
            allQueries.push(...plan.sources);
            totalEstimatedCost += plan.estimated_cost;
            totalEstimatedLeads += plan.estimated_leads;
        }
        return {
            sources: allQueries,
            estimated_cost: totalEstimatedCost,
            estimated_leads: totalEstimatedLeads
        };
    }
    async executeQueries(queries) {
        const allItems = [];
        for (const query of queries) {
            const adapter = this.adapters.get(query.provider);
            if (!adapter) {
                console.warn(`No adapter found for provider: ${query.provider}`);
                continue;
            }
            try {
                let cursor;
                let hasMore = true;
                let pageCount = 0;
                const maxPages = 10;
                while (hasMore && pageCount < maxPages) {
                    const page = await adapter.fetchPage(query, cursor);
                    for (const item of page.items) {
                        allItems.push({
                            provider: query.provider,
                            item,
                            providerKey: this.extractProviderKey(item, query.provider)
                        });
                    }
                    cursor = page.next_cursor;
                    hasMore = page.has_more;
                    pageCount++;
                    if (hasMore) {
                        await this.delay(1000);
                    }
                }
                console.log(`Fetched ${allItems.length} items from ${query.provider}`);
            }
            catch (error) {
                console.error(`Error fetching from ${query.provider}:`, error);
            }
        }
        return allItems;
    }
    extractProviderKey(item, provider) {
        switch (provider) {
            case 'google_places':
                return item.place_id || 'unknown';
            default:
                return item.id || item.name || 'unknown';
        }
    }
    generateRunId() {
        return `run_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    }
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    getRunStatus(runId) {
        return this.activeRuns.get(runId);
    }
    getAllRuns() {
        return [...Array.from(this.activeRuns.values()), ...this.runHistory]
            .sort((a, b) => b.started_at.getTime() - a.started_at.getTime());
    }
    getExportFilesForRun(runId) {
        const exportsDir = path.join(process.cwd(), 'exports');
        if (!fs.existsSync(exportsDir)) {
            return [];
        }
        const files = fs.readdirSync(exportsDir);
        return files.filter(file => file.includes(runId));
    }
    getRunHistory() {
        return this.runHistory.sort((a, b) => b.started_at.getTime() - a.started_at.getTime());
    }
    getExportStats(leads) {
        return this.csvExporter.getExportStats(leads);
    }
}
exports.CampaignOrchestrator = CampaignOrchestrator;
//# sourceMappingURL=orchestrator.js.map