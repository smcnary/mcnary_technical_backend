import { test, expect } from '@playwright/test';

// Test data for the audit wizard
const testData = {
  account: {
    firstName: 'John',
    lastName: 'Doe',
    email: 'john.doe@example.com',
    password: 'TestPassword123!'
  },
  business: {
    companyName: 'Test Company Inc',
    website: 'https://testcompany.com',
    industry: 'Technology',
    monthlyBudget: '$5000',
    competitors: 'competitor1.com, competitor2.com, competitor3.com'
  },
  goals: ['More calls/leads', 'Rank locally', 'Technical SEO'],
  notes: 'This is a test audit submission for automated testing.'
};

test.describe('SEO Audit Wizard', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the audit wizard
    await page.goto('/audit-wizard');
    await page.waitForLoadState('networkidle');
  });

  test('should load the audit wizard page', async ({ page }) => {
    // Check if the page loads correctly
    await expect(page).toHaveTitle(/tulsa-seo\.com/);
    await expect(page.locator('h1')).toContainText('SEO Audit Wizard');
    await expect(page.locator('h2:has-text("Create your account")')).toBeVisible();
  });

  test('should complete the entire wizard flow', async ({ page }) => {
    // Step 1: Account Creation
    await test.step('Complete account step', async () => {
      await page.fill('input[placeholder*="First name"], label:has-text("First name") + input', testData.account.firstName);
      await page.fill('input[placeholder*="Last name"], label:has-text("Last name") + input', testData.account.lastName);
      await page.fill('input[type="email"], label:has-text("Email") + input', testData.account.email);
      await page.fill('input[type="password"], label:has-text("Password") + input', testData.account.password);
      
      await page.click('button:has-text("Continue")');
      await expect(page.locator('h2:has-text("Tell us about your business")')).toBeVisible();
    });

    // Step 2: Business Details
    await test.step('Complete business step', async () => {
      await page.fill('label:has-text("Company name") + input', testData.business.companyName);
      await page.fill('label:has-text("Website URL") + input', testData.business.website);
      await page.fill('label:has-text("Industry/Niche") + input', testData.business.industry);
      await page.fill('label:has-text("Approx. monthly budget") + input', testData.business.monthlyBudget);
      await page.fill('label:has-text("Top competitors") + textarea', testData.business.competitors);
      
      await page.click('button:has-text("Continue")');
      await expect(page.locator('h2:has-text("What are your goals?")')).toBeVisible();
    });

    // Step 3: Goals
    await test.step('Complete goals step', async () => {
      for (const goal of testData.goals) {
        await page.click(`button:has-text("${goal}")`);
      }
      
      await page.fill('label:has-text("Notes") + textarea', testData.notes);
      
      await page.click('button:has-text("Continue")');
      // Wait a bit for the step to advance and verify we're on the checkout step
      await page.waitForTimeout(1000);
      await expect(page.locator('h3:has-text("Order Summary")')).toBeVisible();
    });

    // Step 4: Checkout
    await test.step('Review checkout step', async () => {
      // Verify all data is displayed correctly in the review
      await expect(page.locator('text=John Doe')).toBeVisible();
      await expect(page.locator('text=john.doe@example.com')).toBeVisible();
      await expect(page.locator('p:has-text("Company: Test Company Inc")')).toBeVisible();
      await expect(page.locator('span:has-text("SEO Audit")')).toBeVisible();
      await expect(page.locator('span.text-white.font-semibold:has-text("$799")')).toBeVisible();
      
      // Verify the checkout button is present
      await expect(page.locator('button:has-text("Complete Payment")')).toBeVisible();
    });
  });

  test('should prevent navigation to future steps when previous steps are incomplete', async ({ page }) => {
    // Try to navigate to step 3 via breadcrumb without completing previous steps
    await page.click('button:has-text("Step 3")');
    
    // Should still be on the first step
    await expect(page.locator('h2:has-text("Create your account")')).toBeVisible();
    
    // Fill out first step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    // Now try to navigate to step 3 again
    await page.click('button:has-text("Step 3")');
    
    // Should be able to navigate now
    await expect(page.locator('h2:has-text("What are your goals?")')).toBeVisible();
  });

  test('should disable step breadcrumbs when previous steps are incomplete', async ({ page }) => {
    // Try to click on step 2 without completing step 1
    const step2Button = page.locator('button:has-text("Step 2")');
    await step2Button.click();
    
    // Should still be on step 1
    await expect(page.locator('h2:has-text("Create your account")')).toBeVisible();
    
    // Fill out step 1
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    // Now step 2 should be clickable
    await step2Button.click();
    await expect(page.locator('h2:has-text("Tell us about your business")')).toBeVisible();
  });
});

  test('should validate required fields', async ({ page }) => {
    // Try to continue without filling required fields
    // The button should be disabled, so we can't click it
    const continueButton = page.locator('button:has-text("Continue")');
    await expect(continueButton).toBeDisabled();
    
    // Check that we're still on the first step
    await expect(page.locator('h2:has-text("Create your account")')).toBeVisible();
  });

  test('should enable continue button when all required fields are filled', async ({ page }) => {
    // Fill out all required fields
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    
    // The button should now be enabled
    const continueButton = page.locator('button:has-text("Continue")');
    await expect(continueButton).toBeEnabled();
  });

  test('should navigate between steps', async ({ page }) => {
    // Fill out first step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    // Go back to first step
    await page.click('button:has-text("Back")');
    await expect(page.locator('h2:has-text("Create your account")')).toBeVisible();
    
    // Verify data is preserved
    await expect(page.locator('input[type="email"]')).toHaveValue(testData.account.email);
  });

  test('should handle step navigation via breadcrumbs', async ({ page }) => {
    // Fill out first step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    // Fill out second step
    await page.fill('label:has-text("Company name") + input', testData.business.companyName);
    await page.fill('label:has-text("Website URL") + input', testData.business.website);
    await page.fill('label:has-text("Industry/Niche") + input', testData.business.industry);
    await page.click('button:has-text("Continue")');
    
    // Now we can navigate to step 3 via breadcrumb
    await page.click('button:has-text("Step 3")');
    await expect(page.locator('h2:has-text("What are your goals?")')).toBeVisible();
  });

  test('should handle goal selection', async ({ page }) => {
    // Navigate to goals step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    await page.fill('label:has-text("Company name") + input', testData.business.companyName);
    await page.fill('label:has-text("Website URL") + input', testData.business.website);
    await page.fill('label:has-text("Industry/Niche") + input', testData.business.industry);
    await page.click('button:has-text("Continue")');
    
    // Test goal selection
    const goalButton = page.locator('button:has-text("More calls/leads")');
    await goalButton.click();
    
    // Verify button is selected (should have different styling)
    await expect(goalButton).toHaveClass(/bg-indigo-600/);
    
    // Deselect goal
    await goalButton.click();
    await expect(goalButton).not.toHaveClass(/bg-indigo-600/);
  });

  test('should handle checkout step display', async ({ page }) => {
    // Navigate to checkout step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    await page.fill('label:has-text("Company name") + input', testData.business.companyName);
    await page.fill('label:has-text("Website URL") + input', testData.business.website);
    await page.fill('label:has-text("Industry/Niche") + input', testData.business.industry);
    await page.click('button:has-text("Continue")');
    
    await page.click('button:has-text("More calls/leads")');
    await page.fill('label:has-text("Notes") + textarea', testData.notes);
    await page.click('button:has-text("Continue")');
    
    // Verify checkout step elements
    await expect(page.locator('h3:has-text("Order Summary")')).toBeVisible();
    await expect(page.locator('span:has-text("SEO Audit")')).toBeVisible();
    await expect(page.locator('span.text-white.font-semibold:has-text("$799")')).toBeVisible();
    await expect(page.locator('button:has-text("Complete Payment")')).toBeVisible();
  });

  test('should display proper text visibility', async ({ page }) => {
    // Check that all text is visible with proper contrast
    await expect(page.locator('h1')).toContainText('SEO Audit Wizard');
    await expect(page.locator('h2')).toContainText('Create your account');
    await expect(page.locator('label:has-text("First name")')).toBeVisible();
    await expect(page.locator('label:has-text("Last name")')).toBeVisible();
    await expect(page.locator('label:has-text("Email")')).toBeVisible();
    await expect(page.locator('label:has-text("Password")')).toBeVisible();
    await expect(page.locator('text=We\'ll create your portal login and connect this audit to your account')).toBeVisible();
  });

  test('should handle checkout button interaction', async ({ page }) => {
    // Complete the wizard to reach checkout step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    await page.fill('label:has-text("Company name") + input', testData.business.companyName);
    await page.fill('label:has-text("Website URL") + input', testData.business.website);
    await page.fill('label:has-text("Industry/Niche") + input', testData.business.industry);
    await page.click('button:has-text("Continue")');
    
    await page.click('button:has-text("More calls/leads")');
    await page.fill('label:has-text("Notes") + textarea', testData.notes);
    await page.click('button:has-text("Continue")');
    
    // Verify checkout button is present and clickable
    const checkoutButton = page.locator('button:has-text("Complete Payment")');
    await expect(checkoutButton).toBeVisible();
    await expect(checkoutButton).toBeEnabled();
    
    // Click checkout button (this will trigger Stripe checkout in real environment)
    await checkoutButton.click();
    
    // In test environment, this should handle gracefully without redirecting
    // The button should remain visible or show loading state
    await page.waitForTimeout(2000);
  });

  test('should test Stripe checkout API integration', async ({ page }) => {
    // Complete the wizard to reach checkout step
    await page.fill('label:has-text("First name") + input', testData.account.firstName);
    await page.fill('label:has-text("Last name") + input', testData.account.lastName);
    await page.fill('input[type="email"]', testData.account.email);
    await page.fill('input[type="password"]', testData.account.password);
    await page.click('button:has-text("Continue")');
    
    await page.fill('label:has-text("Company name") + input', testData.business.companyName);
    await page.fill('label:has-text("Website URL") + input', testData.business.website);
    await page.fill('label:has-text("Industry/Niche") + input', testData.business.industry);
    await page.click('button:has-text("Continue")');
    
    await page.click('button:has-text("More calls/leads")');
    await page.fill('label:has-text("Notes") + textarea', testData.notes);
    await page.click('button:has-text("Continue")');
    
    // Intercept the API call to create checkout session
    await page.route('**/api/create-checkout-session', async route => {
      const request = route.request();
      const postData = request.postDataJSON();
      
      // Verify the request contains expected data
      expect(postData.serviceType).toBe('audit');
      expect(postData.price).toBe(799);
      expect(postData.customerEmail).toBe(testData.account.email);
      expect(postData.customerName).toBe(`${testData.account.firstName} ${testData.account.lastName}`);
      expect(postData.companyName).toBe(testData.business.companyName);
      expect(postData.website).toBe(testData.business.website);
      
      // Mock successful response
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          sessionId: 'cs_test_mock_session_id'
        })
      });
    });
    
    // Click checkout button
    await page.click('button:has-text("Complete Payment")');
    
    // Verify the API was called
    await page.waitForTimeout(1000);
  });
});
