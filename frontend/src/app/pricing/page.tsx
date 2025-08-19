'use client'

import Header from '../../components/Header';

export default function PricingPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header onOpenLogin={() => {}} />

      <main className="flex-1">
        <div className="max-w-6xl mx-auto px-6 py-12">
          <h2 className="section-title">Pricing Plans</h2>
          <div className="grid md:grid-cols-3 gap-8">
            <div className="card text-center">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Starter</h3>
              <div className="text-4xl font-bold text-gray-900 mb-4">$3,000<span className="text-lg text-gray-500">/month</span></div>
              <ul className="space-y-2 mb-6 text-gray-600">
                <li>Local SEO optimization</li>
                <li>Google Business Profile setup</li>
                <li>Basic content creation</li>
                <li>Monthly reporting</li>
              </ul>
              <button className="btn-primary">Select Plan</button>
            </div>
            <div className="card text-center border-2 border-teal-500 relative">
              <div className="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-teal-500 text-white px-4 py-1 rounded-full text-sm font-medium">Most Popular</div>
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Growth</h3>
              <div className="text-4xl font-bold text-gray-900 mb-4">$6,000<span className="text-lg text-gray-500">/month</span></div>
              <ul className="space-y-2 mb-6 text-gray-600">
                <li>Everything in Starter</li>
                <li>Advanced local SEO</li>
                <li>Content creation (5 articles/month)</li>
                <li>Review management</li>
                <li>Priority support</li>
              </ul>
              <button className="btn-primary">Select Plan</button>
            </div>
            <div className="card text-center">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Premium</h3>
              <div className="text-4xl font-bold text-gray-900 mb-4">$12,000<span className="text-lg text-gray-500">/month</span></div>
              <ul className="space-y-2 mb-6 text-gray-600">
                <li>Everything in Growth</li>
                <li>Technical SEO audit</li>
                <li>Unlimited content creation</li>
                <li>Advanced analytics</li>
                <li>Dedicated account manager</li>
              </ul>
              <button className="btn-primary">Select Plan</button>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
