'use client'

import Header from '../../components/Header';

export default function AboutPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header onOpenLogin={() => {}} />

      <main className="flex-1">
        <div className="max-w-6xl mx-auto px-6 py-12">
          <h2 className="section-title">About CounselRank.legal</h2>
          <p className="section-subtitle">Your trusted partner for legal SEO that delivers results</p>
          <div className="grid md:grid-cols-2 gap-8">
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Our Mission</h3>
              <p className="text-gray-600">To help law firms dominate search results and attract qualified clients through data-driven SEO strategies.</p>
            </div>
            <div className="card">
              <h3 className="text-xl font-semibold text-gray-900 mb-3">Why Choose Us</h3>
              <ul className="space-y-2 text-gray-600">
                <li>Specialized in legal industry SEO</li>
                <li>Proven track record of results</li>
                <li>Local expertise for geographic markets</li>
              </ul>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
