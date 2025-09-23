#!/usr/bin/env node

/**
 * Leadgen Integration Test Script
 * 
 * This script tests the leadgen integration by making API calls to the backend.
 * Usage: node test-leadgen-integration.js
 */

const https = require('https');
const http = require('http');

const API_BASE_URL = process.env.API_BASE_URL || 'http://localhost:8000';

// Sample campaign configuration
const sampleCampaign = {
  name: 'Test Tulsa Attorneys Campaign',
  vertical: 'local_services',
  geo: {
    city: 'Tulsa',
    region: 'OK',
    country: 'US',
    radius_km: 30
  },
  filters: {
    min_rating: 3.0,
    keywords: ['attorney', 'lawyer'],
    exclude_keywords: ['criminal'],
    max_results: 50
  },
  sources: ['google_places'],
  enrichment: [],
  budget: {
    max_cost_usd: 25
  },
  schedule: {
    enabled: false
  }
};

async function makeRequest(path, method = 'GET', data = null) {
  return new Promise((resolve, reject) => {
    const url = new URL(path, API_BASE_URL);
    const options = {
      hostname: url.hostname,
      port: url.port || (url.protocol === 'https:' ? 443 : 80),
      path: url.pathname + url.search,
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    };

    if (data) {
      const jsonData = JSON.stringify(data);
      options.headers['Content-Length'] = Buffer.byteLength(jsonData);
    }

    const client = url.protocol === 'https:' ? https : http;
    const req = client.request(options, (res) => {
      let responseData = '';
      
      res.on('data', (chunk) => {
        responseData += chunk;
      });
      
      res.on('end', () => {
        try {
          const parsed = JSON.parse(responseData);
          resolve({ status: res.statusCode, data: parsed });
        } catch (e) {
          resolve({ status: res.statusCode, data: responseData });
        }
      });
    });

    req.on('error', (error) => {
      reject(error);
    });

    if (data) {
      req.write(JSON.stringify(data));
    }
    
    req.end();
  });
}

async function testLeadgenIntegration() {
  console.log('🧪 Testing Leadgen Integration');
  console.log('===============================\n');

  try {
    // Test 1: Get available verticals
    console.log('1️⃣ Testing get verticals...');
    const verticalsResponse = await makeRequest('/api/v1/admin/leadgen/verticals');
    console.log('✅ Verticals:', verticalsResponse.data);
    console.log('');

    // Test 2: Get available sources
    console.log('2️⃣ Testing get sources...');
    const sourcesResponse = await makeRequest('/api/v1/admin/leadgen/sources');
    console.log('✅ Sources:', sourcesResponse.data);
    console.log('');

    // Test 3: Get campaign template
    console.log('3️⃣ Testing get template...');
    const templateResponse = await makeRequest('/api/v1/admin/leadgen/template');
    console.log('✅ Template:', templateResponse.data);
    console.log('');

    // Test 4: Execute campaign (this will fail if leadgen service is not running)
    console.log('4️⃣ Testing campaign execution...');
    console.log('⚠️  Note: This will fail if the leadgen service is not running');
    
    try {
      const executeResponse = await makeRequest('/api/v1/admin/leadgen/execute', 'POST', sampleCampaign);
      console.log('✅ Campaign executed:', executeResponse.data);
    } catch (error) {
      console.log('❌ Campaign execution failed (expected if leadgen service is not running):', error.message);
    }
    console.log('');

    console.log('🎉 Integration test completed!');
    console.log('\n📋 Next steps:');
    console.log('1. Ensure the leadgen service is running');
    console.log('2. Update the API_BASE_URL environment variable if needed');
    console.log('3. Test the frontend interface at /admin/leadgen');

  } catch (error) {
    console.error('❌ Test failed:', error.message);
    process.exit(1);
  }
}

// Run the test
if (require.main === module) {
  testLeadgenIntegration();
}

module.exports = { testLeadgenIntegration, sampleCampaign };