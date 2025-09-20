const { chromium } = require('playwright');

async function testSeoClientsAccess() {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log('🚀 Starting SEO Clients access control test...');
    
    // Test 1: Navigate to dashboard without authentication
    console.log('📋 Test 1: Accessing dashboard without authentication...');
    await page.goto('http://localhost:3000/client');
    await page.waitForLoadState('networkidle');
    
    // Check if we're redirected to login or see access denied
    const currentUrl = page.url();
    console.log(`📍 Current URL: ${currentUrl}`);
    
    if (currentUrl.includes('/login')) {
      console.log('✅ SUCCESS: Redirected to login page (expected for unauthenticated users)');
    } else {
      console.log('❌ FAILED: Should be redirected to login page');
    }
    
    await page.screenshot({ path: 'test-screenshots/seo-clients-01-unauthenticated.png' });
    
    // Test 2: Try to access SEO Clients tab directly via URL parameter
    console.log('📋 Test 2: Trying to access SEO Clients tab directly...');
    await page.goto('http://localhost:3000/client?tab=seo-clients');
    await page.waitForLoadState('networkidle');
    
    const currentUrl2 = page.url();
    console.log(`📍 Current URL after direct SEO Clients access: ${currentUrl2}`);
    
    if (currentUrl2.includes('/login')) {
      console.log('✅ SUCCESS: Redirected to login page (expected for unauthenticated users)');
    } else {
      console.log('❌ FAILED: Should be redirected to login page');
    }
    
    await page.screenshot({ path: 'test-screenshots/seo-clients-02-direct-access.png' });
    
    // Note: For full testing, you would need to:
    // 1. Login as different user roles (admin, sales consultant, client admin, client staff)
    // 2. Check if SEO Clients tab is visible in navigation
    // 3. Check if SEO Clients tab content is accessible
    // 4. Verify that client admin and client staff cannot see the tab
    
    console.log('📝 Note: Full role-based testing requires authentication setup');
    console.log('📝 To complete testing, manually test with different user roles:');
    console.log('   - Admin: Should see SEO Clients tab');
    console.log('   - Sales Consultant: Should see SEO Clients tab');
    console.log('   - Client Admin: Should NOT see SEO Clients tab');
    console.log('   - Client Staff: Should NOT see SEO Clients tab');
    
  } catch (error) {
    console.error('❌ Test failed:', error);
    await page.screenshot({ path: 'test-screenshots/seo-clients-error.png' });
  } finally {
    await browser.close();
  }
}

// Create screenshots directory
const fs = require('fs');
if (!fs.existsSync('test-screenshots')) {
  fs.mkdirSync('test-screenshots');
}

testSeoClientsAccess();
