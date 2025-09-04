import { test, expect } from '@playwright/test';

test.describe('Audit Wizard Backend Integration', () => {
  test('should submit audit wizard and redirect to dashboard', async ({ page }) => {
    // Navigate to audit wizard
    await page.goto('/audit-wizard');
    
    // Fill out the form
    await page.fill('label:has-text("First name") + input', 'Test');
    await page.fill('label:has-text("Last name") + input', 'User');
    await page.fill('input[type="email"]', 'test@example.com');
    await page.fill('input[type="password"]', 'password123');
    
    // Continue to next step
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Tell us about your business');
    
    // Fill business details
    await page.fill('label:has-text("Company name") + input', 'Test Company');
    await page.fill('label:has-text("Website URL") + input', 'https://test.com');
    await page.fill('label:has-text("Industry/Niche") + input', 'Technology');
    await page.fill('label:has-text("Approx. monthly budget") + input', '$5000');
    await page.fill('label:has-text("Top competitors") + textarea', 'competitor1.com, competitor2.com');
    
    // Continue to goals
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=What are your goals?');
    
    // Select goals
    await page.click('button:has-text("More calls/leads")');
    await page.fill('label:has-text("Notes") + textarea', 'Test notes');
    
    // Continue to package selection
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Choose Your Package');
    
    // Select package
    await page.click('button:has-text("Growth")');
    
    // Continue to review
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Confirm & Submit');
    
    // Submit the form
    await page.click('button:has-text("Submit"):not(:has-text("Step 5"))');
    
    // Wait for success step
    await page.waitForSelector('text=Audit Submitted Successfully!', { timeout: 15000 });
    
    // Verify success message
    await expect(page.locator('text=Audit Submitted Successfully!')).toBeVisible();
    
    // Verify redirect message
    await expect(page.locator('text=Redirecting to your dashboard')).toBeVisible();
    
    // Wait for redirect to dashboard
    await page.waitForURL('**/client', { timeout: 10000 });
    
    // Verify we're on the client dashboard
    await expect(page).toHaveURL(/.*\/client/);
  });

  test('should not allow proceeding without completing required fields', async ({ page }) => {
    // Navigate to audit wizard
    await page.goto('/audit-wizard');
    
    // Try to continue without filling any fields
    const continueButton = page.locator('button:has-text("Continue")');
    await expect(continueButton).toBeDisabled();
    
    // Fill only some fields
    await page.fill('label:has-text("First name") + input', 'Test');
    await page.fill('label:has-text("Last name") + input', 'User');
    // Don't fill email and password
    
    // Button should still be disabled
    await expect(continueButton).toBeDisabled();
    
    // Fill all required fields
    await page.fill('input[type="email"]', 'test@example.com');
    await page.fill('input[type="password"]', 'password123');
    
    // Now button should be enabled
    await expect(continueButton).toBeEnabled();
  });
});
