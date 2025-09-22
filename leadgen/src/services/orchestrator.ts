import { CampaignSpec, CampaignRun, RunStatus, QueryPlan, ProviderQuery } from '../types/campaign';
import { CanonicalLead, Vertical } from '../types/canonical';
import { SourceAdapter } from '../adapters/google-places';
import { NormalizationEngine } from './normalization';
import { CSVExporter } from './csv-exporter';
import * as fs from 'fs';
import * as path from 'path';

export class CampaignOrchestrator {
  private adapters: Map<string, SourceAdapter> = new Map();
  private normalizationEngine: NormalizationEngine;
  private csvExporter: CSVExporter;
  private activeRuns: Map<string, CampaignRun> = new Map();
  private runHistory: CampaignRun[] = [];

  constructor() {
    this.normalizationEngine = new NormalizationEngine();
    this.csvExporter = new CSVExporter();
  }

  registerAdapter(adapter: SourceAdapter): void {
    this.adapters.set(adapter.name, adapter);
    this.normalizationEngine.registerAdapter(adapter);
  }

  async runCampaign(campaignSpec: CampaignSpec): Promise<CampaignRun> {
    const runId = this.generateRunId();
    const run: CampaignRun = {
      id: runId,
      campaign_id: campaignSpec.name,
      status: RunStatus.PENDING,
      started_at: new Date(),
      total_leads: 0,
      successful_leads: 0,
      failed_leads: 0,
      cost_usd: 0
    };

    this.activeRuns.set(runId, run);

    try {
      run.status = RunStatus.RUNNING;
      
      // Step 1: Plan queries
      console.log(`Planning queries for campaign: ${campaignSpec.name}`);
      const queryPlan = await this.planQueries(campaignSpec);
      
      // Step 2: Execute queries and collect raw data
      console.log(`Executing ${queryPlan.sources.length} queries...`);
      const rawItems = await this.executeQueries(queryPlan.sources);
      run.total_leads = rawItems.length;
      
      // Step 3: Normalize leads
      console.log(`Normalizing ${rawItems.length} raw items...`);
      const normalizedLeads = await this.normalizationEngine.normalizeLeads(
        rawItems,
        campaignSpec.vertical
      );
      
      // Step 4: Score leads
      console.log(`Scoring ${normalizedLeads.length} leads...`);
      const scoredLeads = this.normalizationEngine.scoreLeads(normalizedLeads);
      
      // Step 5: Export to CSV
      console.log(`Exporting ${scoredLeads.length} leads to CSV...`);
      const now = new Date();
      const dateTimeStr = now.toISOString()
        .replace(/:/g, '-')
        .replace(/\./g, '-')
        .slice(0, 19); // Format: 2025-09-15T17-55-32
      const filename = `${campaignSpec.name.replace(/\s/g, '_')}_${dateTimeStr}.csv`;
      const exportPath = await this.csvExporter.exportLeads(scoredLeads, {
        filename: filename
      });
      
      // Store export path in run
      run.export_path = exportPath;
      
      // Update run status
      run.successful_leads = scoredLeads.length;
      run.cost_usd = queryPlan.estimated_cost;
      run.status = RunStatus.COMPLETED;
      run.completed_at = new Date();
      
      console.log(`Campaign completed successfully. Exported to: ${exportPath}`);
      
    } catch (error) {
      console.error(`Campaign failed:`, error);
      run.status = RunStatus.FAILED;
      run.error_message = error instanceof Error ? error.message : 'Unknown error';
      run.completed_at = new Date();
    }

    // Move completed runs to history
    if (run.status === RunStatus.COMPLETED || run.status === RunStatus.FAILED) {
      this.runHistory.push(run);
      this.activeRuns.delete(runId);
    }

    return run;
  }

  private async planQueries(campaignSpec: CampaignSpec): Promise<QueryPlan> {
    const allQueries: ProviderQuery[] = [];
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

  private async executeQueries(queries: ProviderQuery[]): Promise<Array<{ provider: string; item: any; providerKey: string }>> {
    const allItems: Array<{ provider: string; item: any; providerKey: string }> = [];

    for (const query of queries) {
      const adapter = this.adapters.get(query.provider);
      if (!adapter) {
        console.warn(`No adapter found for provider: ${query.provider}`);
        continue;
      }

      try {
        let cursor: string | undefined;
        let hasMore = true;
        let pageCount = 0;
        const maxPages = 10; // Safety limit for Phase 0

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

          // Rate limiting - simple delay
          if (hasMore) {
            await this.delay(1000); // 1 second delay between pages
          }
        }

        console.log(`Fetched ${allItems.length} items from ${query.provider}`);
      } catch (error) {
        console.error(`Error fetching from ${query.provider}:`, error);
        // Continue with other providers
      }
    }

    return allItems;
  }

  private extractProviderKey(item: any, provider: string): string {
    switch (provider) {
      case 'google_places':
        return item.place_id || 'unknown';
      default:
        return item.id || item.name || 'unknown';
    }
  }

  private generateRunId(): string {
    return `run_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
  }

  private delay(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  // Utility methods
  getRunStatus(runId: string): CampaignRun | undefined {
    return this.activeRuns.get(runId);
  }

  getAllRuns(): CampaignRun[] {
    return [...Array.from(this.activeRuns.values()), ...this.runHistory]
      .sort((a, b) => b.started_at.getTime() - a.started_at.getTime());
  }

  getExportFilesForRun(runId: string): string[] {
    const exportsDir = path.join(process.cwd(), 'exports');
    if (!fs.existsSync(exportsDir)) {
      return [];
    }

    const files = fs.readdirSync(exportsDir);
    return files.filter(file => file.includes(runId));
  }

  getRunHistory(): CampaignRun[] {
    return this.runHistory.sort((a, b) => b.started_at.getTime() - a.started_at.getTime());
  }

  getExportStats(leads: CanonicalLead[]) {
    return this.csvExporter.getExportStats(leads);
  }
}
