import { CanonicalLead } from '../types/canonical';
export interface CSVExportOptions {
    filename?: string;
    outputDir?: string;
    includeProvenance?: boolean;
    includeSourceRecords?: boolean;
}
export declare class CSVExporter {
    private outputDir;
    constructor(outputDir?: string);
    exportLeads(leads: CanonicalLead[], options?: CSVExportOptions): Promise<string>;
    private generateCSV;
    private escapeCSVValue;
    private ensureOutputDir;
    getExportStats(leads: CanonicalLead[]): {
        totalLeads: number;
        leadsWithWebsite: number;
        leadsWithPhone: number;
        leadsWithEmail: number;
        averageScore: number;
        verticalBreakdown: Record<string, number>;
    };
}
//# sourceMappingURL=csv-exporter.d.ts.map