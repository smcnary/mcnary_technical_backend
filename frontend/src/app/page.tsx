'use client'

import Header from '../components/Header';
import Footer from '../components/Footer';
import Link from 'next/link';

export default function HomePage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />

      <main className="flex-1">
        {/* Hero Section */}
        <section className="relative bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white py-24 overflow-hidden">
          <div className="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between">
            
            {/* Left */}
            <div className="max-w-xl">
              <span className="text-blue-400 text-sm font-semibold tracking-wide uppercase">AI-First SEO</span>
              <h1 className="mt-4 text-5xl font-extrabold leading-tight">
                <span className="text-white">Tulsa&apos;s</span> <span className="text-blue-500">AI-First SEO Agency</span>
              </h1>
              <p className="mt-6 text-lg text-gray-300">
                We combine the power of <span className="text-white font-semibold">artificial intelligence</span> with proven 
                SEO strategies to put your business ahead of the competition. From Google Maps to organic search, 
                our AI-driven insights help you rank faster and smarter.
              </p>
              <div className="mt-8 flex space-x-4">
                <Link href="/contact" className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
                  Get My AI SEO Audit
                </Link>
                <Link href="/case-studies" className="border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white px-6 py-3 rounded-xl font-semibold">
                  See Case Studies
                </Link>
              </div>
              <ul className="mt-5 text-sm text-gray-400 space-y-1">
                <li>🤖 AI-powered keyword research & content strategy</li>
                <li>⚡ Predictive ranking insights</li>
                <li>✅ Faster results with data-driven automation</li>
              </ul>
            </div>
            
            {/* Right */}
            <div className="mt-12 md:mt-0 md:ml-16 relative">
              <div className="bg-gray-800 p-6 rounded-2xl shadow-xl">
                <div className="w-80 h-64 bg-gradient-to-br from-blue-600 to-purple-700 rounded-xl flex items-center justify-center">
                  <div className="text-center text-white">
                    <svg className="w-16 h-16 mx-auto mb-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 className="text-xl font-semibold mb-2">AI SEO Dashboard</h3>
                    <p className="text-blue-100 text-sm">Real-time analytics & insights</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Trust Logos */}
          <div className="mt-16 flex items-center justify-center space-x-10 opacity-80">
            {/* Google Partner */}
            <div className="flex items-center space-x-2 text-white">
              <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
              </div>
              <span className="text-sm font-medium">Google Partner</span>
            </div>

            {/* AI Powered */}
            <div className="flex items-center space-x-2 text-white">
              <div className="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-600 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
              </div>
              <span className="text-sm font-medium">AI Powered</span>
            </div>

            {/* Clutch */}
            <div className="flex items-center space-x-2 text-white">
              <div className="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"/>
                </svg>
              </div>
              <span className="text-sm font-medium">Clutch</span>
            </div>

            {/* BBB Accredited */}
            <div className="flex items-center space-x-2 text-white">
              <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"/>
                </svg>
              </div>
              <span className="text-sm font-medium">BBB Accredited</span>
            </div>
          </div>
        </section>

        {/* Features Grid */}
        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-6">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything You Need to Dominate Local Search
              </h2>
              <p className="text-xl text-gray-600 max-w-3xl mx-auto">
                From keyword tracking to competitor analysis, we provide the tools and insights to help your business climb the search rankings.
              </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              {/* Feature 1 */}
              <div className="card-hover p-8 text-center">
                <div className="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg className="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Real-Time Rankings</h3>
                <p className="text-gray-600">
                  Track your keyword positions daily with automated monitoring and instant alerts when rankings change.
                </p>
              </div>

              {/* Feature 2 */}
              <div className="card-hover p-8 text-center">
                <div className="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Performance Analytics</h3>
                <p className="text-gray-600">
                  Comprehensive dashboards showing traffic trends, conversion rates, and ROI from your SEO efforts.
                </p>
              </div>

              {/* Feature 3 */}
              <div className="card-hover p-8 text-center">
                <div className="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg className="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Lead Attribution</h3>
                <p className="text-gray-600">
                  See exactly which keywords and content are driving phone calls, form submissions, and consultations.
                </p>
              </div>

              {/* Feature 4 */}
              <div className="card-hover p-8 text-center">
                <div className="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg className="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Competitor Insights</h3>
                <p className="text-gray-600">
                  Monitor your competition&apos;s rankings, content strategies, and identify opportunities to outperform them.
                </p>
              </div>

              {/* Feature 5 */}
              <div className="card-hover p-8 text-center">
                <div className="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg className="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Automated Reporting</h3>
                <p className="text-gray-600">
                  Monthly performance reports automatically generated and sent to stakeholders with actionable insights.
                </p>
              </div>

              {/* Feature 6 */}
              <div className="card-hover p-8 text-center">
                <div className="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg className="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-4">White-Label Solutions</h3>
                <p className="text-gray-600">
                  Customizable dashboards and reports that match your brand for seamless client presentations.
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* CTA Section */}
        <section className="py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
          <div className="max-w-7xl mx-auto px-6 text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">
              Ready to See Your Rankings Soar?
            </h2>
            <p className="text-xl text-blue-100 mb-10 max-w-3xl mx-auto">
              Join hundreds of businesses already using tulsa-seo.com to dominate local search and attract more qualified customers.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link 
                href="/contact"
                className="btn-secondary text-lg px-8 py-4"
              >
                Start Your Journey
              </Link>
              <Link 
                href="/contact"
                className="btn-outline text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-blue-600"
              >
                Talk to an Expert
              </Link>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
}
