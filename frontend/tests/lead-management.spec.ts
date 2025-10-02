import { test, expect } from '@playwright/test';

test.describe('Lead Management', () => {
  test.beforeEach(async ({ page }) => {
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

    // Mock API responses for dashboard
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

    // Mock leads API
    await page.route('**/api/v1/leads**', async (route) => {
      if (route.request().method() === 'GET') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            data: [
              {
                id: 'existing-lead-id',
                fullName: 'John Doe',
                email: 'john@example.com',
                phone: '555-0123',
                firm: 'Doe Law Firm',
                website: 'https://doelaw.com',
                city: 'Tulsa',
                state: 'OK',
                zipCode: '74101',
                message: 'Interested in SEO services',
                practiceAreas: ['Personal Injury', 'Criminal Defense'],
                status: 'new_lead',
                statusLabel: 'New Lead',
                createdAt: '2025-01-01T00:00:00Z',
                updatedAt: '2025-01-01T00:00:00Z'
              }
            ],
            pagination: {
              page: 1,
              per_page: 25,
              total: 1,
              pages: 1
            }
          })
        });
      }
    });

    // Mock lead creation API
    await page.route('**/api/leads', async (route) => {
      if (route.request().method() === 'POST') {
        const requestBody = await route.request().postDataJSON();
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({
            id: 'new-lead-id-' + Date.now(),
            fullName: requestBody.fullName,
            email: requestBody.email,
            phone: requestBody.phone || null,
            firm: requestBody.firm || null,
            website: requestBody.website || null,
            city: requestBody.city || null,
            state: requestBody.state || null,
            zipCode: requestBody.zipCode || null,
            message: requestBody.message || null,
            practiceAreas: requestBody.practiceAreas || [],
            status: requestBody.status || 'new_lead',
            statusLabel: 'New Lead',
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
          })
        });
      }
    });

    // Mock lead update API
    await page.route('**/api/v1/leads/*', async (route) => {
      if (route.request().method() === 'PATCH') {
        const requestBody = await route.request().postDataJSON();
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            id: 'existing-lead-id',
            fullName: requestBody.fullName || 'John Doe',
            email: requestBody.email || 'john@example.com',
            phone: requestBody.phone || '555-0123',
            firm: requestBody.firm || 'Doe Law Firm',
            website: requestBody.website || 'https://doelaw.com',
            city: requestBody.city || 'Tulsa',
            state: requestBody.state || 'OK',
            zipCode: requestBody.zipCode || '74101',
            message: requestBody.message || 'Interested in SEO services',
            practiceAreas: requestBody.practiceAreas || ['Personal Injury', 'Criminal Defense'],
            status: requestBody.status || 'new_lead',
            statusLabel: 'New Lead',
            createdAt: '2025-01-01T00:00:00Z',
            updatedAt: new Date().toISOString()
          })
        });
      }
    });
  });

  test('should create a new lead successfully', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Wait for the kanban board to load
    await expect(page.locator('text=Leads Management')).toBeVisible();
    await expect(page.locator('button:has-text("Create Lead")')).toBeVisible();

    // Click Create Lead button
    await page.click('button:has-text("Create Lead")');

    // Wait for the form modal to open
    await expect(page.locator('text=Create New Lead')).toBeVisible();

    // Fill out the form
    await page.fill('input[id="fullName"]', 'Jane Smith');
    await page.fill('input[id="email"]', 'jane.smith@example.com');
    await page.fill('input[id="phone"]', '555-9876');
    await page.fill('input[id="firm"]', 'Smith & Associates');
    await page.fill('input[id="website"]', 'https://smithlaw.com');
    await page.fill('input[id="city"]', 'Oklahoma City');
    await page.fill('input[id="state"]', 'OK');
    await page.fill('input[id="zipCode"]', '73101');
    await page.fill('textarea[id="message"]', 'Looking for comprehensive SEO services for our law firm.');

    // Add practice areas
    await page.fill('input[placeholder="Enter practice area"]', 'Family Law');
    await page.press('input[placeholder="Enter practice area"]', 'Enter');

    // Add another practice area
    await page.fill('input[placeholder="Enter practice area"]', 'Estate Planning');
    await page.press('input[placeholder="Enter practice area"]', 'Enter');

    // Verify practice areas were added
    await expect(page.locator('text=Family Law')).toBeVisible();
    await expect(page.locator('text=Estate Planning')).toBeVisible();

    // Submit the form
    await page.click('button[type="submit"]:has-text("Create Lead")');

    // Wait for success (modal should close)
    await expect(page.locator('text=Create New Lead')).not.toBeVisible();

    // Verify the form was submitted with correct data
    // The mock API will return the created lead data
  });

  test('should validate required fields when creating a lead', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Click Create Lead button
    await page.click('button:has-text("Create Lead")');

    // Wait for the form modal to open
    await expect(page.locator('text=Create New Lead')).toBeVisible();

    // Try to submit without required fields
    await page.click('button[type="submit"]:has-text("Create Lead")');

    // Check for validation errors
    await expect(page.locator('text=Full name is required')).toBeVisible();
    await expect(page.locator('text=Email is required')).toBeVisible();

    // Fill only the email field with invalid format
    await page.fill('input[id="email"]', 'invalid-email');
    await page.click('button[type="submit"]:has-text("Create Lead")');

    // Check for email validation error
    await expect(page.locator('text=Please enter a valid email address')).toBeVisible();

    // Fill valid email
    await page.fill('input[id="email"]', 'test@example.com');
    
    // Fill full name
    await page.fill('input[id="fullName"]', 'Test User');

    // Try invalid website URL
    await page.fill('input[id="website"]', 'invalid-url');
    await page.click('button[type="submit"]:has-text("Create Lead")');

    // Check for website validation error
    await expect(page.locator('text=Please enter a valid URL')).toBeVisible();

    // Fix website URL
    await page.fill('input[id="website"]', 'https://example.com');

    // Now submit should work
    await page.click('button[type="submit"]:has-text("Create Lead")');
    await expect(page.locator('text=Create New Lead')).not.toBeVisible();
  });

  test('should edit an existing lead successfully', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Wait for leads to load
    await expect(page.locator('text=John Doe')).toBeVisible();

    // Click on the existing lead to open details
    await page.click('text=John Doe');

    // Wait for lead details modal
    await expect(page.locator('text=John Doe').first()).toBeVisible();

    // Click Edit Lead button
    await page.click('button:has-text("Edit Lead")');

    // Wait for edit form modal
    await expect(page.locator('text=Edit Lead')).toBeVisible();

    // Update some fields
    await page.fill('input[id="fullName"]', 'John Doe Updated');
    await page.fill('input[id="phone"]', '555-9999');
    await page.fill('input[id="city"]', 'Muskogee');

    // Add a new practice area
    await page.fill('input[placeholder="Enter practice area"]', 'Real Estate');
    await page.click('button:has(svg)'); // Plus button

    // Update the message
    await page.fill('textarea[id="message"]', 'Updated message: Very interested in your SEO services.');

    // Submit the update
    await page.click('button[type="submit"]:has-text("Update Lead")');

    // Wait for modal to close
    await expect(page.locator('text=Edit Lead')).not.toBeVisible();

    // Verify the update was successful
    // The mock API will return the updated lead data
  });

  test('should handle practice area management correctly', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Click Create Lead button
    await page.click('button:has-text("Create Lead")');

    // Wait for the form modal to open
    await expect(page.locator('text=Create New Lead')).toBeVisible();

    // Fill required fields
    await page.fill('input[id="fullName"]', 'Practice Area Test');
    await page.fill('input[id="email"]', 'practice@example.com');

    // Add multiple practice areas
    const practiceAreas = ['Criminal Law', 'Personal Injury', 'Corporate Law'];
    
    for (const area of practiceAreas) {
      await page.fill('input[placeholder="Enter practice area"]', area);
      await page.press('input[placeholder="Enter practice area"]', 'Enter');
      await expect(page.locator(`text=${area}`)).toBeVisible();
    }

    // Try to add duplicate practice area (should not be added)
    await page.fill('input[placeholder="Enter practice area"]', 'Criminal Law');
    await page.press('input[placeholder="Enter practice area"]', 'Enter');
    
    // Should still only have 3 practice areas
    await expect(page.locator('[data-testid="practice-area-badge"]')).toHaveCount(3);

    // Remove a practice area
    await page.click('[data-testid="practice-area-badge"]:has-text("Criminal Law") button'); // Remove button
    await expect(page.locator('text=Criminal Law')).not.toBeVisible();

    // Should now have 2 practice areas
    await expect(page.locator('[data-testid="practice-area-badge"]')).toHaveCount(2);

    // Submit the form
    await page.click('button[type="submit"]:has-text("Create Lead")');
    await expect(page.locator('text=Create New Lead')).not.toBeVisible();
  });

  test('should handle form cancellation correctly', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Click Create Lead button
    await page.click('button:has-text("Create Lead")');

    // Wait for the form modal to open
    await expect(page.locator('text=Create New Lead')).toBeVisible();

    // Fill some data
    await page.fill('input[id="fullName"]', 'Test User');
    await page.fill('input[id="email"]', 'test@example.com');

    // Click Cancel button
    await page.click('button:has-text("Cancel")');

    // Modal should close
    await expect(page.locator('text=Create New Lead')).not.toBeVisible();

    // Open form again and verify it's empty
    await page.click('button:has-text("Create Lead")');
    await expect(page.locator('input[id="fullName"]')).toHaveValue('');
    await expect(page.locator('input[id="email"]')).toHaveValue('');
  });

  test('should display proper status badges and handle status changes', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Wait for leads to load
    await expect(page.locator('text=John Doe')).toBeVisible();

    // Verify status badge is displayed
    await expect(page.locator('text=New Lead')).toBeVisible();

    // Click on the lead to open details
    await page.click('text=John Doe');

    // Wait for lead details modal
    await expect(page.locator('text=John Doe').first()).toBeVisible();

    // Verify status options are available
    await expect(page.locator('button:has-text("Contacted")')).toBeVisible();
    await expect(page.locator('button:has-text("Interview Scheduled")')).toBeVisible();

    // Change status to "Contacted"
    await page.click('button:has-text("Contacted")');

    // Click Save Status button
    await page.click('button:has-text("Save Status")');

    // Verify the status was updated
    await expect(page.locator('text=Contacted')).toBeVisible();
  });

  test('should handle API errors gracefully', async ({ page }) => {
    // Mock API error for lead creation
    await page.route('**/api/leads', async (route) => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 500,
          contentType: 'application/json',
          body: JSON.stringify({ error: 'Internal server error' })
        });
      }
    });

    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Click Create Lead button
    await page.click('button:has-text("Create Lead")');

    // Fill required fields
    await page.fill('input[id="fullName"]', 'Error Test');
    await page.fill('input[id="email"]', 'error@example.com');

    // Submit the form
    await page.click('button[type="submit"]:has-text("Create Lead")');

    // Should show error message
    await expect(page.locator('text=Failed to save lead')).toBeVisible();
    await expect(page.locator('text=Internal server error')).toBeVisible();

    // Modal should remain open
    await expect(page.locator('text=Create New Lead')).toBeVisible();
  });

  test('should handle drag and drop for status changes', async ({ page }) => {
    // Navigate to SEO clients page
    await page.goto('/seo-clients');
    await page.waitForLoadState('networkidle');

    // Wait for leads to load
    await expect(page.locator('text=John Doe')).toBeVisible();

    // Find the lead card and the "Interviews" column
    const leadCard = page.locator('text=John Doe').first();
    const interviewsColumn = page.locator('text=Interviews').first();

    // Drag the lead from "New Leads" to "Interviews" column
    await leadCard.dragTo(interviewsColumn);

    // Wait for the status change to be processed
    await page.waitForTimeout(1000);

    // Verify the lead moved to the interviews column
    await expect(interviewsColumn.locator('..').locator('text=John Doe')).toBeVisible();
  });
});
