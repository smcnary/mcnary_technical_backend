'use client'

import Header from '../../components/Header';

export default function FaqsPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <div className="max-w-4xl mx-auto px-6 py-12">
          <h2 className="section-title">Frequently Asked Questions</h2>
          <div className="space-y-6">
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">How long does a typical case take?</h3>
              <p className="text-gray-600">Case duration varies depending on complexity and type. Simple cases may resolve in weeks, while complex litigation can take months or years.</p>
            </div>
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">What are your fees?</h3>
              <p className="text-gray-600">We offer various fee structures including contingency fees, hourly rates, and flat fees depending on the case type.</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
