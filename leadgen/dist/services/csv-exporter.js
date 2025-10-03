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
exports.CSVExporter = void 0;
const fs = __importStar(require("fs"));
const path = __importStar(require("path"));
class CSVExporter {
    constructor(outputDir = './exports') {
        this.outputDir = outputDir;
        this.ensureOutputDir();
    }
    async exportLeads(leads, options = {}) {
        const { filename = `leads_${new Date().toISOString().split('T')[0]}.csv`, includeProvenance = false, includeSourceRecords = false } = options;
        const csvContent = this.generateCSV(leads, includeProvenance, includeSourceRecords);
        const filePath = path.join(this.outputDir, filename);
        await fs.promises.writeFile(filePath, csvContent, 'utf8');
        console.log(`Exported ${leads.length} leads to ${filePath}`);
        return filePath;
    }
    generateCSV(leads, includeProvenance, includeSourceRecords) {
        if (leads.length === 0) {
            return '';
        }
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
        const escapedRows = rows.map(row => row.map(cell => this.escapeCSVValue(cell)));
        const csvLines = [
            headers.join(','),
            ...escapedRows.map(row => row.join(','))
        ];
        return csvLines.join('\n');
    }
    escapeCSVValue(value) {
        if (value === null || value === undefined) {
            return '';
        }
        const stringValue = String(value);
        if (stringValue.includes(',') || stringValue.includes('\n') || stringValue.includes('"')) {
            return `"${stringValue.replace(/"/g, '""')}"`;
        }
        return stringValue;
    }
    ensureOutputDir() {
        if (!fs.existsSync(this.outputDir)) {
            fs.mkdirSync(this.outputDir, { recursive: true });
        }
    }
    getExportStats(leads) {
        const stats = {
            totalLeads: leads.length,
            leadsWithWebsite: leads.filter(lead => lead.website).length,
            leadsWithPhone: leads.filter(lead => lead.phones.length > 0).length,
            leadsWithEmail: leads.filter(lead => lead.emails.length > 0).length,
            averageScore: 0,
            verticalBreakdown: {}
        };
        if (leads.length > 0) {
            const scores = leads.map(lead => lead.lead_score || 0);
            stats.averageScore = scores.reduce((sum, score) => sum + score, 0) / scores.length;
            leads.forEach(lead => {
                stats.verticalBreakdown[lead.vertical] = (stats.verticalBreakdown[lead.vertical] || 0) + 1;
            });
        }
        return stats;
    }
}
exports.CSVExporter = CSVExporter;
//# sourceMappingURL=csv-exporter.js.map