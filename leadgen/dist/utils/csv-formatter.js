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
exports.CSVFormatter = void 0;
const fs = __importStar(require("fs"));
const path = __importStar(require("path"));
class CSVFormatter {
    constructor(inputDir = 'exports', outputDir = 'formatted-exports') {
        this.inputDir = inputDir;
        this.outputDir = outputDir;
        if (!fs.existsSync(this.outputDir)) {
            fs.mkdirSync(this.outputDir, { recursive: true });
        }
    }
    formatCSVFile(inputFile) {
        const inputPath = path.join(this.inputDir, inputFile);
        const outputPath = path.join(this.outputDir, `formatted_${inputFile}`);
        try {
            const csvContent = fs.readFileSync(inputPath, 'utf-8');
            const lines = csvContent.trim().split('\n');
            if (lines.length === 0) {
                console.log(`Skipping empty file: ${inputFile}`);
                return;
            }
            const headers = lines[0].split(',');
            const dataRows = lines.slice(1);
            const formattedData = [];
            for (const row of dataRows) {
                if (row.trim() === '')
                    continue;
                const values = this.parseCSVRow(row);
                const formattedRow = this.formatRow(headers, values);
                formattedData.push(formattedRow);
            }
            this.writeFormattedOutput(formattedData, outputPath);
            console.log(`Formatted ${inputFile} -> ${path.basename(outputPath)}`);
        }
        catch (error) {
            console.error(`Error formatting ${inputFile}:`, error);
        }
    }
    formatAllCSVs() {
        const files = fs.readdirSync(this.inputDir);
        const csvFiles = files.filter(file => file.endsWith('.csv'));
        console.log(`Found ${csvFiles.length} CSV files to format`);
        for (const file of csvFiles) {
            this.formatCSVFile(file);
        }
    }
    parseCSVRow(row) {
        const result = [];
        let current = '';
        let inQuotes = false;
        for (let i = 0; i < row.length; i++) {
            const char = row[i];
            if (char === '"') {
                inQuotes = !inQuotes;
            }
            else if (char === ',' && !inQuotes) {
                result.push(current.trim());
                current = '';
            }
            else {
                current += char;
            }
        }
        result.push(current.trim());
        return result;
    }
    formatRow(headers, values) {
        const row = {};
        for (let i = 0; i < headers.length && i < values.length; i++) {
            row[headers[i]] = values[i];
        }
        const addressParts = [
            row.address_line1,
            row.address_line2,
            row.city,
            row.region,
            row.postal_code,
            row.country
        ].filter(part => part && part.trim() !== '');
        const address = addressParts.length > 0 ? addressParts.join(', ') : 'N/A';
        return {
            company_name: row.company_name || 'N/A',
            website: row.website || 'N/A',
            phone: row.primary_phone || 'N/A',
            email: row.primary_email || 'N/A',
            address: address,
            rating: row.rating || 'N/A',
            review_count: row.review_count || 'N/A',
            lead_score: row.lead_score || 'N/A',
            score_explanations: row.score_explanations || 'N/A',
            tags: row.tags || 'N/A'
        };
    }
    writeFormattedOutput(data, outputPath) {
        let output = '';
        output += '='.repeat(80) + '\n';
        output += 'LEAD GENERATION RESULTS\n';
        output += '='.repeat(80) + '\n\n';
        output += `Total Leads: ${data.length}\n`;
        output += `Generated: ${new Date().toLocaleString()}\n\n`;
        data.forEach((lead, index) => {
            output += `${index + 1}. ${lead.company_name}\n`;
            output += '-'.repeat(60) + '\n';
            output += `Website: ${lead.website}\n`;
            output += `Phone: ${lead.phone}\n`;
            output += `Email: ${lead.email}\n`;
            output += `Address: ${lead.address}\n`;
            output += `Rating: ${lead.rating} (${lead.review_count} reviews)\n`;
            output += `Lead Score: ${lead.lead_score}\n`;
            output += `Score Details: ${lead.score_explanations}\n`;
            output += `Tags: ${lead.tags}\n`;
            output += '\n';
        });
        fs.writeFileSync(outputPath, output, 'utf-8');
        const htmlPath = outputPath.replace('.csv', '.html');
        this.writeHTMLOutput(data, htmlPath);
    }
    writeHTMLOutput(data, outputPath) {
        let html = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Generation Results</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 2.5em;
        }
        .summary {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .lead-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .lead-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .lead-title {
            color: #007bff;
            font-size: 1.4em;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .lead-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            color: #212529;
            margin-top: 5px;
        }
        .rating {
            color: #28a745;
            font-weight: bold;
        }
        .score {
            color: #dc3545;
            font-weight: bold;
        }
        .website a {
            color: #007bff;
            text-decoration: none;
        }
        .website a:hover {
            text-decoration: underline;
        }
        .tags {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Lead Generation Results</h1>
        </div>
        
        <div class="summary">
            <h2>Summary</h2>
            <p><strong>Total Leads:</strong> ${data.length}</p>
            <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
        </div>
        
        <div class="leads">
`;
        data.forEach((lead, index) => {
            const websiteLink = lead.website !== 'N/A' ? `<a href="${lead.website}" target="_blank">${lead.website}</a>` : 'N/A';
            html += `
            <div class="lead-card">
                <div class="lead-title">${index + 1}. ${lead.company_name}</div>
                <div class="lead-info">
                    <div class="info-item">
                        <div class="info-label">Website</div>
                        <div class="info-value website">${websiteLink}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value">${lead.phone}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">${lead.email}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">${lead.address}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Rating</div>
                        <div class="info-value rating">${lead.rating} (${lead.review_count} reviews)</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Lead Score</div>
                        <div class="info-value score">${lead.lead_score}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Score Details</div>
                        <div class="info-value">${lead.score_explanations}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tags</div>
                        <div class="info-value tags">${lead.tags}</div>
                    </div>
                </div>
            </div>
`;
        });
        html += `
        </div>
    </div>
</body>
</html>`;
        fs.writeFileSync(outputPath, html, 'utf-8');
    }
    createSummaryReport() {
        const files = fs.readdirSync(this.inputDir);
        const csvFiles = files.filter(file => file.endsWith('.csv'));
        let summary = '';
        summary += '='.repeat(80) + '\n';
        summary += 'CSV FILES SUMMARY REPORT\n';
        summary += '='.repeat(80) + '\n\n';
        let totalLeads = 0;
        for (const file of csvFiles) {
            const filePath = path.join(this.inputDir, file);
            try {
                const content = fs.readFileSync(filePath, 'utf-8');
                const lines = content.trim().split('\n');
                const leadCount = Math.max(0, lines.length - 1);
                totalLeads += leadCount;
                summary += `${file}\n`;
                summary += `  Leads: ${leadCount}\n`;
                summary += `  Size: ${(fs.statSync(filePath).size / 1024).toFixed(2)} KB\n\n`;
            }
            catch (error) {
                summary += `${file}\n`;
                summary += `  Error reading file\n\n`;
            }
        }
        summary += `Total Leads Across All Files: ${totalLeads}\n`;
        summary += `Report Generated: ${new Date().toLocaleString()}\n`;
        const summaryPath = path.join(this.outputDir, 'summary_report.txt');
        fs.writeFileSync(summaryPath, summary, 'utf-8');
        console.log(`Summary report created: ${path.basename(summaryPath)}`);
    }
}
exports.CSVFormatter = CSVFormatter;
if (require.main === module) {
    const formatter = new CSVFormatter();
    const args = process.argv.slice(2);
    if (args.length === 0) {
        console.log('CSV Formatter - Converting CSV files to readable format');
        console.log('Usage:');
        console.log('  npm run format-csv                    # Format all CSV files');
        console.log('  npm run format-csv <filename>         # Format specific file');
        console.log('  npm run format-csv --summary          # Create summary report');
    }
    else if (args[0] === '--summary') {
        formatter.createSummaryReport();
    }
    else if (args[0] === '--all') {
        formatter.formatAllCSVs();
        formatter.createSummaryReport();
    }
    else {
        formatter.formatCSVFile(args[0]);
    }
}
//# sourceMappingURL=csv-formatter.js.map