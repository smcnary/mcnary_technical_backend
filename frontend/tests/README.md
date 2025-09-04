# Audit Wizard Testing Setup

This directory contains comprehensive automated testing for the SEO Audit Wizard to speed up development.

## 🚀 Quick Start

### Run Quick Tests (Recommended for Development)
```bash
npm run test:quick
# or
node dev-helper.js test-quick
```

### Run Full Test Suite
```bash
npm run test:audit-wizard
# or
node dev-helper.js test-full
```

### Run Tests with UI (Visual Testing)
```bash
npm run test:ui
# or
node dev-helper.js test-ui
```

### Run Tests in Headed Mode (See Browser)
```bash
npm run test:headed
# or
node dev-helper.js test-headed
```

## 📋 Test Coverage

### Comprehensive Test Suite (`audit-wizard.spec.ts`)
- ✅ Complete wizard flow testing
- ✅ Form validation
- ✅ Step navigation
- ✅ Goal selection
- ✅ Package selection
- ✅ Text visibility verification
- ✅ Error handling
- ✅ Cross-browser testing (Chrome, Firefox, Safari)

### Quick Development Tests (`quick-dev.spec.ts`)
- ✅ Fast iteration testing
- ✅ Multiple scenarios
- ✅ Basic validation

## 🛠️ Development Helper

Use the development helper for quick commands:

```bash
node dev-helper.js --help
```

Available commands:
- `test-quick` - Run quick development tests
- `test-full` - Run full audit wizard test suite
- `test-ui` - Run tests with Playwright UI
- `test-headed` - Run tests in headed mode
- `test-debug` - Run tests in debug mode
- `dev` - Start development server
- `build` - Build for production
- `lint` - Run linting

## 🔧 Test Utilities

### `utils/audit-wizard-utils.ts`
Contains helper functions for:
- Generating test data
- Filling form steps
- Completing the wizard
- Validating form data
- Pre-defined test scenarios

### Test Scenarios
- **Minimal**: Basic form completion
- **Full**: All goals selected
- **Enterprise**: High-budget scenario
- **Invalid**: Validation testing

## 🎯 Development Workflow

1. **Make Changes**: Edit the audit wizard component
2. **Quick Test**: `npm run test:quick`
3. **Full Test**: `npm run test:audit-wizard`
4. **Visual Test**: `npm run test:ui` (if needed)
5. **Debug**: `npm run test:debug` (if issues)

## 📊 Test Reports

After running tests, view detailed reports:
```bash
npx playwright show-report
```

## 🔍 Debugging

### Debug Mode
```bash
npm run test:debug
```
This opens Playwright Inspector for step-by-step debugging.

### Screenshots
Failed tests automatically capture screenshots in `test-results/`.

### Traces
Test traces are saved for failed tests and can be viewed with:
```bash
npx playwright show-trace trace.zip
```

## 🚨 Common Issues

### Tests Failing?
1. Ensure dev server is running: `npm run dev`
2. Check browser compatibility
3. Verify selectors haven't changed
4. Run in debug mode: `npm run test:debug`

### Slow Tests?
- Use `npm run test:quick` for faster iteration
- Run specific test files: `npx playwright test audit-wizard.spec.ts`
- Use headed mode to see what's happening

## 📝 Adding New Tests

1. Add test to `audit-wizard.spec.ts` for comprehensive coverage
2. Add test to `quick-dev.spec.ts` for development speed
3. Update utilities in `utils/audit-wizard-utils.ts` if needed
4. Run tests to verify

## 🎉 Benefits

- **Faster Development**: Catch issues immediately
- **Confidence**: Know changes don't break functionality
- **Regression Prevention**: Automated testing prevents bugs
- **Cross-browser**: Test in Chrome, Firefox, Safari
- **Visual Testing**: See exactly what users see
- **Debugging**: Step-by-step debugging when issues arise
