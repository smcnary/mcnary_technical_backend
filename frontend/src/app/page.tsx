'use client'

import { useState } from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import ClientLoginModal from '../components/ClientLoginModal';

export default function HomePage() {
  const [isLoginModalOpen, setIsLoginModalOpen] = useState(false);

  return (
    <div className="min-h-screen flex flex-col">
      <Header onOpenLogin={() => setIsLoginModalOpen(true)} />

      <main className="flex-1">
        {/* Hero Section */}
        <section className="hero-gradient text-white py-20 lg:py-32 relative overflow-hidden">
          {/* Background Pattern */}
          <div className="absolute inset-0 opacity-10">
            <div className="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyIi8+PC9nPjwvZz48L3N2Zz4=')]"></div>
          </div>
          
          <div className="max-w-6xl mx-auto px-6 text-center relative z-10">
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-shadow animate-fade-in">
              Legal SEO that wins cases
            </h1>
            <p className="text-xl md:text-2xl text-gray-200 mb-8 max-w-4xl mx-auto leading-relaxed animate-fade-in" style={{animationDelay: '0.2s'}}>
              We help law firms dominate Google search with local + AI-first SEO strategies that drive real results.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center mb-16 animate-fade-in" style={{animationDelay: '0.4s'}}>
              <button className="btn-primary text-lg px-8 py-4 text-lg">
                Book Demo
              </button>
              <a 
                href="/pricing"
                className="btn-outline text-lg px-8 py-4"
              >
                See Pricing
              </a>
            </div>
            
            {/* Trust Badges */}
            <div className="mb-12 animate-fade-in" style={{animationDelay: '0.6s'}}>
              <p className="text-gray-300 mb-8 text-lg font-medium">Trusted by leading firms</p>
              <div className="flex flex-wrap justify-center gap-4 max-w-2xl mx-auto">
                <div className="trust-badge">
                  <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold text-lg">G</div>
                  <span className="font-medium">Google</span>
                </div>
                <div className="trust-badge">
                  <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold text-lg">C</div>
                  <span className="font-medium">Clutch</span>
                </div>
                <div className="trust-badge">
                  <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold text-lg">A</div>
                  <span className="font-medium">Avvo</span>
                </div>
                <div className="trust-badge">
                  <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-900 font-bold text-lg">B</div>
                  <span className="font-medium">BBB</span>
                </div>
              </div>
            </div>
            
            {/* Scroll Indicator */}
            <div className="text-gray-300 animate-float">
              <div className="flex flex-col items-center gap-2">
                <span className="text-sm font-medium">Learn more</span>
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
              </div>
            </div>
          </div>
        </section>

        {/* Services Grid */}
        <section className="py-20 bg-gray-50">
          <div className="max-w-6xl mx-auto px-6">
            <div className="text-center mb-16">
              <h2 className="section-title">Get Started Today</h2>
              <p className="section-subtitle">
                Choose the service that best fits your legal needs and start your journey to better online visibility.
              </p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
              <a href="/leads" className="card-hover group">
                <div className="text-center">
                  <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors duration-200">
                    <svg className="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">Get Legal Help</h3>
                  <p className="text-gray-600 mb-6">Submit your legal inquiry and get connected with the right attorney for your case.</p>
                  <button className="btn-primary w-full">Submit Inquiry</button>
                </div>
              </a>
              
              <a href="/case-studies" className="card-hover group">
                <div className="text-center">
                  <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 transition-colors duration-200">
                    <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">Case Studies</h3>
                  <p className="text-gray-600 mb-6">Explore our successful legal cases and see real results from our SEO strategies.</p>
                  <button className="btn-primary w-full">View Cases</button>
                </div>
              </a>
              
              <a href="/faqs" className="card-hover group">
                <div className="text-center">
                  <div className="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-200 transition-colors duration-200">
                    <svg className="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">FAQ</h3>
                  <p className="text-gray-600 mb-6">Find answers to common legal questions and learn more about our services.</p>
                  <button className="btn-primary w-full">View FAQs</button>
                </div>
              </a>
              
              <a href="/api-test" className="card-hover group">
                <div className="text-center">
                  <div className="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-200">
                    <svg className="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">API Test</h3>
                  <p className="text-gray-600 mb-6">Test the connection to our backend API and verify system integration.</p>
                  <button className="btn-primary w-full">Test API</button>
                </div>
              </a>
            </div>
          </div>
        </section>
      </main>

      <Footer />

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
