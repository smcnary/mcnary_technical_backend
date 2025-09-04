# Audit Intake Validation System

This document explains how to use the new validation system that checks for existing client associations before audit intake submission.

## Overview

The validation system provides multiple ways to check if an audit intake request conflicts with existing clients:

1. **Automatic validation** - Custom validator that runs during entity validation
2. **Service-based validation** - Service class for programmatic validation
3. **API endpoints** - REST endpoints for frontend validation before submission

## How It Works

### Email Validation
- Checks if the `contact_email` field matches an existing client's email
- Prevents duplicate client creation through audit intake

### Website Validation  
- Extracts domain from `website_url` field
- Converts domain to slug format (e.g., "example.com" → "example-com")
- Checks if the slug matches an existing client's slug
- Helps identify if the website is already associated with a client

## Usage Examples

### 1. Automatic Validation (Recommended)

The `AuditIntake` entity now automatically validates data during submission:

```php
// This will automatically trigger validation
$auditIntake = new AuditIntake();
$auditIntake->setContactEmail('existing@client.com');
$auditIntake->setWebsiteUrl('https://existingclient.com');

// Validation will fail if conflicts are found
$violations = $validator->validate($auditIntake);
```

### 2. Service-Based Validation

Use the `AuditIntakeValidationService` for programmatic validation:

```php
use App\Service\AuditIntakeValidationService;

class YourController
{
    public function __construct(
        private AuditIntakeValidationService $validationService
    ) {}

    public function someAction(): void
    {
        // Check email only
        $emailResult = $this->validationService->checkEmailExists('test@example.com');
        
        // Check website only  
        $websiteResult = $this->validationService->checkWebsiteExists('https://example.com');
        
        // Comprehensive check
        $validationResults = $this->validationService->validateAuditIntakeData(
            'test@example.com', 
            'https://example.com'
        );
    }
}
```

### 3. API Endpoints for Frontend Validation

Use these endpoints to validate data before form submission:

#### Validate Complete Data
```http
POST /api/v1/audit-intakes/validate
Content-Type: application/json

{
    "contact_email": "test@example.com",
    "website_url": "https://example.com"
}
```

Response:
```json
{
    "valid": false,
    "validation_results": {
        "email_check": {
            "exists": true,
            "client_id": "uuid",
            "client_name": "Existing Client",
            "client_slug": "existing-client",
            "message": "Email 'test@example.com' is already associated with client 'Existing Client'"
        },
        "website_check": null,
        "has_conflicts": true,
        "conflicts": [...]
    }
}
```

#### Check Email Only
```http
POST /api/v1/audit-intakes/check-email
Content-Type: application/json

{
    "email": "test@example.com"
}
```

#### Check Website Only
```http
POST /api/v1/audit-intakes/check-website
Content-Type: application/json

{
    "website_url": "https://example.com"
}
```

## Frontend Integration

### Real-time Validation
```javascript
// Validate email as user types
async function validateEmail(email) {
    const response = await fetch('/api/v1/audit-intakes/check-email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
    });
    
    const result = await response.json();
    
    if (result.exists) {
        showError(`Email is already associated with client: ${result.result.client_name}`);
    }
}

// Validate website as user types
async function validateWebsite(url) {
    const response = await fetch('/api/v1/audit-intakes/check-website', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ website_url: url })
    });
    
    const result = await response.json();
    
    if (result.exists) {
        showError(`Website appears to be associated with existing client: ${result.result.client_name}`);
    }
}
```

### Form Submission Validation
```javascript
async function validateForm(formData) {
    const response = await fetch('/api/v1/audit-intakes/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            contact_email: formData.get('contact_email'),
            website_url: formData.get('website_url')
        })
    });
    
    const result = await response.json();
    
    if (!result.valid) {
        // Show validation errors
        result.validation_results.conflicts.forEach(conflict => {
            showError(conflict.message);
        });
        return false;
    }
    
    return true;
}
```

## Configuration

### Customizing Error Messages

Edit the constraint class to customize validation messages:

```php
// backend/src/Validator/AuditIntakeClient.php
class AuditIntakeClient extends Constraint
{
    public string $emailMessage = 'Custom email message: {{ email }} is already used by {{ client_name }}';
    public string $websiteMessage = 'Custom website message: {{ website }} belongs to {{ client_name }}';
}
```

### Domain Extraction Logic

The domain extraction logic can be customized in the service:

```php
// backend/src/Service/AuditIntakeValidationService.php
private function extractDomain(string $url): ?string
{
    // Customize domain extraction logic here
    $parsedUrl = parse_url($url);
    if (!$parsedUrl || !isset($parsedUrl['host'])) {
        return null;
    }

    $host = $parsedUrl['host'];
    
    // Remove www. prefix
    if (str_starts_with($host, 'www.')) {
        $host = substr($host, 4);
    }

    // Convert to slug format
    $slug = strtolower(str_replace('.', '-', $host));
    
    return $slug;
}
```

## Testing

Run the validation service tests:

```bash
cd backend
php bin/phpunit tests/Service/AuditIntakeValidationServiceTest.php
```

## Security Considerations

- Validation endpoints are publicly accessible for new users during audit intake
- Validation only checks for conflicts, doesn't expose sensitive client data
- Email and website validation is case-insensitive for better matching
- Domain extraction handles various URL formats safely
- No authentication required for validation endpoints

## Troubleshooting

### Common Issues

1. **Validator not working**: Ensure the constraint is properly registered in `services.yaml`
2. **Domain extraction failing**: Check URL format and ensure `parse_url()` works correctly
3. **API endpoints not accessible**: Verify the routes are properly configured and accessible

### Debug Mode

Enable debug logging to see validation details:

```yaml
# config/packages/dev/monolog.yaml
monolog:
    handlers:
        main:
            level: debug
            channels: ['!event', '!doctrine']
```

## Future Enhancements

- Fuzzy matching for similar client names/domains
- Bulk validation for multiple audit intakes
- Integration with client onboarding workflow
- Advanced domain matching (subdomains, redirects)
