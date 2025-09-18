import { test, expect } from '@playwright/test';

test.describe('Dashboard Debug', () => {
  test('debug dashboard loading', async ({ page }) => {
    // Mock authentication by setting localStorage and cookies
    await page.goto('/login');
    await page.evaluate(() => {
      localStorage.setItem('auth_token', 'mock-jwt-token-' + Date.now());
    });
    
    // Set the cookies that middleware expects
    await page.context().addCookies([
      {
        name: 'auth',
        value: 'mock-jwt-token-' + Date.now(),
        domain: 'localhost',
        path: '/'
      },
      {
        name: 'role',
        value: 'ROLE_CLIENT_USER',
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
            email: 'test@example.com',
            firstName: 'Test',
            lastName: 'User',
            name: 'Test User',
            role: 'ROLE_CLIENT_USER',
            status: 'active',
            lastLoginAt: '2025-01-01T00:00:00Z'
          },
          agency: {
            id: 'mock-agency-id',
            name: 'Test Agency',
            domain: 'testagency.com',
            description: 'Test Agency Description'
          },
          client: {
            id: 'mock-client-id',
            name: 'Test Client',
            slug: 'test-client',
            description: 'Test Client Description',
            status: 'active'
          },
          greeting: {
            displayName: 'Test User',
            organizationName: 'Test Client',
            userRole: 'Client User',
            timeBasedGreeting: 'Good morning'
          }
        })
      });
    });

    await page.route('**/api/v1/leads**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [],
          meta: { total: 0 }
        })
      });
    });

    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [],
          meta: { total: 0 }
        })
      });
    });

    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [],
          meta: { total: 0 }
        })
      });
    });

    // Navigate to dashboard
    await page.goto('/client');
    
    // Wait for page to load
    await page.waitForLoadState('networkidle');
    
    // Debug: Check what's actually on the page
    const bodyText = await page.locator('body').textContent();
    console.log('Body text:', bodyText);
    
    const url = page.url();
    console.log('Current URL:', url);
    
    const title = await page.title();
    console.log('Page title:', title);
    
    // Check if we're redirected to login
    if (url.includes('login')) {
      console.log('❌ Redirected to login page');
      const loginText = await page.locator('body').textContent();
      console.log('Login page content:', loginText);
    } else {
      console.log('✅ Not redirected to login');
    }
    
    // Check for any error messages
    const errorElements = await page.locator('[class*="error"], [class*="Error"]').count();
    console.log('Error elements found:', errorElements);
    
    // Check for loading states
    const loadingElements = await page.locator('[class*="loading"], [class*="Loading"]').count();
    console.log('Loading elements found:', loadingElements);
    
    // Take a screenshot for debugging
    await page.screenshot({ path: 'debug-dashboard.png' });
    
    // Just check that the page loaded
    await expect(page.locator('body')).toBeVisible();
  });
});
