import { test, expect } from '@playwright/test';

test.describe('Lead Management Debug', () => {
  test('should debug page loading', async ({ page }) => {
    // Mock authentication
    await page.goto('/login');
    await page.evaluate(() => {
      localStorage.setItem('auth_token', 'mock-jwt-token-' + Date.now());
    });
    
    // Set authentication cookies
    await page.context().addCookies([
      {
        name: 'auth',
        value: 'mock-jwt-token-' + Date.now(),
        domain: 'localhost',
        path: '/'
      },
      {
        name: 'role',
        value: 'ROLE_AGENCY_ADMIN',
        domain: 'localhost',
        path: '/'
      }
    ]);

    // Mock API responses
    await page.route('**/api/v1/user-profile/greeting', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: {
            id: 'mock-user-id',
            email: 'admin@example.com',
            firstName: 'Admin',
            lastName: 'User',
            name: 'Admin User',
            role: 'ROLE_AGENCY_ADMIN',
            status: 'active'
          }
        })
      });
    });

    // Mock the /api/v1/me endpoint that auth service calls
    await page.route('**/api/v1/me', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          id: 'mock-user-id',
          email: 'admin@example.com',
          firstName: 'Admin',
          lastName: 'User',
          name: 'Admin User',
          roles: ['ROLE_AGENCY_ADMIN'],
          status: 'active',
          lastLoginAt: '2025-01-01T00:00:00Z'
        })
      });
    });

    await page.route('**/api/v1/leads**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [],
          pagination: {
            page: 1,
            per_page: 25,
            total: 0,
            pages: 1
          }
        })
      });
    });

    // Navigate to SEO clients page
    console.log('Navigating to /seo-clients...');
    await page.goto('/seo-clients');
    
    // Wait for page to load
    await page.waitForLoadState('networkidle');
    
    // Take a screenshot to see what's on the page
    await page.screenshot({ path: 'debug-seo-clients.png' });
    
    // Log page content
    const pageContent = await page.content();
    console.log('Page content length:', pageContent.length);
    
    // Check if any text contains "Leads"
    const hasLeadsText = await page.locator('text=Leads').count();
    console.log('Found "Leads" text count:', hasLeadsText);
    
    // Check if any text contains "Management"
    const hasManagementText = await page.locator('text=Management').count();
    console.log('Found "Management" text count:', hasManagementText);
    
    // Check if any text contains "Create"
    const hasCreateText = await page.locator('text=Create').count();
    console.log('Found "Create" text count:', hasCreateText);
    
    // Check if any text contains "Lead"
    const hasLeadText = await page.locator('text=Lead').count();
    console.log('Found "Lead" text count:', hasLeadText);
    
    // List all visible text on the page
    const allText = await page.evaluate(() => {
      const elements = Array.from(document.querySelectorAll('*'));
      return elements
        .filter(el => el.textContent && el.textContent.trim().length > 0)
        .map(el => el.textContent?.trim())
        .filter((text, index, arr) => arr.indexOf(text) === index) // Remove duplicates
        .slice(0, 20); // First 20 unique texts
    });
    
    console.log('First 20 visible texts on page:', allText);
    
    // Check for any error messages
    const errorText = await page.locator('text=Error').count();
    console.log('Found "Error" text count:', errorText);
    
    const loadingText = await page.locator('text=Loading').count();
    console.log('Found "Loading" text count:', loadingText);
    
    // Wait a bit more to see if content loads
    await page.waitForTimeout(3000);
    
    // Check again for "Leads Management"
    const leadsManagementExists = await page.locator('text=Leads Management').count();
    console.log('Found "Leads Management" count after wait:', leadsManagementExists);
    
    if (leadsManagementExists > 0) {
      console.log('✅ Found "Leads Management" text!');
    } else {
      console.log('❌ Still no "Leads Management" text found');
    }
    
    // This test is just for debugging, so we'll always pass
    expect(true).toBe(true);
  });
});
