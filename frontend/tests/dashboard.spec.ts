import { test, expect } from '@playwright/test';

test.describe('Client Dashboard', () => {
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

  test('should load dashboard after login', async ({ page }) => {
    // Mock successful login response
    await page.route('**/api/auth/login', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          token: 'mock-jwt-token-' + Date.now(),
          user: {
            id: 'mock-user-id',
            email: 'test@example.com',
            name: 'Test User',
            roles: ['ROLE_CLIENT_USER'],
            status: 'active'
          }
        })
      });
    });

    // Mock dashboard API responses
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
          data: [
            {
              id: '1',
              name: 'John Doe',
              email: 'john@example.com',
              phone: '555-0123',
              status: 'new',
              createdAt: '2025-01-01T00:00:00Z'
            }
          ],
          meta: {
            total: 1,
            page: 1,
            per_page: 10
          }
        })
      });
    });

    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: '1',
              name: 'Test Campaign',
              status: 'active',
              createdAt: '2025-01-01T00:00:00Z'
            }
          ],
          meta: {
            total: 1,
            page: 1,
            per_page: 10
          }
        })
      });
    });

    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: '1',
              title: 'Test Case Study',
              summary: 'A test case study',
              isActive: true,
              createdAt: '2025-01-01T00:00:00Z'
            }
          ],
          meta: {
            total: 1,
            page: 1,
            per_page: 20
          }
        })
      });
    });

    // Perform login
    await page.fill('input[id="email"]', 'test@example.com');
    await page.fill('input[id="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('/client');
    
    // Verify dashboard elements are present
    await expect(page.locator('h1')).toContainText('Dashboard');
  });

  test('should display user greeting', async ({ page }) => {
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

    // Mock other dashboard APIs
    await page.route('**/api/v1/leads**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });
    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });
    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });

    await page.goto('/client');
    
    // Wait for the page to load completely
    await page.waitForLoadState('networkidle');
    
    // Check for greeting text - be more flexible with the greeting
    await expect(page.locator('text=Test User')).toBeVisible({ timeout: 10000 });
  });

  test('should show navigation sidebar', async ({ page }) => {
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
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });
    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });
    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });

    await page.goto('/client');
    
    // Wait for the page to load completely
    await page.waitForLoadState('networkidle');
    
    // Check for sidebar navigation elements - use more specific selectors
    // First check if the sidebar container exists
    await expect(page.locator('aside')).toBeVisible({ timeout: 10000 });
    
    // Then check for specific navigation links
    await expect(page.locator('nav a[href="/client"]').first()).toBeVisible({ timeout: 10000 });
    await expect(page.locator('nav a[href="/user-preferences"]')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('nav a[href="/client/billing"]')).toBeVisible({ timeout: 10000 });
  });

  test('should handle logout functionality', async ({ page }) => {
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
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });
    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });
    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [], meta: { total: 0 } }) });
    });

    // Mock logout API
    await page.route('**/api/auth/logout', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ success: true, message: 'Logged out successfully' })
      });
    });

    await page.goto('/client');
    
    // Click logout button
    await page.click('text=Logout');
    
    // Should redirect to login page
    await page.waitForURL('/login');
    await expect(page.locator('h1')).toContainText('Welcome back');
  });

  test('should redirect to login if not authenticated', async ({ page }) => {
    // Clear any existing auth token
    await page.evaluate(() => {
      localStorage.removeItem('auth_token');
    });

    // Try to access dashboard
    await page.goto('/client');
    
    // Should redirect to login
    await page.waitForURL('/login');
    await expect(page.locator('h1')).toContainText('Welcome back');
  });

  test('should display dashboard metrics', async ({ page }) => {
    // Mock API responses with data
    await page.route('**/api/v1/user-profile/greeting', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          greeting: 'Good morning, Test User!',
          user: { name: 'Test User', email: 'test@example.com' }
        })
      });
    });

    await page.route('**/api/v1/leads**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            { id: '1', name: 'Lead 1', status: 'new', createdAt: '2025-01-01T00:00:00Z' },
            { id: '2', name: 'Lead 2', status: 'contacted', createdAt: '2025-01-02T00:00:00Z' }
          ],
          meta: { total: 2, page: 1, per_page: 10 }
        })
      });
    });

    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            { id: '1', name: 'Campaign 1', status: 'active', createdAt: '2025-01-01T00:00:00Z' }
          ],
          meta: { total: 1, page: 1, per_page: 10 }
        })
      });
    });

    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            { id: '1', title: 'Case Study 1', isActive: true, createdAt: '2025-01-01T00:00:00Z' }
          ],
          meta: { total: 1, page: 1, per_page: 20 }
        })
      });
    });

    await page.goto('/client');
    
    // Wait for the page to load completely
    await page.waitForLoadState('networkidle');
    
    // Check for dashboard metrics/cards - use more specific selectors
    await expect(page.locator('p:has-text("Total Leads")').first()).toBeVisible({ timeout: 10000 });
    await expect(page.locator('p:has-text("Active Campaigns")').first()).toBeVisible({ timeout: 10000 });
  });
});