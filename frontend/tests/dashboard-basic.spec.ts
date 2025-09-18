import { test, expect } from '@playwright/test';

test.describe('Dashboard Basic Functionality', () => {
  test.beforeEach(async ({ page }) => {
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
  });

  test('should load dashboard page without errors', async ({ page }) => {
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
    
    // Check that the page loaded without errors
    await expect(page.locator('body')).toBeVisible();
    
    // Check for basic dashboard elements
    await expect(page.locator('main').first()).toBeVisible();
    
    // Check that we're not on the login page
    await expect(page).not.toHaveURL(/.*login.*/);
    
    console.log('✅ Dashboard loaded successfully');
  });

  test('should display sidebar navigation', async ({ page }) => {
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
    
    // Check for sidebar
    await expect(page.locator('aside')).toBeVisible({ timeout: 15000 });
    
    // Check for navigation links
    await expect(page.locator('nav')).toBeVisible();
    
    console.log('✅ Sidebar navigation displayed successfully');
  });
});
