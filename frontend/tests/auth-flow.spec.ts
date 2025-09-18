import { test, expect } from '@playwright/test';

test.describe('Authentication Flow', () => {
  test('should complete login flow and access dashboard', async ({ page }) => {
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
            status: 'active'
          }
        })
      });
    });

    // Mock dashboard APIs
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
        body: JSON.stringify({ data: [], meta: { total: 0 } })
      });
    });

    await page.route('**/api/v1/campaigns**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: [], meta: { total: 0 } })
      });
    });

    await page.route('**/api/case_studies**', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: [], meta: { total: 0 } })
      });
    });

    // Start at login page
    await page.goto('/login');
    
    // Fill login form
    await page.fill('input[id="email"]', 'test@example.com');
    await page.fill('input[id="password"]', 'password123');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await page.waitForURL('/client');
    
    // Verify we're on the dashboard
    await expect(page.locator('h1')).toContainText('Dashboard');
    
    // Verify localStorage has auth token
    const token = await page.evaluate(() => localStorage.getItem('auth_token'));
    expect(token).toBeTruthy();
  });

  test('should handle login with show password toggle', async ({ page }) => {
    await page.goto('/login');
    
    // Check password field is initially hidden
    const passwordInput = page.locator('input[id="password"]');
    await expect(passwordInput).toHaveAttribute('type', 'password');
    
    // Click show password button
    await page.click('button[aria-label="Show password"]');
    
    // Check password field is now visible
    await expect(passwordInput).toHaveAttribute('type', 'text');
    
    // Click hide password button
    await page.click('button[aria-label="Hide password"]');
    
    // Check password field is hidden again
    await expect(passwordInput).toHaveAttribute('type', 'password');
  });

  test('should handle registration with show password toggle', async ({ page }) => {
    await page.goto('/register');
    
    // Check password fields are initially hidden
    const passwordInput = page.locator('input[name="password"]');
    const confirmPasswordInput = page.locator('input[name="confirmPassword"]');
    
    await expect(passwordInput).toHaveAttribute('type', 'password');
    await expect(confirmPasswordInput).toHaveAttribute('type', 'password');
    
    // Click show password button for password field
    await page.click('button[aria-label="Show password"]');
    await expect(passwordInput).toHaveAttribute('type', 'text');
    
    // Click show password button for confirm password field
    await page.click('button[aria-label="Show password"]').nth(1);
    await expect(confirmPasswordInput).toHaveAttribute('type', 'text');
  });

  test('should redirect to login when accessing protected route', async ({ page }) => {
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

  test('should handle logout and redirect to login', async ({ page }) => {
    // Set auth token
    await page.evaluate(() => {
      localStorage.setItem('auth_token', 'mock-jwt-token');
    });

    // Mock logout API
    await page.route('**/api/auth/logout', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ success: true, message: 'Logged out successfully' })
      });
    });

    // Mock dashboard APIs
    await page.route('**/api/v1/user-profile/greeting', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ greeting: 'Hello', user: { name: 'Test' } })
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
    
    // Click logout button
    await page.click('text=Logout');
    
    // Should redirect to login page
    await page.waitForURL('/login');
    
    // Verify localStorage is cleared
    const token = await page.evaluate(() => localStorage.getItem('auth_token'));
    expect(token).toBeNull();
  });

  test('should handle Client Dashboard link from hero page', async ({ page }) => {
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
            status: 'active'
          }
        })
      });
    });

    // Mock dashboard APIs
    await page.route('**/api/v1/user-profile/greeting', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ greeting: 'Hello', user: { name: 'Test' } })
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

    // Start at home page
    await page.goto('/');
    
    // Click Client Dashboard link
    await page.click('text=Client Dashboard');
    
    // Should redirect to login since not authenticated
    await page.waitForURL('/login');
    
    // Login
    await page.fill('input[id="email"]', 'test@example.com');
    await page.fill('input[id="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Should redirect to dashboard
    await page.waitForURL('/client');
    await expect(page.locator('h1')).toContainText('Dashboard');
  });
});