"use client";
import { useState } from 'react';

export default function TulsaSEOHero() {
  const [isLoading, setIsLoading] = useState(false);

  const handleStripeCheckout = async () => {
    setIsLoading(true);
    
    try {
      // Create checkout session for $799 audit
      const response = await fetch('/api/create-checkout-session', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          serviceType: 'audit',
          price: 799,
          customerEmail: '', // Will be collected in Stripe checkout
          customerName: '',
          companyName: '',
          website: '',
          industry: '',
          goals: [],
          competitors: '',
          monthlyBudget: '',
          notes: '',
        }),
      });

      const { sessionId } = await response.json();
      
      if (sessionId) {
        // Redirect to Stripe checkout
        window.location.href = `https://checkout.stripe.com/pay/${sessionId}`;
      }
    } catch (error) {
      console.error('Error creating checkout session:', error);
      alert('Failed to start checkout. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <section className="relative overflow-hidden bg-gradient-to-b from-[#0c0a17] to-black text-white">
        {/* soft grid + glow */}
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_60%_at_70%_10%,rgba(99,102,241,0.15),transparent_60%),radial-gradient(50%_50%_at_20%_20%,rgba(16,185,129,0.10),transparent_60%)]" />
        
        <div className="mx-auto max-w-7xl px-6 pt-20 pb-16">
          {/* Eyebrow */}
          <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-2 text-sm text-indigo-200">
            <span className="h-2 w-2 rounded-full bg-indigo-400" /> AI‑First SEO Platform
          </div>

          {/* Headline */}
          <h1 className="text-4xl font-bold leading-tight md:text-5xl lg:text-6xl xl:text-7xl mb-6">
            <span className="text-white">Tulsa‑SEO:</span>{" "}
            <span className="text-white">Start with an Audit.</span>{" "}
            <span className="text-indigo-300">Scale with a Plan.</span>
          </h1>
          
          <p className="text-lg md:text-xl text-white/90 max-w-3xl mb-8 leading-relaxed">
            Get a comprehensive SEO audit for just $799. Our AI-powered analysis will identify opportunities and create a roadmap to boost your rankings and drive more qualified traffic.
          </p>

          {/* Wizard preview breadcrumb */}
          <div className="mb-10 grid grid-cols-1 gap-3 sm:grid-cols-5">
            {["Create Account","Business Details","Goals","Pick Tier","Review"].map((label, i) => (
              <div key={label} className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/90 hover:bg-white/10 transition-colors duration-200">
                <span className={`flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold transition-all duration-200 ${
                  i === 0 
                    ? "bg-indigo-600 text-white shadow-lg shadow-indigo-600/25" 
                    : "bg-white/10 text-white/90"
                }`}>
                  {i+1}
                </span>
                <span className="truncate font-medium text-white">{label}</span>
              </div>
            ))}
          </div>

          {/* Controls */}
          <div className="mb-12 flex justify-center">
            <button
              onClick={handleStripeCheckout}
              disabled={isLoading}
              className="inline-flex items-center justify-center gap-3 rounded-xl bg-indigo-600 px-8 py-4 text-lg font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500 hover:shadow-xl hover:shadow-indigo-600/30 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              {isLoading ? (
                <>
                  <svg className="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </>
              ) : (
                <>
                  Get Your $799 SEO Audit <span aria-hidden className="text-xl">→</span>
                </>
              )}
            </button>
          </div>

          {/* Trust bar */}
          <div className="grid gap-6 sm:grid-cols-3">
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 text-center hover:bg-white/10 transition-all duration-200 hover:border-white/20">
              <div className="text-3xl font-bold text-white mb-2">+38%</div>
              <div className="text-sm text-white/90 leading-relaxed">Avg. 90‑day organic uplift</div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 text-center hover:bg-white/10 transition-all duration-200 hover:border-white/20">
              <div className="text-3xl font-bold text-white mb-2">500+</div>
              <div className="text-sm text-white/90 leading-relaxed">Issues detected & prioritized</div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/5 p-6 text-center hover:bg-white/10 transition-all duration-200 hover:border-white/20">
              <div className="text-3xl font-bold text-white mb-2">Autosave</div>
              <div className="text-sm text-white/90 leading-relaxed">Progress kept while you explore</div>
            </div>
          </div>
        </div>

        {/* bottom illustration divider */}
        <div className="pointer-events-none h-32 w-full bg-gradient-to-b from-transparent to-black" />
      </section>
    </>
  );
}
