import { test, expect } from '@playwright/test';

test.describe('Audit Wizard Backend Integration', () => {
  test('should complete wizard flow and reach checkout step', async ({ page }) => {
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
    
    // Continue to checkout step
    await page.click('button:has-text("Continue")');
    await page.waitForSelector('text=Order Summary');
    
    // Verify we're on the checkout step
    await expect(page.locator('h3:has-text("Order Summary")')).toBeVisible();
    await expect(page.locator('span:has-text("SEO Audit")')).toBeVisible();
    await expect(page.locator('span.text-white.font-semibold:has-text("$799")')).toBeVisible();
    await expect(page.locator('button:has-text("Complete Payment")')).toBeVisible();
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

  test('should validate email format', async ({ page }) => {
    // Navigate to audit wizard
    await page.goto('/audit-wizard');
    
    // Fill fields with invalid email
    await page.fill('label:has-text("First name") + input', 'Test');
    await page.fill('label:has-text("Last name") + input', 'User');
    await page.fill('input[type="email"]', 'invalid-email');
    await page.fill('input[type="password"]', 'password123');
    
    // Button should be disabled with invalid email
    const continueButton = page.locator('button:has-text("Continue")');
    await expect(continueButton).toBeDisabled();
    
    // Fix email format
    await page.fill('input[type="email"]', 'test@example.com');
    
    // Now button should be enabled
    await expect(continueButton).toBeEnabled();
  });
});
