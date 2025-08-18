import { useState, useEffect, useRef } from 'react';
import './App.css';

function App() {
  const [activeTab, setActiveTab] = useState('home');
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);
  const navRef = useRef<HTMLDivElement>(null);

  const handleDropdownToggle = (dropdown: string) => {
    setActiveDropdown(activeDropdown === dropdown ? null : dropdown);
  };

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (navRef.current && !navRef.current.contains(event.target as Node)) {
        setActiveDropdown(null);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const renderContent = () => {
    switch (activeTab) {
      case 'leads':
        return (
          <div className="content-section">
            <h2>Get Legal Help</h2>
            <p>Submit your legal inquiry and get connected with the right attorney</p>
            <form className="lead-form">
              <input type="text" placeholder="Your Name" required />
              <input type="email" placeholder="Your Email" required />
              <textarea placeholder="Describe your legal issue" rows={5} required></textarea>
              <button type="submit" className="btn-primary">Submit Inquiry</button>
            </form>
          </div>
        );
      case 'case-studies':
        return (
          <div className="content-section">
            <h2>Case Studies</h2>
            <p>Explore our successful legal cases and outcomes</p>
            <div className="case-studies-grid">
              <div className="case-study-card">
                <h3>Personal Injury Settlement</h3>
                <p>Successfully negotiated a $2.5M settlement for a car accident victim</p>
              </div>
              <div className="case-study-card">
                <h3>Family Law Resolution</h3>
                <p>Helped family reach amicable custody agreement</p>
              </div>
            </div>
          </div>
        );
      case 'faqs':
        return (
          <div className="content-section">
            <h2>Frequently Asked Questions</h2>
            <div className="faq-list">
              <div className="faq-item">
                <h3>How long does a typical case take?</h3>
                <p>Case duration varies depending on complexity and type. Simple cases may resolve in weeks, while complex litigation can take months or years.</p>
              </div>
              <div className="faq-item">
                <h3>What are your fees?</h3>
                <p>We offer various fee structures including contingency fees, hourly rates, and flat fees depending on the case type.</p>
              </div>
            </div>
          </div>
        );
      case 'api-test':
        return (
          <div className="content-section">
            <h2>API Test</h2>
            <p>Test the connection to the backend API</p>
            <button className="btn-primary">Test Connection</button>
          </div>
        );
      case 'services':
        return (
          <div className="content-section">
            <h2>Our Services</h2>
            <div className="services-grid">
              <div className="service-card">
                <h3>Local SEO</h3>
                <p>Optimize your law firm for local search results</p>
              </div>
              <div className="service-card">
                <h3>Content & AEO</h3>
                <p>Create content that answers user questions</p>
              </div>
              <div className="service-card">
                <h3>GBP & Reviews</h3>
                <p>Manage your Google Business Profile and reviews</p>
              </div>
              <div className="service-card">
                <h3>Technical SEO</h3>
                <p>Improve website performance and technical aspects</p>
              </div>
            </div>
          </div>
        );
      case 'pricing':
        return (
          <div className="content-section">
            <h2>Pricing Plans</h2>
            <div className="pricing-grid">
              <div className="pricing-card">
                <h3>Starter</h3>
                <div className="price">$3,000<span>/month</span></div>
                <ul>
                  <li>Local SEO optimization</li>
                  <li>Google Business Profile setup</li>
                  <li>Basic content creation</li>
                  <li>Monthly reporting</li>
                </ul>
                <button className="btn-primary">Select Plan</button>
              </div>
              <div className="pricing-card featured">
                <h3>Growth</h3>
                <div className="price">$6,000<span>/month</span></div>
                <ul>
                  <li>Everything in Starter</li>
                  <li>Advanced local SEO</li>
                  <li>Content creation (5 articles/month)</li>
                  <li>Review management</li>
                  <li>Priority support</li>
                </ul>
                <button className="btn-primary">Select Plan</button>
              </div>
              <div className="pricing-card">
                <h3>Premium</h3>
                <div className="price">$12,000<span>/month</span></div>
                <ul>
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
          <div className="content-section">
            <h2>Latest Insights</h2>
            <div className="blog-grid">
              <div className="blog-card">
                <h3>5 Ways to Improve Your Law Firm&apos;s Local SEO</h3>
                <p>Discover proven strategies to dominate local search results...</p>
                <button className="btn-secondary">Read More</button>
              </div>
              <div className="blog-card">
                <h3>The Future of Legal Marketing: AI-Powered SEO</h3>
                <p>How artificial intelligence is transforming legal marketing...</p>
                <button className="btn-secondary">Read More</button>
              </div>
            </div>
          </div>
        );
      case 'about':
        return (
          <div className="content-section">
            <h2>About CounselRank.legal</h2>
            <p>Your trusted partner for legal SEO that delivers results</p>
            <div className="about-grid">
              <div className="about-card">
                <h3>Our Mission</h3>
                <p>To help law firms dominate search results and attract qualified clients through data-driven SEO strategies.</p>
              </div>
              <div className="about-card">
                <h3>Why Choose Us</h3>
                <ul>
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
          <div className="content-section">
            <h2>Get In Touch</h2>
            <p>Ready to dominate search results? Let&apos;s discuss your law firm&apos;s SEO strategy</p>
            <div className="contact-grid">
              <div className="contact-form">
                <h3>Send us a message</h3>
                <form>
                  <input type="text" placeholder="Name *" required />
                  <input type="email" placeholder="Email *" required />
                  <input type="tel" placeholder="Phone" />
                  <input type="text" placeholder="Law Firm" />
                  <select>
                    <option value="">Select practice area</option>
                    <option value="personal-injury">Personal Injury</option>
                    <option value="family-law">Family Law</option>
                    <option value="criminal-defense">Criminal Defense</option>
                    <option value="business-law">Business Law</option>
                  </select>
                  <textarea placeholder="Message *" rows={5} required></textarea>
                  <button type="submit" className="btn-primary">Send Message</button>
                </form>
              </div>
              <div className="contact-info">
                <h3>Contact Information</h3>
                <div className="contact-item">
                  <strong>Email:</strong> hello@counselrank.legal
                </div>
                <div className="contact-item">
                  <strong>Phone:</strong> (555) 123-4567
                </div>
                <div className="contact-item">
                  <strong>Address:</strong> 123 Legal Street, Suite 100<br />
                  New York, NY 10001
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
    <div className="App">
      <header className="App-header">
        <div className="header-content">
          <h1 className="logo">CounselRank.legal</h1>
          
          <nav className="nav-menu" ref={navRef}>
            <button 
              className={`nav-item ${activeTab === 'home' ? 'active' : ''}`}
              onClick={() => setActiveTab('home')}
            >
              Home
            </button>
            
            {/* Services Dropdown */}
            <div className="nav-dropdown">
              <button 
                className={`nav-item dropdown-trigger ${activeDropdown === 'services' ? 'active' : ''}`}
                onClick={() => handleDropdownToggle('services')}
              >
                Services
                <span className="dropdown-arrow">▼</span>
              </button>
              {activeDropdown === 'services' && (
                <div className="dropdown-menu">
                  <button onClick={() => { setActiveTab('services'); setActiveDropdown(null); }}>Local SEO</button>
                  <button onClick={() => { setActiveTab('services'); setActiveDropdown(null); }}>Content & AEO</button>
                  <button onClick={() => { setActiveTab('services'); setActiveDropdown(null); }}>GBP & Reviews</button>
                  <button onClick={() => { setActiveTab('services'); setActiveDropdown(null); }}>Technical SEO</button>
                </div>
              )}
            </div>

            {/* Resources Dropdown */}
            <div className="nav-dropdown">
              <button 
                className={`nav-item dropdown-trigger ${activeDropdown === 'resources' ? 'active' : ''}`}
                onClick={() => handleDropdownToggle('resources')}
              >
                Resources
                <span className="dropdown-arrow">▼</span>
              </button>
              {activeDropdown === 'resources' && (
                <div className="dropdown-menu">
                  <button onClick={() => { setActiveTab('case-studies'); setActiveDropdown(null); }}>Case Studies</button>
                  <button onClick={() => { setActiveTab('blog'); setActiveDropdown(null); }}>Blog</button>
                  <button onClick={() => { setActiveTab('faqs'); setActiveDropdown(null); }}>FAQ</button>
                  <button onClick={() => { setActiveTab('api-test'); setActiveDropdown(null); }}>API Test</button>
                </div>
              )}
            </div>

            {/* Company Dropdown */}
            <div className="nav-dropdown">
              <button 
                className={`nav-item dropdown-trigger ${activeDropdown === 'company' ? 'active' : ''}`}
                onClick={() => handleDropdownToggle('company')}
              >
                Company
                <span className="dropdown-arrow">▼</span>
              </button>
              {activeDropdown === 'company' && (
                <div className="dropdown-menu">
                  <button onClick={() => { setActiveTab('about'); setActiveDropdown(null); }}>About</button>
                  <button onClick={() => { setActiveTab('pricing'); setActiveDropdown(null); }}>Pricing</button>
                  <button onClick={() => { setActiveTab('contact'); setActiveDropdown(null); }}>Contact</button>
                </div>
              )}
            </div>
          </nav>

          <div className="header-actions">
            <button className="btn-login">Client Login</button>
            <button className="btn-demo">Book Demo</button>
          </div>
        </div>
      </header>

      <main className="App-main">
        {activeTab === 'home' && (
          <div className="home-content">
            <div className="hero-section">
              <div className="hero-content">
                <h1 className="hero-headline">Legal SEO that wins cases</h1>
                <p className="hero-subhead">We help law firms dominate Google search with local + AI-first SEO.</p>
                <div className="hero-cta">
                  <button className="btn-primary btn-large">Book Demo</button>
                  <div className="secondary-cta">
                    <button 
                      className="link-secondary"
                      onClick={() => setActiveTab('pricing')}
                    >
                      See Pricing
                    </button>
                  </div>
                </div>
                <div className="trust-section">
                  <p className="trust-label">Trusted by leading firms</p>
                  <div className="trust-badges">
                    <button 
                      className="badge"
                      aria-label="Google - Trusted partner"
                      tabIndex={0}
                      onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                          e.preventDefault();
                          // Handle click action
                        }
                      }}
                    >
                      <div className="badge-logo">G</div>
                      <span>Google</span>
                    </button>
                    <button 
                      className="badge"
                      aria-label="Clutch - Top rated agency"
                      tabIndex={0}
                      onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                          e.preventDefault();
                          // Handle click action
                        }
                      }}
                    >
                      <div className="badge-logo">C</div>
                      <span>Clutch</span>
                    </button>
                    <button 
                      className="badge"
                      aria-label="Avvo - Legal excellence"
                      tabIndex={0}
                      onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                          e.preventDefault();
                          // Handle click action
                        }
                      }}
                    >
                      <div className="badge-logo">A</div>
                      <span>Avvo</span>
                    </button>
                    <button 
                      className="badge"
                      aria-label="BBB - Better Business Bureau accredited"
                      tabIndex={0}
                      onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                          e.preventDefault();
                          // Handle click action
                        }
                      }}
                    >
                      <div className="badge-logo">B</div>
                      <span>BBB</span>
                    </button>
                  </div>
                </div>
                <div className="scroll-indicator">
                  <span>↓ Learn more</span>
                </div>
              </div>
            </div>
            <div className="home-grid">
              <div className="home-card" onClick={() => setActiveTab('leads')}>
                <h3>Get Legal Help</h3>
                <p>Submit your legal inquiry and get connected with the right attorney</p>
                <button className="btn-primary">Submit Inquiry</button>
              </div>
              <div className="home-card" onClick={() => setActiveTab('case-studies')}>
                <h3>Case Studies</h3>
                <p>Explore our successful legal cases and outcomes</p>
                <button className="btn-primary">View Cases</button>
              </div>
              <div className="home-card" onClick={() => setActiveTab('faqs')}>
                <h3>FAQ</h3>
                <p>Find answers to common legal questions</p>
                <button className="btn-primary">View FAQs</button>
              </div>
              <div className="home-card" onClick={() => setActiveTab('api-test')}>
                <h3>API Test</h3>
                <p>Test the connection to the backend API</p>
                <button className="btn-primary">Test API</button>
              </div>
            </div>
          </div>
        )}
        {renderContent()}
      </main>
    </div>
  );
}

export default App;

