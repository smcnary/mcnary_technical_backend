# Stripe Testing Environment Setup

This guide will help you set up a complete testing environment for the Stripe payment integration.

## 🔑 **Step 1: Get Stripe Test Keys**

1. **Go to Stripe Dashboard**: https://dashboard.stripe.com/test/apikeys
2. **Toggle to Test Mode**: Make sure you're in "Test mode" (toggle in top left)
3. **Copy Test Keys**:
   - **Publishable key**: `pk_test_...` (starts with pk_test_)
   - **Secret key**: `sk_test_...` (starts with sk_test_)

## 📝 **Step 2: Configure Environment Variables**

Create or update your `.env.local` file with test keys:

```bash
# Stripe Test Configuration
NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=pk_test_your_test_publishable_key_here
STRIPE_SECRET_KEY=sk_test_your_test_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_test_your_test_webhook_secret_here

# Base URL for redirects
NEXT_PUBLIC_BASE_URL=http://localhost:3000

# Test Mode Flag (optional)
NEXT_PUBLIC_TEST_MODE=true
```

## 🔗 **Step 3: Set Up Webhooks for Testing**

### Option A: Stripe CLI (Recommended for Local Testing)

1. **Install Stripe CLI**:
   ```bash
   # macOS
   brew install stripe/stripe-cli/stripe
   
   # Or download from: https://github.com/stripe/stripe-cli/releases
   ```

2. **Login to Stripe**:
   ```bash
   stripe login
   ```

3. **Forward Webhooks**:
   ```bash
   stripe listen --forward-to localhost:3000/api/webhook/stripe
   ```
   
   This will give you a webhook signing secret starting with `whsec_`

4. **Update your `.env.local`** with the webhook secret from step 3

### Option B: Test Webhook Endpoint

1. **Go to Stripe Dashboard**: https://dashboard.stripe.com/test/webhooks
2. **Add endpoint**: `http://localhost:3000/api/webhook/stripe`
3. **Select events**:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
4. **Copy webhook signing secret** and add to `.env.local`

## 💳 **Step 4: Test Cards**

Use these test card numbers for testing:

### ✅ **Successful Payments**
- **Visa**: `4242 4242 4242 4242`
- **Mastercard**: `5555 5555 5555 4444`
- **American Express**: `3782 822463 10005`

### ❌ **Declined Payments**
- **Generic decline**: `4000 0000 0000 0002`
- **Insufficient funds**: `4000 0000 0000 9995`
- **Lost card**: `4000 0000 0000 9987`

### 🔐 **3D Secure Authentication**
- **Requires authentication**: `4000 0025 0000 3155`
- **Authentication fails**: `4000 0000 0000 3220`

### 📧 **Test Customer Info**
- **Email**: Any valid email format (e.g., `test@example.com`)
- **Expiry**: Any future date (e.g., `12/25`)
- **CVC**: Any 3-digit number (e.g., `123`)

## 🧪 **Step 5: Test the Payment Flow**

1. **Start the development server**:
   ```bash
   npm run dev
   ```

2. **Navigate to**: `http://localhost:3000/audit-wizard`

3. **Fill out the form**:
   - Complete all steps through "Choose Service"
   - Select either "SEO Audit ($799)" or "Full SEO Service ($6,000)"

4. **Test Payment**:
   - Click "Pay $X,XXX" button
   - Use test card `4242 4242 4242 4242`
   - Use any future expiry date and CVC
   - Use any email address

5. **Verify Success**:
   - Should redirect back to success page
   - Check browser console for any errors
   - Check Stripe Dashboard for successful payment

## 🔍 **Step 6: Monitor and Debug**

### Browser Console
Check for any JavaScript errors or API failures.

### Stripe Dashboard
- **Payments**: https://dashboard.stripe.com/test/payments
- **Events**: https://dashboard.stripe.com/test/events
- **Logs**: Check for any webhook failures

### Local Logs
If using Stripe CLI, you'll see webhook events in your terminal.

## 🚨 **Common Issues & Solutions**

### Issue: "Stripe failed to load"
**Solution**: Check that `NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY` is set correctly

### Issue: "Invalid signature" in webhook
**Solution**: Ensure `STRIPE_WEBHOOK_SECRET` matches the one from Stripe CLI or dashboard

### Issue: Payment succeeds but doesn't redirect
**Solution**: Check `NEXT_PUBLIC_BASE_URL` is set to `http://localhost:3000`

### Issue: "Missing required fields" error
**Solution**: Ensure all form fields are filled out before reaching checkout

## 📊 **Testing Checklist**

- [ ] Test keys configured in `.env.local`
- [ ] Webhook endpoint set up and working
- [ ] SEO Audit payment ($799) works
- [ ] Full SEO Service payment ($6,000) works
- [ ] Successful payment redirects to success page
- [ ] Failed payment shows error message
- [ ] Webhook receives `checkout.session.completed` event
- [ ] All form validation works correctly
- [ ] Service selection works properly

## 🔄 **Switching to Live Mode**

When ready for production:

1. **Replace test keys** with live keys from Stripe Dashboard
2. **Update webhook URL** to your production domain
3. **Test with real payment methods** (start with small amounts)
4. **Remove `NEXT_PUBLIC_TEST_MODE=true`** from environment

## 📞 **Need Help?**

- **Stripe Documentation**: https://stripe.com/docs/testing
- **Stripe CLI Documentation**: https://stripe.com/docs/stripe-cli
- **Test Card Numbers**: https://stripe.com/docs/testing#cards
