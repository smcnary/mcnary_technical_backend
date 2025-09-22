import * as fs from 'fs';
import * as path from 'path';
import { CanonicalLead } from '../types/canonical';

export interface CSVExportOptions {
  filename?: string;
  outputDir?: string;
  includeProvenance?: boolean;
  includeSourceRecords?: boolean;
}

export class CSVExporter {
  private outputDir: string;

  constructor(outputDir: string = './exports') {
    this.outputDir = outputDir;
    this.ensureOutputDir();
  }

  async exportLeads(leads: CanonicalLead[], options: CSVExportOptions = {}): Promise<string> {
    const {
      filename = `leads_${new Date().toISOString().split('T')[0]}.csv`,
      includeProvenance = false,
      includeSourceRecords = false
    } = options;

    const csvContent = this.generateCSV(leads, includeProvenance, includeSourceRecords);
    const filePath = path.join(this.outputDir, filename);

    await fs.promises.writeFile(filePath, csvContent, 'utf8');
    
    console.log(`Exported ${leads.length} leads to ${filePath}`);
    return filePath;
  }

  private generateCSV(leads: CanonicalLead[], includeProvenance: boolean, includeSourceRecords: boolean): string {
    if (leads.length === 0) {
      return '';
    }

    // Define CSV headers
    const headers = [
      'lead_id',
      'company_name',
      'vertical',
      'website',
      'primary_phone',
      'primary_email',
      'address_line1',
      'address_line2',
      'city',
      'region',
      'postal_code',
      'country',
      'latitude',
      'longitude',
      'rating',
      'review_count',
      'employee_range',
      'revenue_range',
      'founded_year',
      'tech_signals',
      'tags',
      'lead_score',
      'score_explanations',
      'created_at',
      'updated_at'
    ];

    if (includeProvenance) {
      headers.push('provenance');
    }

    if (includeSourceRecords) {
      headers.push('source_providers');
    }

    // Generate CSV rows
    const rows = leads.map(lead => {
      const row = [
        lead.lead_id,
        lead.legal_entity.name,
        lead.vertical,
        lead.website || '',
        lead.phones.length > 0 ? lead.phones[0].value : '',
        lead.emails.length > 0 ? lead.emails[0].value : '',
        lead.address?.line1 || '',
        lead.address?.line2 || '',
        lead.address?.city || '',
        lead.address?.region || '',
        lead.address?.postal || '',
        lead.address?.country || '',
        lead.geo?.lat || '',
        lead.geo?.lon || '',
        lead.reviews.rating || '',
        lead.reviews.count || '',
        lead.firmographics.employees_range || '',
        lead.firmographics.revenue_range || '',
        lead.firmographics.founded_year || '',
        lead.tech_signals.join(';'),
        lead.tags.join(';'),
        lead.lead_score || '',
        lead.score_explanations.join(';'),
        lead.created_at.toISOString(),
        lead.updated_at.toISOString()
      ];

      if (includeProvenance) {
        row.push(JSON.stringify(lead.provenance));
      }

      if (includeSourceRecords) {
        const providers = lead.source_records.map(record => record.provider);
        row.push(providers.join(';'));
      }

      return row;
    });

    // Escape CSV values
    const escapedRows = rows.map(row => 
      row.map(cell => this.escapeCSVValue(cell))
    );

    // Combine headers and rows
    const csvLines = [
      headers.join(','),
      ...escapedRows.map(row => row.join(','))
    ];

    return csvLines.join('\n');
  }

  private escapeCSVValue(value: any): string {
    if (value === null || value === undefined) {
      return '';
    }

    const stringValue = String(value);
    
    // If the value contains comma, newline, or quote, wrap in quotes and escape quotes
    if (stringValue.includes(',') || stringValue.includes('\n') || stringValue.includes('"')) {
      return `"${stringValue.replace(/"/g, '""')}"`;
    }

    return stringValue;
  }

  private ensureOutputDir(): void {
    if (!fs.existsSync(this.outputDir)) {
      fs.mkdirSync(this.outputDir, { recursive: true });
    }
  }

  // Utility method to get export statistics
  getExportStats(leads: CanonicalLead[]): {
    totalLeads: number;
    leadsWithWebsite: number;
    leadsWithPhone: number;
    leadsWithEmail: number;
    averageScore: number;
    verticalBreakdown: Record<string, number>;
  } {
    const stats = {
      totalLeads: leads.length,
      leadsWithWebsite: leads.filter(lead => lead.website).length,
      leadsWithPhone: leads.filter(lead => lead.phones.length > 0).length,
      leadsWithEmail: leads.filter(lead => lead.emails.length > 0).length,
      averageScore: 0,
      verticalBreakdown: {} as Record<string, number>
    };

    if (leads.length > 0) {
      const scores = leads.map(lead => lead.lead_score || 0);
      stats.averageScore = scores.reduce((sum, score) => sum + score, 0) / scores.length;

      // Count by vertical
      leads.forEach(lead => {
        stats.verticalBreakdown[lead.vertical] = (stats.verticalBreakdown[lead.vertical] || 0) + 1;
      });
    }

    return stats;
  }
}
