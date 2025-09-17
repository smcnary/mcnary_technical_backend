# Stripe Integration Setup

This document explains how to set up Stripe payment processing for the audit wizard.

## Prerequisites

1. Stripe account (sign up at https://stripe.com)
2. Stripe CLI (optional, for webhook testing)

## Environment Variables

Create a `.env.local` file in the frontend directory with the following variables:

```bash
# Stripe Configuration
NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_publishable_key_here
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# Base URL for redirects
NEXT_PUBLIC_BASE_URL=http://localhost:3000
```

## Stripe Dashboard Setup

1. **Get API Keys**:
   - Go to Stripe Dashboard → Developers → API Keys
   - Copy your Publishable Key and Secret Key
   - Use test keys for development

2. **Set up Webhook**:
   - Go to Stripe Dashboard → Developers → Webhooks
   - Add endpoint: `https://yourdomain.com/api/webhook/stripe`
   - Select events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`
   - Copy the webhook signing secret

## Testing

### Test Cards
Use these test card numbers:
- Success: `4242 4242 4242 4242`
- Decline: `4000 0000 0000 0002`
- Requires 3D Secure: `4000 0025 0000 3155`

### Test Flow
1. Navigate to `/audit-wizard`
2. Fill out the form steps
3. Select a tier on the "Pick Your Tier" step
4. Click "Continue" to go to the "Payment" step
5. Click "Pay $X,XXX" to initiate Stripe checkout
6. Use test card details to complete payment
7. Verify redirect to success page

## Production Deployment

1. **Switch to Live Keys**:
   - Replace test keys with live keys in production environment
   - Update webhook URL to production domain
   - Test with real payment methods

2. **Security Considerations**:
   - Never expose secret keys in client-side code
   - Validate webhook signatures
   - Use HTTPS in production
   - Implement proper error handling

## API Endpoints

- `POST /api/create-checkout-session` - Creates Stripe checkout session
- `POST /api/webhook/stripe` - Handles Stripe webhook events

## Features Implemented

- ✅ Stripe checkout integration
- ✅ Payment step in audit wizard
- ✅ Order summary display
- ✅ Success/cancel handling
- ✅ Webhook processing
- ✅ Error handling
- ✅ Test mode support

## Next Steps

1. Implement database storage for orders
2. Add email notifications
3. Create admin dashboard for order management
4. Add refund handling
5. Implement subscription billing (if needed)
