'use client'

import Header from '../../components/Header';

export default function ServicesPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header onOpenLogin={() => {}} />

      <main className="flex-1">
        <div className="max-w-6xl mx-auto px-6 py-12">
          <h2 className="section-title">Our Services</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div className="card text-center">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Local SEO</h3>
              <p className="text-gray-600">Optimize your law firm for local search results</p>
            </div>
            <div className="card text-center">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Content & AEO</h3>
              <p className="text-gray-600">Create content that answers user questions</p>
            </div>
            <div className="card text-center">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">GBP & Reviews</h3>
              <p className="text-gray-600">Manage your Google Business Profile and reviews</p>
            </div>
            <div className="card text-center">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Technical SEO</h3>
              <p className="text-gray-600">Improve website performance and technical aspects</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
