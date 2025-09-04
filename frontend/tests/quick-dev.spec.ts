import { test, expect } from '@playwright/test';
import { generateTestData, completeWizard, validateFormData } from './utils/audit-wizard-utils';

test.describe('Quick Development Tests', () => {
  test('quick wizard completion', async ({ page }) => {
    await page.goto('/audit-wizard');
    
    const testData = generateTestData();
    await completeWizard(page, testData);
    
    // Verify success step is reached
    await expect(page.locator('text=Audit Submitted Successfully!')).toBeVisible();
    
    // Verify redirect message
    await expect(page.locator('text=Redirecting to your dashboard')).toBeVisible();
  });

  test('test different scenarios', async ({ page }) => {
    const scenarios = [
      generateTestData({ goals: ['More calls/leads'] }),
      generateTestData({ goals: ['More calls/leads', 'Rank locally', 'Technical SEO'] }),
      generateTestData({ tier: 'Audit + Retainer' })
    ];

    for (const scenario of scenarios) {
      await page.goto('/audit-wizard');
      await completeWizard(page, scenario);
      await expect(page.locator('text=Audit Submitted Successfully!')).toBeVisible();
      await expect(page.locator('text=Redirecting to your dashboard')).toBeVisible();
    }
  });

  test('test form validation', async ({ page }) => {
    await page.goto('/audit-wizard');
    
    // Try to continue without filling fields
    await page.click('button:has-text("Continue")');
    await expect(page.locator('text=Create your account')).toBeVisible();
  });
});
