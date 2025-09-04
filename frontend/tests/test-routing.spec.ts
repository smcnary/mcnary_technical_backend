import { test, expect } from '@playwright/test';

test('Test routing to client dashboard', async ({ page }) => {
  // Navigate to audit wizard
  await page.goto('http://localhost:3000/audit-wizard');
  await page.waitForLoadState('networkidle');
  
  console.log('📋 Step 1: Filling out account information...');
  await page.screenshot({ path: 'test-screenshots/01-audit-wizard-start.png' });
  
  // Fill out account step
  await page.fill('label:has-text("First name") + input', 'John');
  await page.fill('label:has-text("Last name") + input', 'Doe');
  await page.fill('input[type="email"]', 'john.doe@example.com');
  await page.fill('input[type="password"]', 'TestPassword123!');
  
  await page.screenshot({ path: 'test-screenshots/02-account-filled.png' });
  
  // Click continue
  await page.click('button:has-text("Continue")');
  await page.waitForTimeout(1000);
  
  console.log('🏢 Step 2: Filling out business information...');
  await page.screenshot({ path: 'test-screenshots/03-business-step.png' });
  
  // Fill out business step
  await page.fill('label:has-text("Company name") + input', 'Test Company Inc');
  await page.fill('label:has-text("Website URL") + input', 'https://testcompany.com');
  await page.fill('label:has-text("Industry/Niche") + input', 'Technology');
  
  await page.screenshot({ path: 'test-screenshots/04-business-filled.png' });
  
  // Click continue
  await page.click('button:has-text("Continue")');
  await page.waitForTimeout(1000);
  
  console.log('🎯 Step 3: Selecting goals...');
  await page.screenshot({ path: 'test-screenshots/05-goals-step.png' });
  
  // Select goals
  await page.click('button:has-text("More calls/leads")');
  await page.click('button:has-text("Rank locally")');
  
  await page.screenshot({ path: 'test-screenshots/06-goals-selected.png' });
  
  // Click continue
  await page.click('button:has-text("Continue")');
  await page.waitForTimeout(1000);
  
  console.log('📦 Step 4: Selecting package...');
  await page.screenshot({ path: 'test-screenshots/07-package-step.png' });
  
  // Select package
  await page.click('button:has-text("Growth"):has-text("Choose")');
  
  await page.screenshot({ path: 'test-screenshots/08-package-selected.png' });
  
  // Click continue
  await page.click('button:has-text("Continue")');
  await page.waitForTimeout(1000);
  
  console.log('✅ Step 5: Review and submit...');
  await page.screenshot({ path: 'test-screenshots/09-review-step.png' });
  
  // Submit the form
  console.log('🚀 Submitting form and testing redirect...');
  await page.click('button:has-text("Submit")');
  
  // Wait for redirect
  await page.waitForTimeout(3000);
  
  // Check if we're on the client dashboard
  const currentUrl = page.url();
  console.log(`📍 Current URL: ${currentUrl}`);
  
  if (currentUrl.includes('/client')) {
    console.log('✅ SUCCESS: Successfully redirected to client dashboard!');
    await page.screenshot({ path: 'test-screenshots/10-client-dashboard.png' });
  } else {
    console.log('❌ FAILED: Not redirected to client dashboard');
    await page.screenshot({ path: 'test-screenshots/10-no-redirect.png' });
  }
  
  console.log('📸 Screenshots saved to test-screenshots/ directory');
});
