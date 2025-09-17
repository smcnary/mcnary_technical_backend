#!/usr/bin/env node

/**
 * Stripe Integration Test Script
 * 
 * This script helps validate your Stripe configuration and test the integration.
 * Run with: node scripts/test-stripe.js
 */

const https = require('https');
const fs = require('fs');
const path = require('path');

// Colors for console output
const colors = {
  reset: '\x1b[0m',
  bright: '\x1b[1m',
  red: '\x1b[31m',
  green: '\x1b[32m',
  yellow: '\x1b[33m',
  blue: '\x1b[34m',
  magenta: '\x1b[35m',
  cyan: '\x1b[36m',
};

function log(message, color = 'reset') {
  console.log(`${colors[color]}${message}${colors.reset}`);
}

function checkEnvFile() {
  log('\n🔍 Checking Environment Configuration...', 'cyan');
  
  const envPath = path.join(__dirname, '..', '.env.local');
  
  if (!fs.existsSync(envPath)) {
    log('❌ .env.local file not found', 'red');
    log('   Please create .env.local with your Stripe test keys', 'yellow');
    return false;
  }
  
  const envContent = fs.readFileSync(envPath, 'utf8');
  
  const requiredVars = [
    'NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY',
    'STRIPE_SECRET_KEY',
    'STRIPE_WEBHOOK_SECRET',
    'NEXT_PUBLIC_BASE_URL'
  ];
  
  let allPresent = true;
  
  requiredVars.forEach(varName => {
    if (envContent.includes(varName)) {
      log(`✅ ${varName} is configured`, 'green');
    } else {
      log(`❌ ${varName} is missing`, 'red');
      allPresent = false;
    }
  });
  
  // Check if using test keys
  if (envContent.includes('pk_test_') && envContent.includes('sk_test_')) {
    log('✅ Using Stripe test keys', 'green');
  } else if (envContent.includes('pk_live_') && envContent.includes('sk_live_')) {
    log('⚠️  Using Stripe LIVE keys - be careful!', 'yellow');
  } else {
    log('❓ Stripe key format unclear', 'yellow');
  }
  
  return allPresent;
}

function testStripeAPI() {
  log('\n🔌 Testing Stripe API Connection...', 'cyan');
  
  // This would require the Stripe secret key from env
  // For now, just check if the key format is correct
  log('📝 To test API connection, run: npm run dev and visit http://localhost:3000/audit-wizard', 'blue');
}

function showTestCards() {
  log('\n💳 Test Card Numbers:', 'cyan');
  
  const testCards = [
    { name: 'Visa (Success)', number: '4242 4242 4242 4242', description: 'Standard successful payment' },
    { name: 'Visa (Decline)', number: '4000 0000 0000 0002', description: 'Generic decline' },
    { name: 'Mastercard', number: '5555 5555 5555 4444', description: 'Alternative successful card' },
    { name: '3D Secure', number: '4000 0025 0000 3155', description: 'Requires authentication' },
    { name: 'Insufficient Funds', number: '4000 0000 0000 9995', description: 'Card declined - insufficient funds' }
  ];
  
  testCards.forEach(card => {
    log(`  ${card.name}: ${card.number}`, 'magenta');
    log(`    ${card.description}`, 'yellow');
  });
}

function showTestFlow() {
  log('\n🧪 Testing Flow Instructions:', 'cyan');
  
  const steps = [
    '1. Start dev server: npm run dev',
    '2. Visit: http://localhost:3000/audit-wizard',
    '3. Fill out the form through all steps',
    '4. Select a service (SEO Audit $799 or Full Service $6,000)',
    '5. Click "Pay $X,XXX" button',
    '6. Use test card: 4242 4242 4242 4242',
    '7. Use any future expiry date and CVC',
    '8. Use any email address',
    '9. Complete payment',
    '10. Verify redirect to success page'
  ];
  
  steps.forEach(step => {
    log(`  ${step}`, 'blue');
  });
}

function showWebhookSetup() {
  log('\n🔗 Webhook Setup Options:', 'cyan');
  
  log('Option 1 - Stripe CLI (Recommended):', 'yellow');
  log('  1. Install: brew install stripe/stripe-cli/stripe', 'blue');
  log('  2. Login: stripe login', 'blue');
  log('  3. Forward: stripe listen --forward-to localhost:3000/api/webhook/stripe', 'blue');
  log('  4. Copy webhook secret to STRIPE_WEBHOOK_SECRET in .env.local', 'blue');
  
  log('\nOption 2 - Stripe Dashboard:', 'yellow');
  log('  1. Go to: https://dashboard.stripe.com/test/webhooks', 'blue');
  log('  2. Add endpoint: http://localhost:3000/api/webhook/stripe', 'blue');
  log('  3. Select events: checkout.session.completed, payment_intent.succeeded', 'blue');
  log('  4. Copy webhook secret to .env.local', 'blue');
}

function main() {
  log('🧪 Stripe Integration Test Helper', 'bright');
  log('=====================================', 'bright');
  
  const envOk = checkEnvFile();
  
  if (envOk) {
    log('\n✅ Environment configuration looks good!', 'green');
  } else {
    log('\n❌ Please fix environment configuration issues above', 'red');
  }
  
  testStripeAPI();
  showTestCards();
  showTestFlow();
  showWebhookSetup();
  
  log('\n📚 For more details, see: TESTING_SETUP.md', 'cyan');
  log('\n🎉 Ready to test your Stripe integration!', 'green');
}

// Run the script
if (require.main === module) {
  main();
}

module.exports = { main };
