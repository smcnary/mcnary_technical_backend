'use client'

import Header from '../../components/Header';

export default function CaseStudiesPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <div className="max-w-4xl mx-auto px-6 py-12">
          <h2 className="section-title">Case Studies</h2>
          <p className="section-subtitle">Explore our successful legal cases and outcomes</p>
          <div className="grid md:grid-cols-2 gap-8">
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Personal Injury Settlement</h3>
              <p className="text-gray-600">Successfully negotiated a $2.5M settlement for a car accident victim</p>
            </div>
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Family Law Resolution</h3>
              <p className="text-gray-600">Helped family reach amicable custody agreement</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
