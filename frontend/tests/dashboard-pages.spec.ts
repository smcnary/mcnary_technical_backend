import { test, expect } from '@playwright/test';

test.describe('Dashboard Pages Integration', () => {
  test.beforeEach(async ({ page }) => {
    // Mock authentication by setting localStorage
    await page.goto('/login');
    await page.evaluate(() => {
      localStorage.setItem('auth_token', 'mock-jwt-token-' + Date.now());
    });
  });

  // Helper function to mock API responses
  const mockApiResponses = async (page: any) => {
    // Mock login API
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
            status: 'active',
            clientId: 'mock-client-id'
          }
        })
      });
    });

    // Mock user profile greeting API
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

    // Mock leads API
    await page.route('**/api/v1/leads**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: '1',
              fullName: 'John Doe',
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

    // Mock campaigns API
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

    // Mock case studies API
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

    // Mock clients API
    await page.route('**/api/v1/clients**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: '1',
              name: 'Test Client',
              email: 'client@example.com',
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

    // Mock packages API
    await page.route('**/api/v1/packages**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: '1',
              name: 'Test Package',
              price: 2500,
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

    // Mock users API
    await page.route('**/api/v1/users**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: '1',
              email: 'user@example.com',
              name: 'Test User',
              roles: ['ROLE_CLIENT_USER'],
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
  };

  test.describe('Client Dashboard Pages', () => {
    test('should load client dashboard with API data', async ({ page }) => {
      await mockApiResponses(page);

      // Perform login
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');

      // Wait for redirect to dashboard
      await page.waitForURL('/client');
      
      // Verify dashboard elements are present
      await expect(page.locator('h1')).toContainText('Dashboard');
      
      // Verify API data is displayed
      await expect(page.locator('text=1 total leads')).toBeVisible();
      await expect(page.locator('text=1 total campaigns')).toBeVisible();
    });

    test('should load leads page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login and navigate to leads page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/leads');
      
      // Verify leads page loads with API data
      await expect(page.locator('h1')).toContainText('Leads');
      await expect(page.locator('text=John Doe')).toBeVisible();
      await expect(page.locator('text=john@example.com')).toBeVisible();
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total leads count
    });

    test('should load campaigns page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login and navigate to campaigns page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/campaigns');
      
      // Verify campaigns page loads with API data
      await expect(page.locator('h1')).toContainText('Campaigns');
      await expect(page.locator('text=Test Campaign')).toBeVisible();
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total campaigns count
    });

    test('should load cases page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login and navigate to cases page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/cases');
      
      // Verify cases page loads with API data
      await expect(page.locator('h1')).toContainText('Cases');
      await expect(page.locator('text=Test Case Study')).toBeVisible();
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total cases count
    });

    test('should load settings page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login and navigate to settings page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/settings');
      
      // Verify settings page loads
      await expect(page.locator('h1')).toContainText('Settings');
      
      // Verify form elements are present
      await expect(page.locator('input[id="name"]')).toBeVisible();
      await expect(page.locator('input[id="website"]')).toBeVisible();
      await expect(page.locator('input[id="phone"]')).toBeVisible();
    });

    test('should load billing page with mock data', async ({ page }) => {
      await mockApiResponses(page);

      // Login and navigate to billing page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/billing');
      
      // Verify billing page loads
      await expect(page.locator('h1')).toContainText('Billing');
      
      // Verify billing elements are present
      await expect(page.locator('text=Invoice History')).toBeVisible();
      await expect(page.locator('text=Current Subscriptions')).toBeVisible();
    });

    test('should load notifications page with mock data', async ({ page }) => {
      await mockApiResponses(page);

      // Login and navigate to notifications page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/notifications');
      
      // Verify notifications page loads
      await expect(page.locator('h1')).toContainText('Notifications');
      
      // Verify notification elements are present
      await expect(page.locator('text=Audit Completed')).toBeVisible();
      await expect(page.locator('text=New Lead Received')).toBeVisible();
    });
  });

  test.describe('Admin Dashboard Pages', () => {
    test.beforeEach(async ({ page }) => {
      // Mock admin user authentication
      await page.route('**/api/auth/login', async (route) => {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            token: 'mock-jwt-token-' + Date.now(),
            user: {
              id: 'mock-admin-id',
              email: 'admin@example.com',
              name: 'Admin User',
              roles: ['ROLE_ADMIN'],
              status: 'active'
            }
          })
        });
      });
    });

    test('should load admin dashboard with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login as admin
      await page.fill('input[id="email"]', 'admin@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/admin');
      
      // Verify admin dashboard loads
      await expect(page.locator('h1')).toContainText('Admin Dashboard');
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total users count
      await expect(page.locator('text=1').nth(1)).toBeVisible(); // Total clients count
      await expect(page.locator('text=1').nth(2)).toBeVisible(); // Total packages count
    });

    test('should load admin users page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login as admin and navigate to users page
      await page.fill('input[id="email"]', 'admin@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/admin/users');
      
      // Verify admin users page loads
      await expect(page.locator('h1')).toContainText('Users');
      
      // Verify user data is displayed
      await expect(page.locator('text=Test User')).toBeVisible();
      await expect(page.locator('text=user@example.com')).toBeVisible();
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total users count
    });

    test('should load admin clients page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login as admin and navigate to clients page
      await page.fill('input[id="email"]', 'admin@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/admin/clients');
      
      // Verify admin clients page loads
      await expect(page.locator('h1')).toContainText('Clients');
      
      // Verify client data is displayed
      await expect(page.locator('text=Test Client')).toBeVisible();
      await expect(page.locator('text=client@example.com')).toBeVisible();
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total clients count
    });

    test('should load admin packages page with API integration', async ({ page }) => {
      await mockApiResponses(page);

      // Login as admin and navigate to packages page
      await page.fill('input[id="email"]', 'admin@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/admin/packages');
      
      // Verify admin packages page loads
      await expect(page.locator('h1')).toContainText('Packages');
      
      // Verify package data is displayed
      await expect(page.locator('text=Test Package')).toBeVisible();
      
      // Verify stats cards show data
      await expect(page.locator('text=1').first()).toBeVisible(); // Total packages count
    });

    test('should deny access to admin pages for non-admin users', async ({ page }) => {
      await mockApiResponses(page);

      // Login as regular user
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      // Try to access admin page
      await page.goto('/admin');
      
      // Verify access denied message
      await expect(page.locator('text=Access Denied')).toBeVisible();
      await expect(page.locator('text=Admin access required')).toBeVisible();
    });
  });

  test.describe('API Error Handling', () => {
    test('should handle API errors gracefully on leads page', async ({ page }) => {
      // Mock API error
      await page.route('**/api/v1/leads**', async (route) => {
        await route.fulfill({
          status: 500,
          contentType: 'application/json',
          body: JSON.stringify({
            error: 'Internal Server Error'
          })
        });
      });

      // Mock other APIs successfully
      await page.route('**/api/auth/login', async (route) => {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            token: 'mock-jwt-token',
            user: {
              id: 'mock-user-id',
              email: 'test@example.com',
              name: 'Test User',
              roles: ['ROLE_CLIENT_USER'],
              status: 'active',
              clientId: 'mock-client-id'
            }
          })
        });
      });

      await page.route('**/api/v1/user-profile/greeting', async (route) => {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            greeting: 'Hello',
            user: { name: 'Test' }
          })
        });
      });

      // Login and navigate to leads page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/leads');
      
      // Verify error handling
      await expect(page.locator('text=Loading leads...')).toBeVisible();
      // The error should be displayed after loading
      await page.waitForTimeout(2000);
      await expect(page.locator('text=Failed to load leads')).toBeVisible();
    });

    test('should handle loading states correctly', async ({ page }) => {
      // Mock slow API response
      await page.route('**/api/v1/leads**', async (route) => {
        await new Promise(resolve => setTimeout(resolve, 2000));
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            data: [],
            meta: { total: 0 }
          })
        });
      });

      // Mock other APIs
      await page.route('**/api/auth/login', async (route) => {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            token: 'mock-jwt-token',
            user: {
              id: 'mock-user-id',
              email: 'test@example.com',
              name: 'Test User',
              roles: ['ROLE_CLIENT_USER'],
              status: 'active',
              clientId: 'mock-client-id'
            }
          })
        });
      });

      await page.route('**/api/v1/user-profile/greeting', async (route) => {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            greeting: 'Hello',
            user: { name: 'Test' }
          })
        });
      });

      // Login and navigate to leads page
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');
      
      await page.goto('/client/leads');
      
      // Verify loading state is shown
      await expect(page.locator('text=Loading leads...')).toBeVisible();
      
      // Wait for loading to complete
      await page.waitForTimeout(3000);
      await expect(page.locator('text=Loading leads...')).not.toBeVisible();
    });
  });

  test.describe('Navigation and Routing', () => {
    test('should navigate between dashboard pages correctly', async ({ page }) => {
      await mockApiResponses(page);

      // Login
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');

      // Test navigation to different pages
      const pages = [
        { path: '/client', title: 'Dashboard' },
        { path: '/client/leads', title: 'Leads' },
        { path: '/client/campaigns', title: 'Campaigns' },
        { path: '/client/cases', title: 'Cases' },
        { path: '/client/settings', title: 'Settings' },
        { path: '/client/billing', title: 'Billing' },
        { path: '/notifications', title: 'Notifications' }
      ];

      for (const pageInfo of pages) {
        await page.goto(pageInfo.path);
        await expect(page.locator('h1')).toContainText(pageInfo.title);
      }
    });

    test('should maintain authentication across page navigation', async ({ page }) => {
      await mockApiResponses(page);

      // Login
      await page.fill('input[id="email"]', 'test@example.com');
      await page.fill('input[id="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('/client');

      // Navigate to different pages and verify we stay authenticated
      await page.goto('/client/leads');
      await expect(page.locator('h1')).toContainText('Leads');
      
      await page.goto('/client/campaigns');
      await expect(page.locator('h1')).toContainText('Campaigns');
      
      await page.goto('/client/settings');
      await expect(page.locator('h1')).toContainText('Settings');
    });
  });
});
