import { test, expect } from '@playwright/test';

test.describe('Audit Wizard Debug', () => {
  test('debug step by step', async ({ page }) => {
    // Listen to console logs
    page.on('console', msg => console.log('Browser console:', msg.text()));
    
    await page.goto('/audit-wizard');
    
    // Step 1: Fill account details
    console.log('Filling account details...');
    await page.fill('label:has-text("First name") + input', 'John');
    await page.fill('label:has-text("Last name") + input', 'Doe');
    await page.fill('input[type="email"]', 'john@example.com');
    await page.fill('input[type="password"]', 'password123');
    
    // Click continue
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Tell us about your business');
    console.log('Step 1 completed');
    
    // Step 2: Fill business details
    console.log('Filling business details...');
    await page.fill('label:has-text("Company name") + input', 'Test Company');
    await page.fill('label:has-text("Website URL") + input', 'https://test.com');
    await page.fill('label:has-text("Industry/Niche") + input', 'Technology');
    
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=What are your goals?');
    console.log('Step 2 completed');
    
    // Step 3: Select goals
    console.log('Selecting goals...');
    await page.click('button:has-text("More calls/leads")');
    await page.fill('label:has-text("Notes") + textarea', 'Test notes');
    
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Choose Your Package');
    console.log('Step 3 completed');
    
    // Step 4: Select package
    console.log('Selecting package...');
    await page.click('button:has-text("Audit")');
    
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Confirm & Submit');
    console.log('Step 4 completed');
    
    // Step 5: Submit
    console.log('Submitting...');
    
    // Check if submit button exists and is enabled (use more specific selector)
    const submitButton = page.locator('button:has-text("Submit"):not(:has-text("Step 5"))');
    await expect(submitButton).toBeVisible();
    await expect(submitButton).toBeEnabled();
    
    console.log('Submit button found and enabled, clicking...');
    await submitButton.click();
    
    // Wait a bit and check what's on the page
    await page.waitForTimeout(2000);
    const pageContent = await page.content();
    console.log('Page content after submit:', pageContent.substring(0, 500));
    
    // Wait for success message
    await page.waitForSelector('text=Audit submitted successfully', { timeout: 15000 });
    console.log('Success message found!');
    
    await expect(page.locator('text=Audit submitted successfully')).toBeVisible();
  });
});
