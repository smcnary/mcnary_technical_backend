'use client'

import { useState } from 'react';
import Header from '../components/Header';
import ClientLoginModal from '../components/ClientLoginModal';

export default function HomePage() {
  const [isLoginModalOpen, setIsLoginModalOpen] = useState(false);

  return (
    <div className="min-h-screen flex flex-col">
      <Header onOpenLogin={() => setIsLoginModalOpen(true)} />

      <main className="flex-1">
        <div>
          <div className="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-20">
            <div className="max-w-4xl mx-auto px-6 text-center">
              <h1 className="text-5xl font-bold mb-6">Legal SEO that wins cases</h1>
              <p className="text-xl text-gray-200 mb-8">We help law firms dominate Google search with local + AI-first SEO.</p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <button className="btn-primary text-lg px-8 py-4">Book Demo</button>
                <a 
                  href="/pricing"
                  className="text-gray-200 hover:text-white transition-colors duration-200 font-medium"
                >
                  See Pricing
                </a>
              </div>
              <div className="mb-12">
                <p className="text-gray-300 mb-6">Trusted by leading firms</p>
                <div className="flex flex-wrap justify-center gap-4">
                  <button 
                    className="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors duration-200"
                    aria-label="Google - Trusted partner"
                    tabIndex={0}
                    onKeyDown={(e) => {
                      if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        // Handle click action
                      }
                    }}
                  >
                    <div className="w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold">G</div>
                    <span>Google</span>
                  </button>
                  <button 
                    className="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors duration-200"
                    aria-label="Clutch - Top rated agency"
                    tabIndex={0}
                    onKeyDown={(e) => {
                      if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        // Handle click action
                      }
                    }}
                  >
                    <div className="w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold">C</div>
                    <span>Clutch</span>
                  </button>
                  <button 
                    className="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors duration-200"
                    aria-label="Avvo - Legal excellence"
                    tabIndex={0}
                    onKeyDown={(e) => {
                      if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        // Handle click action
                      }
                    }}
                  >
                    <div className="w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold">A</div>
                    <span>Avvo</span>
                  </button>
                  <button 
                    className="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors duration-200"
                    aria-label="BBB - Better Business Bureau accredited"
                    tabIndex={0}
                    onKeyDown={(e) => {
                      if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        // Handle click action
                      }
                    }}
                  >
                    <div className="w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold">B</div>
                    <span>BBB</span>
                  </button>
                </div>
              </div>
              <div className="text-gray-300 animate-bounce">
                <span>↓ Learn more</span>
              </div>
            </div>
          </div>
          <div className="max-w-6xl mx-auto px-6 py-16">
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
              <a href="/leads" className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200">
                <h3 className="text-xl font-semibold text-gray-900 mb-3">Get Legal Help</h3>
                <p className="text-gray-600 mb-6">Submit your legal inquiry and get connected with the right attorney</p>
                <button className="btn-primary">Submit Inquiry</button>
              </a>
              <a href="/case-studies" className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200">
                <h3 className="text-xl font-semibold text-gray-900 mb-3">Case Studies</h3>
                <p className="text-gray-600 mb-6">Explore our successful legal cases and outcomes</p>
                <button className="btn-primary">View Cases</button>
              </a>
              <a href="/faqs" className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200">
                <h3 className="text-xl font-semibold text-gray-900 mb-3">FAQ</h3>
                <p className="text-gray-600 mb-6">Find answers to common legal questions</p>
                <button className="btn-primary">View FAQs</button>
              </a>
              <a href="/api-test" className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200">
                <h3 className="text-xl font-semibold text-gray-900 mb-3">API Test</h3>
                <p className="text-gray-600 mb-6">Test the connection to the backend API</p>
                <button className="btn-primary">Test API</button>
              </a>
            </div>
          </div>
        </div>
      </main>

      {/* Client Login Modal */}
      <ClientLoginModal
        open={isLoginModalOpen}
        onClose={() => setIsLoginModalOpen(false)}
        onSuccess={(user) => {
          console.log('Login successful:', user);
          // TODO: Handle successful login (e.g., update user state, redirect, etc.)
          setIsLoginModalOpen(false);
        }}
      />
    </div>
  );
}
