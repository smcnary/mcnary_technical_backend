#!/usr/bin/env node

/**
 * Leadgen Data Converter
 * 
 * This script converts leadgen JSON data to the format expected by the backend API.
 * Usage: node convert-leadgen.js <input-file> [output-file]
 */

const fs = require('fs');
const path = require('path');

function convertLeadgenData(inputData) {
  // Handle both single objects and arrays
  const leads = Array.isArray(inputData) ? inputData : [inputData];
  
  return leads.map(lead => {
    // Extract primary contact information
    const primaryEmail = extractPrimaryEmail(lead);
    const primaryPhone = extractPrimaryPhone(lead);
    
    if (!primaryEmail) {
      throw new Error(`Lead ${lead.lead_id || 'unknown'} has no valid email`);
    }
    
    return {
      lead_id: lead.lead_id || generateId(),
      legal_entity: {
        name: lead.legal_entity?.name || lead.company_name || 'Unknown Company',
        alt_names: lead.legal_entity?.alt_names || [],
        registration_id: lead.legal_entity?.registration_id || null,
        jurisdictions: lead.legal_entity?.jurisdictions || []
      },
      brand: {
        name: lead.brand?.name || null
      },
      vertical: lead.vertical || 'other',
      website: lead.website || null,
      domains: lead.domains || [],
      emails: lead.emails || (primaryEmail ? [{
        value: primaryEmail,
        type: 'generic',
        verified: null,
        provider: null
      }] : []),
      phones: lead.phones || (primaryPhone ? [{
        value: primaryPhone,
        type: 'main',
        provider: null
      }] : []),
      address: lead.address || null,
      geo: lead.geo || null,
      social: lead.social || {
        linkedin: null,
        twitter: null,
        facebook: null,
        instagram: null
      },
      firmographics: lead.firmographics || {
        employees_range: null,
        revenue_range: null,
        founded_year: null
      },
      tech_signals: lead.tech_signals || [],
      reviews: lead.reviews || {
        count: 0,
        rating: null,
        last_reviewed_at: null
      },
      hours: lead.hours || [],
      tags: lead.tags || [],
      lead_score: lead.lead_score || null,
      score_explanations: lead.score_explanations || [],
      provenance: lead.provenance || [],
      source_records: lead.source_records || [],
      created_at: new Date(),
      updated_at: new Date()
    };
  });
}

function extractPrimaryEmail(lead) {
  if (lead.emails && Array.isArray(lead.emails) && lead.emails.length > 0) {
    // Prefer personal emails over generic/role emails
    const personalEmail = lead.emails.find(email => email.type === 'personal');
    if (personalEmail) return personalEmail.value;
    
    // Fall back to first email
    return lead.emails[0].value;
  }
  
  // Fallback to direct email field
  return lead.email || lead.primary_email || null;
}

function extractPrimaryPhone(lead) {
  if (lead.phones && Array.isArray(lead.phones) && lead.phones.length > 0) {
    // Prefer main phone over mobile/fax
    const mainPhone = lead.phones.find(phone => phone.type === 'main');
    if (mainPhone) return mainPhone.value;
    
    // Fall back to first phone
    return lead.phones[0].value;
  }
  
  // Fallback to direct phone field
  return lead.phone || lead.primary_phone || null;
}

function generateId() {
  return 'lead-' + Math.random().toString(36).substr(2, 9);
}

function main() {
  const args = process.argv.slice(2);
  
  if (args.length === 0) {
    console.error('Usage: node convert-leadgen.js <input-file> [output-file]');
    process.exit(1);
  }
  
  const inputFile = args[0];
  const outputFile = args[1] || inputFile.replace(/\.(json|js)$/, '.converted.json');
  
  try {
    // Read input file
    const inputData = JSON.parse(fs.readFileSync(inputFile, 'utf8'));
    
    // Convert data
    const convertedData = convertLeadgenData(inputData);
    
    // Write output file
    fs.writeFileSync(outputFile, JSON.stringify(convertedData, null, 2));
    
    console.log(`✅ Converted ${convertedData.length} leads`);
    console.log(`📁 Input: ${inputFile}`);
    console.log(`📁 Output: ${outputFile}`);
    
    // Show sample of converted data
    if (convertedData.length > 0) {
      console.log('\n📋 Sample converted lead:');
      console.log(JSON.stringify(convertedData[0], null, 2));
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    process.exit(1);
  }
}

if (require.main === module) {
  main();
}

module.exports = { convertLeadgenData };
