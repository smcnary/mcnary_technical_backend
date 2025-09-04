// Utility functions for faster audit wizard development and testing
import { expect } from '@playwright/test';

export interface AuditWizardData {
  account: {
    firstName: string;
    lastName: string;
    email: string;
    password: string;
  };
  business: {
    companyName: string;
    website: string;
    industry: string;
    monthlyBudget: string;
    competitors: string;
  };
  goals: string[];
  notes: string;
  tier: string;
}

export const generateTestData = (overrides: Partial<AuditWizardData> = {}): AuditWizardData => {
  const timestamp = Date.now();
  const baseData: AuditWizardData = {
    account: {
      firstName: `Test${timestamp}`,
      lastName: 'User',
      email: `test${timestamp}@example.com`,
      password: 'TestPassword123!'
    },
    business: {
      companyName: `Test Company ${timestamp}`,
      website: `https://testcompany${timestamp}.com`,
      industry: 'Technology',
      monthlyBudget: '$5000',
      competitors: 'competitor1.com, competitor2.com'
    },
    goals: ['More calls/leads', 'Technical SEO'],
    notes: `Test audit submission ${timestamp}`,
    tier: 'Audit'
  };

  return { ...baseData, ...overrides };
};

export const fillAccountStep = async (page: any, data: AuditWizardData) => {
  await page.fill('label:has-text("First name") + input', data.account.firstName);
  await page.fill('label:has-text("Last name") + input', data.account.lastName);
  await page.fill('input[type="email"]', data.account.email);
  await page.fill('input[type="password"]', data.account.password);
};

export const fillBusinessStep = async (page: any, data: AuditWizardData) => {
  await page.fill('label:has-text("Company name") + input', data.business.companyName);
  await page.fill('label:has-text("Website URL") + input', data.business.website);
  await page.fill('label:has-text("Industry/Niche") + input', data.business.industry);
  await page.fill('label:has-text("Approx. monthly budget") + input', data.business.monthlyBudget);
  await page.fill('label:has-text("Top competitors") + textarea', data.business.competitors);
};

export const fillGoalsStep = async (page: any, data: AuditWizardData) => {
  for (const goal of data.goals) {
    await page.click(`button:has-text("${goal}")`);
  }
  await page.fill('label:has-text("Notes") + textarea', data.notes);
};

export const selectPackage = async (page: any, data: AuditWizardData) => {
  await page.click(`button:has-text("${data.tier}")`);
};

export const completeWizard = async (page: any, data: AuditWizardData) => {
  // Step 1: Account
  await fillAccountStep(page, data);
  await page.click('button:has-text("Continue")');
  await page.waitForSelector('text=Tell us about your business');

  // Step 2: Business
  await fillBusinessStep(page, data);
  await page.click('button:has-text("Continue")');
  await page.waitForSelector('text=What are your goals?');

  // Step 3: Goals
  await fillGoalsStep(page, data);
  await page.click('button:has-text("Continue")');
  await page.waitForSelector('text=Choose Your Package');

  // Step 4: Package
  await selectPackage(page, data);
  await page.click('button:has-text("Continue")');
  await page.waitForSelector('text=Confirm & Submit');

  // Step 5: Submit (use specific selector to avoid breadcrumb button)
  await page.click('button:has-text("Submit"):not(:has-text("Step 5"))');
  
  // Wait for success step
  await page.waitForSelector('text=Audit Submitted Successfully!', { timeout: 10000 });
};

export const validateFormData = async (page: any, data: AuditWizardData) => {
  await expect(page.locator('text=' + data.account.firstName)).toBeVisible();
  await expect(page.locator('text=' + data.account.lastName)).toBeVisible();
  await expect(page.locator('text=' + data.account.email)).toBeVisible();
  await expect(page.locator('text=' + data.business.companyName)).toBeVisible();
  await expect(page.locator('text=' + data.tier)).toBeVisible();
};

// Quick test scenarios for development
export const testScenarios = {
  minimal: () => generateTestData({
    goals: ['More calls/leads'],
    notes: 'Minimal test'
  }),
  
  full: () => generateTestData({
    goals: ['More calls/leads', 'Rank locally', 'Technical SEO', 'Content strategy'],
    notes: 'Comprehensive test with all goals selected'
  }),
  
  enterprise: () => generateTestData({
    tier: 'Audit + Retainer',
    business: {
      companyName: 'Enterprise Company',
      website: 'https://enterprise.com',
      industry: 'Technology',
      monthlyBudget: '$10000',
      competitors: 'enterprise-competitor.com'
    },
    notes: 'Enterprise level test'
  }),
  
  invalid: () => generateTestData({
    account: {
      firstName: '',
      lastName: '',
      email: 'invalid-email',
      password: 'weak'
    },
    business: {
      companyName: '',
      website: 'not-a-url',
      industry: '',
      monthlyBudget: '',
      competitors: ''
    },
    goals: [],
    notes: '',
    tier: ''
  })
};
