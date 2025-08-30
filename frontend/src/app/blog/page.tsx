'use client'

import Header from '../../components/common/Header';

export default function BlogPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        <div className="max-w-6xl mx-auto px-6 py-12">
          <h2 className="section-title">Latest Insights</h2>
          <div className="grid md:grid-cols-2 gap-8">
            <div className="card">
                              <h3 className="text-xl font-semibold text-gray-900 mb-3">5 Ways to Improve Your Business&apos;s Local SEO</h3>
              <p className="text-gray-600 mb-4">Discover proven strategies to dominate local search results...</p>
              <button className="btn-secondary">Read More</button>
            </div>
            <div className="card">
                              <h3 className="text-xl font-semibold text-gray-900 mb-3">The Future of Business Marketing: AI-Powered SEO</h3>
                <p className="text-gray-600 mb-4">How artificial intelligence is transforming business marketing...</p>
              <button className="btn-secondary">Read More</button>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
