import { useState, useEffect, useRef } from 'react';
import './App.css';
import LeadForm from './components/LeadForm';
import CaseStudies from './components/CaseStudies';
import ApiTest from './components/ApiTest';

function App() {
  const [activeTab, setActiveTab] = useState('home');
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);
  const [openFaqs, setOpenFaqs] = useState<Set<string>>(new Set(['faq-2']));
  const navRef = useRef<HTMLDivElement>(null);

  const handleDropdownToggle = (dropdown: string) => {
    setActiveDropdown(activeDropdown === dropdown ? null : dropdown);
  };

  const toggleFaq = (faqId: string) => {
    const newOpenFaqs = new Set(openFaqs);
    if (newOpenFaqs.has(faqId)) {
      newOpenFaqs.delete(faqId);
    } else {
      newOpenFaqs.add(faqId);
    }
    setOpenFaqs(newOpenFaqs);
  };

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (navRef.current && !navRef.current.contains(event.target as Node)) {
        setActiveDropdown(null);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  const renderContent = () => {
    switch (activeTab) {
      case 'leads':
        return <LeadForm />;
      case 'case-studies':
        return <CaseStudies />;
      case 'faqs':
        return (
          <div className="faq-content">
            <div className="faq-hero">
              <h1>Frequently Asked Questions</h1>
              <p>Find answers to common questions about our legal SEO services</p>
            </div>
            <div className="faq-accordion">
              <div className="faq-item">
                <button 
                  className={`faq-question ${openFaqs.has('faq-1') ? 'active' : ''}`}
                  onClick={() => toggleFaq('faq-1')}
                  aria-expanded={openFaqs.has('faq-1')}
                  aria-controls="faq-answer-1"
                >
                  <span className="faq-icon">{openFaqs.has('faq-1') ? '▼' : '►'}</span>
                  <span>How long until results?</span>
                </button>
                <div className={`faq-answer ${openFaqs.has('faq-1') ? 'active' : ''}`} id="faq-answer-1">
                  <p>Most law firms see initial improvements in local search rankings within 30-60 days. Significant traffic increases typically occur within 3-6 months, while comprehensive SEO results develop over 6-12 months. We provide monthly reports to track progress and adjust strategies as needed.</p>
                </div>
              </div>
              
              <div className="faq-item">
                <button 
                  className={`faq-question ${openFaqs.has('faq-2') ? 'active' : ''}`}
                  onClick={() => toggleFaq('faq-2')}
                  aria-expanded={openFaqs.has('faq-2')}
                  aria-controls="faq-answer-2"
                >
                  <span className="faq-icon">{openFaqs.has('faq-2') ? '▼' : '►'}</span>
                  <span>What is AEO/GEO?</span>
                </button>
                <div className={`faq-answer ${openFaqs.has('faq-2') ? 'active' : ''}`} id="faq-answer-2">
                  <p><strong>AEO (Answer Engine Optimization)</strong> focuses on optimizing content to appear in Google&apos;s featured snippets and answer boxes. <strong>GEO (Geographic Engine Optimization)</strong> targets location-based searches to improve local visibility. Together, they help law firms capture both informational and local search traffic.</p>
                </div>
              </div>
              
              <div className="faq-item">
                <button 
                  className={`faq-question ${openFaqs.has('faq-3') ? 'active' : ''}`}
                  onClick={() => toggleFaq('faq-3')}
                  aria-expanded={openFaqs.has('faq-3')}
                  aria-controls="faq-answer-3"
                >
                  <span className="faq-icon">{openFaqs.has('faq-3') ? '▼' : '►'}</span>
                  <span>Do you work with PI firms?</span>
                </button>
                <div className={`faq-answer ${openFaqs.has('faq-3') ? 'active' : ''}`} id="faq-answer-3">
                  <p>Yes, we specialize in personal injury law firms and understand the unique challenges of PI marketing. Our strategies are designed to navigate advertising restrictions while maximizing organic search visibility. We&apos;ve helped numerous PI firms dominate local search results and increase case inquiries.</p>
                </div>
              </div>
              
              <div className="faq-item">
                <button 
                  className={`faq-question ${openFaqs.has('faq-4') ? 'active' : ''}`}
                  onClick={() => toggleFaq('faq-4')}
                  aria-expanded={openFaqs.has('faq-4')}
                  aria-controls="faq-answer-4"
                >
                  <span className="faq-icon">{openFaqs.has('faq-4') ? '▼' : '►'}</span>
                  <span>What makes your approach different?</span>
                </button>
                <div className={`faq-answer ${openFaqs.has('faq-4') ? 'active' : ''}`} id="faq-answer-4">
                  <p>We combine AI-powered keyword research with deep legal industry expertise. Unlike generic SEO agencies, we understand legal marketing regulations, client acquisition cycles, and the specific search behaviors of potential clients. Our local-first approach ensures you dominate your geographic market.</p>
                </div>
              </div>
              
              <div className="faq-item">
                <button 
                  className={`faq-question ${openFaqs.has('faq-5') ? 'active' : ''}`}
                  onClick={() => toggleFaq('faq-5')}
                  aria-expanded={openFaqs.has('faq-5')}
                  aria-controls="faq-answer-5"
                >
                  <span className="faq-icon">{openFaqs.has('faq-5') ? '▼' : '►'}</span>
                  <span>Can you work with multiple office locations?</span>
                </button>
                <div className={`faq-answer ${openFaqs.has('faq-5') ? 'active' : ''}`} id="faq-answer-5">
                  <p>Absolutely! We specialize in multi-location SEO strategies. We&apos;ll optimize each office location for its specific geographic area while maintaining brand consistency. This includes local keyword targeting, Google Business Profile optimization, and location-specific content strategies.</p>
                </div>
              </div>
              
              <div className="faq-item">
                <button 
                  className={`faq-question ${openFaqs.has('faq-6') ? 'active' : ''}`}
                  onClick={() => toggleFaq('faq-6')}
                  aria-expanded={openFaqs.has('faq-6')}
                  aria-controls="faq-answer-6"
                >
                  <span className="faq-icon">{openFaqs.has('faq-6') ? '▼' : '►'}</span>
                  <span>What&apos;s included in your monthly reporting?</span>
                </button>
                <div className={`faq-answer ${openFaqs.has('faq-6') ? 'active' : ''}`} id="faq-answer-6">
                  <p>Our comprehensive monthly reports include keyword ranking changes, organic traffic growth, local search performance, competitor analysis, and actionable recommendations. We also provide insights into lead generation improvements and ROI metrics specific to legal marketing.</p>
                </div>
              </div>
            </div>
            <div className="faq-cta">
              <h2>Still have questions?</h2>
              <p>Let&apos;s discuss your specific needs and how we can help your law firm succeed</p>
              <div className="cta-buttons">
                <button className="btn-primary btn-large">Book Demo</button>
                <a href="#contact" className="link-secondary">Contact Us</a>
              </div>
            </div>
          </div>
        );
      case 'api-test':
        return <ApiTest />;
      case 'services':
        return (
          <div className="services-content">
            <div className="services-hero">
              <h1>Our Services</h1>
              <p>Comprehensive SEO solutions designed specifically for law firms</p>
            </div>
            <div className="services-grid">
              <div className="service-card">
                <div className="service-icon">🎯</div>
                <h3>Local SEO</h3>
                <p>Dominate local search results with targeted optimization for your geographic service areas</p>
                <ul>
                  <li>Google Business Profile optimization</li>
                  <li>Local keyword targeting</li>
                  <li>Citation management</li>
                  <li>Local link building</li>
                </ul>
                <button className="btn-primary">Learn More</button>
              </div>
              <div className="service-card">
                <div className="service-icon">📝</div>
                <h3>Content & AEO</h3>
                <p>Create compelling content that answers user questions and improves search visibility</p>
                <ul>
                  <li>Answer Engine Optimization</li>
                  <li>Legal content strategy</li>
                  <li>Case study development</li>
                  <li>Blog and article creation</li>
                </ul>
                <button className="btn-primary">Learn More</button>
              </div>
              <div className="service-card">
                <div className="service-icon">⭐</div>
                <h3>GBP & Reviews</h3>
                <p>Build and maintain your online reputation through review management</p>
                <ul>
                  <li>Review monitoring & response</li>
                  <li>Reputation management</li>
                  <li>Client feedback collection</li>
                  <li>Review site optimization</li>
                </ul>
                <button className="btn-primary">Learn More</button>
              </div>
              <div className="service-card">
                <div className="service-icon">🔧</div>
                <h3>Technical SEO</h3>
                <p>Optimize your website&apos;s technical foundation for maximum search performance</p>
                <ul>
                  <li>Site speed optimization</li>
                  <li>Mobile-first indexing</li>
                  <li>Schema markup implementation</li>
                  <li>Core Web Vitals improvement</li>
                </ul>
                <button className="btn-primary">Learn More</button>
              </div>
            </div>
            <div className="services-cta">
              <h2>Ready to dominate search results?</h2>
              <p>Let&apos;s discuss your law firm&apos;s SEO strategy</p>
              <div className="cta-buttons">
                <button className="btn-primary btn-large">Book Demo</button>
                <a href="#pricing" className="link-secondary">See Pricing</a>
              </div>
            </div>
          </div>
        );
      case 'pricing':
        return (
          <div className="pricing-content">
            <div className="pricing-hero">
              <h1>Pricing Plans</h1>
              <p>Choose the plan that fits your law firm&apos;s needs</p>
            </div>
            <div className="pricing-grid">
              <div className="pricing-card">
                <div className="pricing-header">
                  <h3>Starter</h3>
                  <div className="price">$3,000<span>/month</span></div>
                  <p className="plan-description">Perfect for small law firms getting started with SEO</p>
                </div>
                <ul className="features-list">
                  <li>✓ Basic SEO audit & recommendations</li>
                  <li>✓ Local optimization & GBP setup</li>
                  <li>✓ Monthly performance reporting</li>
                  <li>✓ Email support & consultation</li>
                  <li>✓ Basic keyword research (50 keywords)</li>
                  <li>✓ Citation management</li>
                </ul>
                <button className="btn-primary">Select Plan</button>
              </div>
              <div className="pricing-card featured">
                <div className="featured-badge">Most Popular</div>
                <div className="pricing-header">
                  <h3>Growth</h3>
                  <div className="price">$6,000<span>/month</span></div>
                  <p className="plan-description">Ideal for growing firms ready to scale their online presence</p>
                </div>
                <ul className="features-list">
                  <li>✓ Everything in Starter</li>
                  <li>✓ Content creation & optimization</li>
                  <li>✓ Review management & response</li>
                  <li>✓ Priority support & dedicated account manager</li>
                  <li>✓ Advanced keyword research (150 keywords)</li>
                  <li>✓ Local link building campaigns</li>
                  <li>✓ Monthly strategy calls</li>
                  <li>✓ Competitor analysis</li>
                </ul>
                <button className="btn-primary">Select Plan</button>
              </div>
              <div className="pricing-card">
                <div className="pricing-header">
                  <h3>Premium</h3>
                  <div className="price">$12,000<span>/month</span></div>
                  <p className="plan-description">Comprehensive SEO solution for established law firms</p>
                </div>
                <ul className="features-list">
                  <li>✓ Everything in Growth</li>
                  <li>✓ Advanced analytics & reporting</li>
                  <li>✓ Dedicated SEO specialist</li>
                  <li>✓ 24/7 priority support</li>
                  <li>✓ Unlimited keyword optimization</li>
                  <li>✓ Advanced technical SEO</li>
                  <li>✓ Weekly performance reviews</li>
                  <li>✓ Custom strategy development</li>
                  <li>✓ Crisis management support</li>
                </ul>
                <button className="btn-primary">Select Plan</button>
              </div>
            </div>
            <div className="pricing-cta">
              <h2>Need a custom solution?</h2>
              <p>Let&apos;s discuss your specific requirements and create a tailored plan</p>
              <div className="cta-buttons">
                <button className="btn-primary btn-large">Book Demo</button>
                <a href="#contact" className="link-secondary">Contact Sales</a>
              </div>
            </div>
          </div>
        );
      case 'blog':
        return (
          <div className="blog-content">
            <div className="blog-hero">
              <h1>Latest Insights</h1>
              <p>Stay updated with the latest SEO trends and legal marketing strategies</p>
            </div>
            <div className="blog-grid">
              <div className="blog-card">
                <div className="blog-image">
                  <div className="blog-placeholder">📊</div>
                </div>
                <div className="blog-content">
                  <h3>5 Ways to Improve Your Law Firm&apos;s Local SEO</h3>
                  <p>Discover proven strategies to dominate local search results and attract more clients in your area. Learn the key tactics that successful law firms use to rank higher in local searches.</p>
                  <div className="blog-meta">
                    <span className="blog-date">January 15, 2025</span>
                    <span className="blog-category">Local SEO</span>
                  </div>
                  <button className="btn-secondary">Read More</button>
                </div>
              </div>
              <div className="blog-card">
                <div className="blog-image">
                  <div className="blog-placeholder">🤖</div>
                </div>
                <div className="blog-content">
                  <h3>The Future of Legal Marketing: AI-Powered SEO</h3>
                  <p>How artificial intelligence is transforming the way law firms approach search engine optimization. Explore cutting-edge AI tools and strategies that give you a competitive edge.</p>
                  <div className="blog-meta">
                    <span className="blog-date">January 10, 2025</span>
                    <span className="blog-category">AI & Technology</span>
                  </div>
                  <button className="btn-secondary">Read More</button>
                </div>
              </div>
              <div className="blog-card">
                <div className="blog-image">
                  <div className="blog-placeholder">⭐</div>
                </div>
                <div className="blog-content">
                  <h3>Building Trust Through Online Reviews</h3>
                  <p>Strategies for managing your online reputation and leveraging reviews to build client trust. Learn how to turn positive reviews into powerful marketing tools.</p>
                  <div className="blog-meta">
                    <span className="blog-date">January 5, 2025</span>
                    <span className="blog-category">Reputation Management</span>
                  </div>
                  <button className="btn-secondary">Read More</button>
                </div>
              </div>
              <div className="blog-card">
                <div className="blog-image">
                  <div className="blog-placeholder">📱</div>
                </div>
                <div className="blog-content">
                  <h3>Mobile-First SEO for Law Firms</h3>
                  <p>Why mobile optimization is crucial for legal websites and how to implement mobile-first SEO strategies that improve rankings and user experience.</p>
                  <div className="blog-meta">
                    <span className="blog-date">January 1, 2025</span>
                    <span className="blog-category">Technical SEO</span>
                  </div>
                  <button className="btn-secondary">Read More</button>
                </div>
              </div>
            </div>
            <div className="blog-cta">
              <h2>Stay Updated with Legal SEO Insights</h2>
              <p>Get the latest strategies and tips delivered to your inbox</p>
              <div className="newsletter-signup">
                <input type="email" placeholder="Enter your email address" className="newsletter-input" />
                <button className="btn-primary">Subscribe</button>
              </div>
            </div>
          </div>
        );
      case 'about':
        return (
          <div className="about-content">
            <div className="about-hero">
              <h1>About CounselRank.legal</h1>
              <p>Your trusted partner for legal SEO that delivers results</p>
            </div>
            <div className="about-sections">
              <div className="about-section">
                <div className="about-icon">🎯</div>
                <h3>Our Mission</h3>
                <p>To help law firms dominate search results and attract qualified clients through data-driven SEO strategies and local optimization. We believe every law firm deserves to be found by the clients who need them most.</p>
              </div>
              <div className="about-section">
                <div className="about-icon">💼</div>
                <h3>Why Choose Us</h3>
                <ul>
                  <li>Specialized in legal industry SEO</li>
                  <li>Proven track record of results</li>
                  <li>Local expertise for geographic markets</li>
                  <li>AI-powered optimization strategies</li>
                  <li>Dedicated account management</li>
                  <li>Transparent reporting and analytics</li>
                </ul>
              </div>
              <div className="about-section">
                <div className="about-icon">🔄</div>
                <h3>Our Process</h3>
                <ol>
                  <li><strong>Discovery & Audit:</strong> Comprehensive analysis of your current SEO performance</li>
                  <li><strong>Strategy Development:</strong> Custom SEO roadmap tailored to your practice area</li>
                  <li><strong>Implementation:</strong> Systematic execution of optimization strategies</li>
                  <li><strong>Monitoring & Optimization:</strong> Continuous improvement based on data insights</li>
                </ol>
              </div>
            </div>
            <div className="about-stats">
              <div className="stat-item">
                <div className="stat-number">500+</div>
                <div className="stat-label">Law Firms Helped</div>
              </div>
              <div className="stat-item">
                <div className="stat-number">95%</div>
                <div className="stat-label">Client Retention Rate</div>
              </div>
              <div className="stat-item">
                <div className="stat-number">3x</div>
                <div className="stat-label">Average Traffic Increase</div>
              </div>
              <div className="stat-item">
                <div className="stat-number">24/7</div>
                <div className="stat-label">Support Available</div>
              </div>
            </div>
            <div className="about-cta">
              <h2>Ready to Transform Your Law Firm&apos;s Online Presence?</h2>
              <p>Join hundreds of successful law firms who trust us with their SEO</p>
              <div className="cta-buttons">
                <button className="btn-primary btn-large">Book Demo</button>
                <a href="#contact" className="link-secondary">Contact Us</a>
              </div>
            </div>
          </div>
        );
      case 'contact':
        return (
          <div className="contact-content">
            <div className="contact-hero">
              <h1>Get In Touch</h1>
              <p>Ready to dominate search results? Let&apos;s discuss your law firm&apos;s SEO strategy</p>
            </div>
            <div className="contact-grid">
              <div className="contact-form">
                <h3>Send us a message</h3>
                <form>
                  <div className="form-group">
                    <label htmlFor="name">Name *</label>
                    <input type="text" id="name" required />
                  </div>
                  <div className="form-group">
                    <label htmlFor="email">Email *</label>
                    <input type="email" id="email" required />
                  </div>
                  <div className="form-group">
                    <label htmlFor="phone">Phone</label>
                    <input type="tel" id="phone" />
                  </div>
                  <div className="form-group">
                    <label htmlFor="firm">Law Firm</label>
                    <input type="text" id="firm" />
                  </div>
                  <div className="form-group">
                    <label htmlFor="practice">Practice Area</label>
                    <select id="practice">
                      <option value="">Select your practice area</option>
                      <option value="personal-injury">Personal Injury</option>
                      <option value="family-law">Family Law</option>
                      <option value="criminal-defense">Criminal Defense</option>
                      <option value="business-law">Business Law</option>
                      <option value="real-estate">Real Estate</option>
                      <option value="estate-planning">Estate Planning</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                  <div className="form-group">
                    <label htmlFor="message">Message *</label>
                    <textarea id="message" rows={5} required placeholder="Tell us about your SEO needs and goals..."></textarea>
                  </div>
                  <div className="form-group checkbox-group">
                    <label className="checkbox-label">
                      <input type="checkbox" required />
                      <span>I agree to receive communications from CounselRank.legal</span>
                    </label>
                  </div>
                  <button type="submit" className="btn-primary">Send Message</button>
                </form>
              </div>
              <div className="contact-info">
                <h3>Contact Information</h3>
                <div className="contact-item">
                  <div className="contact-icon">📧</div>
                  <div>
                    <strong>Email:</strong>
                    <p>hello@counselrank.legal</p>
                  </div>
                </div>
                <div className="contact-item">
                  <div className="contact-icon">📞</div>
                  <div>
                    <strong>Phone:</strong>
                    <p>(555) 123-4567</p>
                  </div>
                </div>
                <div className="contact-item">
                  <div className="contact-icon">📍</div>
                  <div>
                    <strong>Address:</strong>
                    <p>123 Legal Street, Suite 100<br />New York, NY 10001</p>
                  </div>
                </div>
                <div className="contact-item">
                  <div className="contact-icon">⏰</div>
                  <div>
                    <strong>Business Hours:</strong>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM EST<br />Weekend: By appointment</p>
                  </div>
                </div>
                <div className="social-links">
                  <h4>Follow Us</h4>
                  <div className="social-icons">
                    <a href="#" className="social-icon">📘</a>
                    <a href="#" className="social-icon">🐦</a>
                    <a href="#" className="social-icon">💼</a>
                    <a href="#" className="social-icon">📸</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        );
      default:
        return (
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
                    <div className="badge">
                      <div className="badge-logo">G</div>
                      <span>Google</span>
                    </div>
                    <div className="badge">
                      <div className="badge-logo">C</div>
                      <span>Clutch</span>
                    </div>
                    <div className="badge">
                      <div className="badge-logo">A</div>
                      <span>Avvo</span>
                    </div>
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
        );
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
        {renderContent()}
      </main>

      <footer className="App-footer">
        <div className="footer-content">
          <p>&copy; 2025 CounselRank.legal. All rights reserved.</p>
          <p>Your trusted partner for professional legal services.</p>
        </div>
      </footer>
    </div>
  );
}

export default App;
