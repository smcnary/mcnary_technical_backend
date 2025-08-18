import { useState, useEffect } from 'react';
import Header from './components/Header';
import ClientLoginModal from './components/ClientLoginModal';

function App() {
  const [activeTab, setActiveTab] = useState('home');
  const [isLoginModalOpen, setIsLoginModalOpen] = useState(false);

  // Valid tab names for navigation
  const validTabs = ['home', 'services', 'pricing', 'leads', 'case-studies', 'faqs', 'api-test', 'blog', 'about', 'contact'] as const;

  // Page titles for each tab
  const titles: Record<typeof validTabs[number], string> = {
    'home': 'CounselRank.legal - Professional Legal Services',
    'services': 'Our Services - CounselRank.legal',
    'pricing': 'Pricing Plans - CounselRank.legal',
    'leads': 'Get Legal Help - CounselRank.legal',
    'case-studies': 'Case Studies - CounselRank.legal',
    'faqs': 'Frequently Asked Questions - CounselRank.legal',
    'api-test': 'API Test - CounselRank.legal',
    'blog': 'Latest Insights - CounselRank.legal',
    'about': 'About Us - CounselRank.legal',
    'contact': 'Contact Us - CounselRank.legal'
  };

  // Validate if a tab name is valid
  const isValidTab = (tab: string): tab is typeof validTabs[number] => {
    return validTabs.includes(tab as any);
  };

  // Handle browser back/forward navigation
  useEffect(() => {
    const handlePopState = (event: PopStateEvent) => {
      console.log('PopState event triggered:', event.state);
      if (event.state && event.state.tab) {
        console.log(`Navigating to tab from history: ${event.state.tab}`);
        setActiveTab(event.state.tab);
      } else {
        console.log('Navigating to home from history');
        setActiveTab('home');
      }
    };

    // Handle initial page load with hash
    const handleInitialLoad = () => {
      const hash = window.location.hash.slice(1); // Remove the # symbol
      if (hash && isValidTab(hash)) {
        setActiveTab(hash);
        window.history.replaceState({ tab: hash }, '', `#${hash}`);
      }
      
      // Set initial title
      const currentTab = hash || 'home';
      document.title = titles[currentTab as keyof typeof titles] || titles.home;
    };

    // Set initial history state
    if (activeTab !== 'home') {
      window.history.replaceState({ tab: activeTab }, '', `#${activeTab}`);
    }

    handleInitialLoad();
    window.addEventListener('popstate', handlePopState);
    return () => window.removeEventListener('popstate', handlePopState);
  }, [activeTab]);

  // Update browser history when tab changes
  const handleTabChange = (tab: string) => {
    // Validate the tab before setting it
    if (!isValidTab(tab)) {
      console.warn(`Invalid tab: ${tab}`);
      return;
    }
    
    console.log(`Navigating to tab: ${tab}`);
    setActiveTab(tab);
    
    // Update browser title
    document.title = titles[tab];
    
    // Update browser history
    if (tab === 'home') {
      window.history.pushState({ tab }, '', '/');
    } else {
      window.history.pushState({ tab }, '', `#${tab}`);
    }
    
    console.log(`URL updated to: ${window.location.href}`);
  };

  const renderContent = () => {
    switch (activeTab) {
      case 'leads':
        return (
          <div className="max-w-4xl mx-auto px-6 py-12">
            <h2 className="section-title">Get Legal Help</h2>
            <p className="section-subtitle">Submit your legal inquiry and get connected with the right attorney</p>
            <form className="space-y-6">
              <input type="text" placeholder="Your Name" required className="input-field" />
              <input type="email" placeholder="Your Email" required className="input-field" />
              <textarea placeholder="Describe your legal issue" rows={5} required className="input-field" />
              <button type="submit" className="btn-primary">Submit Inquiry</button>
            </form>
          </div>
        );
      case 'case-studies':
        return (
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
        );
      case 'faqs':
        return (
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
        );
      case 'api-test':
        return (
          <div className="max-w-4xl mx-auto px-6 py-12">
            <h2 className="section-title">API Test</h2>
            <p className="section-subtitle">Test the connection to the backend API</p>
            <button className="btn-primary">Test Connection</button>
          </div>
        );
      case 'services':
        return (
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
        );
      case 'pricing':
        return (
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
        );
      case 'blog':
        return (
          <div className="max-w-6xl mx-auto px-6 py-12">
            <h2 className="section-title">Latest Insights</h2>
            <div className="grid md:grid-cols-2 gap-8">
              <div className="card">
                <h3 className="text-xl font-semibold text-gray-900 mb-3">5 Ways to Improve Your Law Firm&apos;s Local SEO</h3>
                <p className="text-gray-600 mb-4">Discover proven strategies to dominate local search results...</p>
                <button className="btn-secondary">Read More</button>
              </div>
              <div className="card">
                <h3 className="text-xl font-semibold text-gray-900 mb-3">The Future of Legal Marketing: AI-Powered SEO</h3>
                <p className="text-gray-600 mb-4">How artificial intelligence is transforming legal marketing...</p>
                <button className="btn-secondary">Read More</button>
              </div>
            </div>
          </div>
        );
      case 'about':
        return (
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
        );
      case 'contact':
        return (
          <div className="max-w-6xl mx-auto px-6 py-12">
            <h2 className="section-title">Get In Touch</h2>
            <p className="section-subtitle">Ready to dominate search results? Let&apos;s discuss your law firm&apos;s SEO strategy</p>
            <div className="grid md:grid-cols-2 gap-12">
              <div className="card">
                <h3 className="text-xl font-semibold text-gray-900 mb-6">Send us a message</h3>
                <form className="space-y-4">
                  <input type="text" placeholder="Name *" required className="input-field" />
                  <input type="email" placeholder="Email *" required className="input-field" />
                  <input type="tel" placeholder="Phone" className="input-field" />
                  <input type="text" placeholder="Law Firm" className="input-field" />
                  <select className="input-field">
                    <option value="">Select practice area</option>
                    <option value="personal-injury">Personal Injury</option>
                    <option value="family-law">Family Law</option>
                    <option value="criminal-defense">Criminal Defense</option>
                    <option value="business-law">Business Law</option>
                  </select>
                  <textarea placeholder="Message *" rows={5} required className="input-field" />
                  <button type="submit" className="btn-primary">Send Message</button>
                </form>
              </div>
              <div className="card">
                <h3 className="text-xl font-semibold text-gray-900 mb-6">Contact Information</h3>
                <div className="space-y-4 text-gray-600">
                  <div>
                    <strong className="text-gray-900">Email:</strong> hello@counselrank.legal
                  </div>
                  <div>
                    <strong className="text-gray-900">Phone:</strong> (555) 123-4567
                  </div>
                  <div>
                    <strong className="text-gray-900">Address:</strong> 123 Legal Street, Suite 100<br />
                    New York, NY 10001
                  </div>
                </div>
              </div>
            </div>
          </div>
        );
      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen flex flex-col">
      <Header 
        onNavigate={handleTabChange} 
        activeTab={activeTab}
        onOpenLogin={() => setIsLoginModalOpen(true)}
      />

      <main className="flex-1">
        {activeTab === 'home' && (
          <div>
            <div className="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-20">
              <div className="max-w-4xl mx-auto px-6 text-center">
                <h1 className="text-5xl font-bold mb-6">Legal SEO that wins cases</h1>
                <p className="text-xl text-gray-200 mb-8">We help law firms dominate Google search with local + AI-first SEO.</p>
                <div className="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                  <button className="btn-primary text-lg px-8 py-4">Book Demo</button>
                  <button 
                    className="text-gray-200 hover:text-white transition-colors duration-200 font-medium"
                    onClick={() => handleTabChange('pricing')}
                  >
                    See Pricing
                  </button>
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
                <div className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200" onClick={() => handleTabChange('leads')}>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">Get Legal Help</h3>
                  <p className="text-gray-600 mb-6">Submit your legal inquiry and get connected with the right attorney</p>
                  <button className="btn-primary">Submit Inquiry</button>
                </div>
                <div className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200" onClick={() => handleTabChange('case-studies')}>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">Case Studies</h3>
                  <p className="text-gray-600 mb-6">Explore our successful legal cases and outcomes</p>
                  <button className="btn-primary">View Cases</button>
                </div>
                <div className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200" onClick={() => handleTabChange('faqs')}>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">FAQ</h3>
                  <p className="text-gray-600 mb-6">Find answers to common legal questions</p>
                  <button className="btn-primary">View FAQs</button>
                </div>
                <div className="card text-center cursor-pointer hover:shadow-lg transition-shadow duration-200" onClick={() => handleTabChange('api-test')}>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">API Test</h3>
                  <p className="text-gray-600 mb-6">Test the connection to the backend API</p>
                  <button className="btn-primary">Test API</button>
                </div>
              </div>
            </div>
          </div>
        )}
        {renderContent()}
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

export default App;

