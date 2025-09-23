const fs = require('fs');
const path = require('path');

// Read the real leadgen CSV file
const realLeadsFile = path.join(__dirname, 'exports', 'Tulsa_Attorneys_Simple_2025-09-23T12-38-16.csv');
const content = fs.readFileSync(realLeadsFile, 'utf8');

// Parse CSV
const lines = content.split('\n');
const headers = lines[0].split(',');
const leads = [];

for (let i = 1; i < lines.length; i++) {
    if (lines[i].trim()) {
        const values = lines[i].split(',');
        const lead = {};
        headers.forEach((header, index) => {
            lead[header] = values[index] || '';
        });
        leads.push(lead);
    }
}

// Convert to backend format
const backendLeads = leads.map(lead => {
    // Extract company name and create full name
    const companyName = lead.company_name || '';
    const fullName = companyName; // Use company name as full name
    
    // Extract email from website if available, or create placeholder
    let email = '';
    if (lead.website && lead.website.includes('http')) {
        const domain = lead.website.replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
        email = `contact@${domain}`;
    }
    
    // Clean phone number
    let phone = lead.primary_phone || '';
    phone = phone.replace(/[^\d\-\+\(\)\s]/g, '').trim();
    
    // Create message
    const message = `Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: ${lead.vertical} - Lead Score: ${lead.lead_score}`;
    
    return {
        full_name: fullName,
        email: email,
        phone: phone,
        firm: companyName,
        website: lead.website || '',
        city: lead.city || 'Tulsa',
        state: lead.region || 'OK',
        zip_code: lead.postal_code || '74101',
        message: message,
        practice_areas: 'attorney, lawyer, legal services',
        lead_score: lead.lead_score || 0,
        rating: lead.rating || 0,
        review_count: lead.review_count || 0,
        vertical: lead.vertical || 'local_services'
    };
});

// Write to CSV file for backend import
const backendHeaders = ['full_name', 'email', 'phone', 'firm', 'website', 'city', 'state', 'zip_code', 'message', 'practice_areas'];
const backendCsvContent = [
    backendHeaders.join(','),
    ...backendLeads.map(lead => backendHeaders.map(header => `"${lead[header] || ''}"`).join(','))
].join('\n');

const outputFile = path.join(__dirname, 'exports', 'tulsa_attorneys_real_import.csv');
fs.writeFileSync(outputFile, backendCsvContent);

console.log(`Converted ${backendLeads.length} real Tulsa attorney leads`);
console.log(`Output written to: ${outputFile}`);
console.log(`Sample leads:`);
backendLeads.slice(0, 3).forEach((lead, index) => {
    console.log(`${index + 1}. ${lead.full_name} - ${lead.email} - ${lead.phone}`);
});
