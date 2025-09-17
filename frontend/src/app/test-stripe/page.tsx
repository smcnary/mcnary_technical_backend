"use client";
import { useState } from 'react';
import { loadStripe } from '@stripe/stripe-js';

export default function TestStripePage() {
  const [status, setStatus] = useState<string>('');
  const [error, setError] = useState<string>('');

  const testStripeConnection = async () => {
    setStatus('Testing Stripe connection...');
    setError('');

    try {
      const stripe = await loadStripe(process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY!);
      
      if (!stripe) {
        throw new Error('Failed to load Stripe');
      }

      setStatus('✅ Stripe loaded successfully!');
      
      // Test creating a simple checkout session
      const response = await fetch('/api/create-checkout-session', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          serviceType: 'audit',
          price: 799,
          customerEmail: 'test@example.com',
          customerName: 'Test User',
          companyName: 'Test Company',
          website: 'https://test.com',
          industry: 'Technology',
          goals: ['Increase traffic'],
          competitors: 'Test competitor',
          monthlyBudget: '1000',
          notes: 'Test order',
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to create checkout session');
      }

      const { sessionId } = await response.json();
      setStatus(`✅ Test checkout session created: ${sessionId}`);
      
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Unknown error';
      setError(`❌ Error: ${errorMessage}`);
      setStatus('');
    }
  };

  const checkEnvironment = () => {
    setStatus('Checking environment...');
    setError('');

    const publishableKey = process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY;
    
    if (!publishableKey) {
      setError('❌ NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY not found');
      return;
    }

    if (publishableKey.startsWith('pk_test_')) {
      setStatus('✅ Using Stripe test keys');
    } else if (publishableKey.startsWith('pk_live_')) {
      setStatus('⚠️ Using Stripe LIVE keys - be careful!');
    } else {
      setError('❌ Invalid Stripe publishable key format');
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white p-8">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">Stripe Integration Test</h1>
        
        <div className="space-y-6">
          {/* Environment Check */}
          <div className="bg-white/5 rounded-xl p-6 border border-white/10">
            <h2 className="text-xl font-semibold mb-4">Environment Check</h2>
            <button
              onClick={checkEnvironment}
              className="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-lg font-medium transition"
            >
              Check Environment
            </button>
            {status && <p className="mt-2 text-green-400">{status}</p>}
            {error && <p className="mt-2 text-red-400">{error}</p>}
          </div>

          {/* Stripe Connection Test */}
          <div className="bg-white/5 rounded-xl p-6 border border-white/10">
            <h2 className="text-xl font-semibold mb-4">Stripe Connection Test</h2>
            <button
              onClick={testStripeConnection}
              className="bg-green-600 hover:bg-green-500 px-4 py-2 rounded-lg font-medium transition"
            >
              Test Stripe Connection
            </button>
            {status && <p className="mt-2 text-green-400">{status}</p>}
            {error && <p className="mt-2 text-red-400">{error}</p>}
          </div>

          {/* Test Cards */}
          <div className="bg-white/5 rounded-xl p-6 border border-white/10">
            <h2 className="text-xl font-semibold mb-4">Test Cards</h2>
            <div className="space-y-2 text-sm">
              <div className="flex justify-between">
                <span className="text-white/80">Success:</span>
                <span className="font-mono">4242 4242 4242 4242</span>
              </div>
              <div className="flex justify-between">
                <span className="text-white/80">Decline:</span>
                <span className="font-mono">4000 0000 0000 0002</span>
              </div>
              <div className="flex justify-between">
                <span className="text-white/80">3D Secure:</span>
                <span className="font-mono">4000 0025 0000 3155</span>
              </div>
            </div>
          </div>

          {/* Instructions */}
          <div className="bg-blue-500/10 rounded-xl p-6 border border-blue-500/20">
            <h2 className="text-xl font-semibold mb-4 text-blue-200">Next Steps</h2>
            <ol className="space-y-2 text-blue-200/80">
              <li>1. Make sure your .env.local file has test Stripe keys</li>
              <li>2. Run the environment check above</li>
              <li>3. Test the Stripe connection</li>
              <li>4. Visit the audit wizard to test the full flow</li>
              <li>5. Use the test cards provided above</li>
            </ol>
          </div>

          {/* Links */}
          <div className="bg-white/5 rounded-xl p-6 border border-white/10">
            <h2 className="text-xl font-semibold mb-4">Useful Links</h2>
            <div className="space-y-2">
              <a 
                href="/audit-wizard" 
                className="block text-blue-400 hover:text-blue-300 underline"
              >
                → Go to Audit Wizard
              </a>
              <a 
                href="https://dashboard.stripe.com/test/apikeys" 
                target="_blank"
                rel="noopener noreferrer"
                className="block text-blue-400 hover:text-blue-300 underline"
              >
                → Stripe Test Dashboard
              </a>
              <a 
                href="https://stripe.com/docs/testing#cards" 
                target="_blank"
                rel="noopener noreferrer"
                className="block text-blue-400 hover:text-blue-300 underline"
              >
                → Stripe Test Documentation
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
