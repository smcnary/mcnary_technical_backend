import { useState } from 'react';
import './App.css';
import LeadForm from './components/LeadForm';
import CaseStudies from './components/CaseStudies';
import Faqs from './components/Faqs';

function App() {
  const [activeTab, setActiveTab] = useState('home');

  const renderContent = () => {
    switch (activeTab) {
      case 'leads':
        return <LeadForm />;
      case 'case-studies':
        return <CaseStudies />;
      case 'faqs':
        return <Faqs />;
      default:
        return (
          <div className="home-content">
            <h1>Welcome to CounselRank.legal</h1>
            <p>Your trusted partner for professional legal services</p>
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
          <nav className="nav-menu">
            <button 
              className={`nav-item ${activeTab === 'home' ? 'active' : ''}`}
              onClick={() => setActiveTab('home')}
            >
              Home
            </button>
            <button 
              className={`nav-item ${activeTab === 'leads' ? 'active' : ''}`}
              onClick={() => setActiveTab('leads')}
            >
              Get Legal Help
            </button>
            <button 
              className={`nav-item ${activeTab === 'case-studies' ? 'active' : ''}`}
              onClick={() => setActiveTab('case-studies')}
            >
              Case Studies
            </button>
            <button 
              className={`nav-item ${activeTab === 'faqs' ? 'active' : ''}`}
              onClick={() => setActiveTab('faqs')}
            >
              FAQ
            </button>
          </nav>
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
