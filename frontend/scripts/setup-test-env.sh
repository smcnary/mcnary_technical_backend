#!/bin/bash

# Stripe Test Environment Setup Script
# This script helps set up the test environment for Stripe integration

echo "🧪 Setting up Stripe Test Environment..."
echo "========================================"

# Check if .env.local exists
if [ ! -f ".env.local" ]; then
    echo "📝 Creating .env.local file..."
    cp env.example .env.local
    echo "✅ Created .env.local from env.example"
    echo ""
    echo "⚠️  IMPORTANT: You need to add your Stripe test keys to .env.local"
    echo "   Get them from: https://dashboard.stripe.com/test/apikeys"
    echo ""
else
    echo "✅ .env.local already exists"
fi

# Check for Stripe keys
echo "🔍 Checking for Stripe keys in .env.local..."

if grep -q "pk_test_" .env.local 2>/dev/null; then
    echo "✅ Stripe test publishable key found"
else
    echo "❌ Stripe test publishable key not found"
    echo "   Add: NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=pk_test_..."
fi

if grep -q "sk_test_" .env.local 2>/dev/null; then
    echo "✅ Stripe test secret key found"
else
    echo "❌ Stripe test secret key not found"
    echo "   Add: STRIPE_SECRET_KEY=sk_test_..."
fi

if grep -q "whsec_" .env.local 2>/dev/null; then
    echo "✅ Stripe webhook secret found"
else
    echo "❌ Stripe webhook secret not found"
    echo "   Add: STRIPE_WEBHOOK_SECRET=whsec_..."
    echo "   Get it from Stripe CLI or Dashboard"
fi

echo ""
echo "🔗 Next Steps:"
echo "1. Get your Stripe test keys from: https://dashboard.stripe.com/test/apikeys"
echo "2. Add them to .env.local"
echo "3. Set up webhooks (see TESTING_SETUP.md)"
echo "4. Run: npm run dev"
echo "5. Visit: http://localhost:3000/test-stripe"
echo "6. Test the full flow at: http://localhost:3000/audit-wizard"
echo ""
echo "📚 For detailed instructions, see: TESTING_SETUP.md"
echo ""
echo "🎉 Ready to test your Stripe integration!"
