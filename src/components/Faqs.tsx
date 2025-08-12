import React, { useState, useEffect } from 'react';
import { apiService, Faq } from '../services/api';
import './Faqs.css';

const Faqs: React.FC = () => {
  const [faqs, setFaqs] = useState<Faq[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [expandedFaqs, setExpandedFaqs] = useState<Set<string>>(new Set());

  useEffect(() => {
    fetchFaqs();
  }, []);

  const fetchFaqs = async () => {
    try {
      setLoading(true);
      const response = await apiService.getFaqs();
      setFaqs(response.member || []);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to fetch FAQs');
    } finally {
      setLoading(false);
    }
  };

  const toggleFaq = (faqId: string) => {
    const newExpanded = new Set(expandedFaqs);
    if (newExpanded.has(faqId)) {
      newExpanded.delete(faqId);
    } else {
      newExpanded.add(faqId);
    }
    setExpandedFaqs(newExpanded);
  };

  const filteredFaqs = faqs.filter(faq => {
    if (!searchTerm) return true;
    const searchLower = searchTerm.toLowerCase();
    return (
      faq.question.toLowerCase().includes(searchLower) ||
      faq.answer.toLowerCase().includes(searchLower)
    );
  });

  const sortedFaqs = filteredFaqs.sort((a, b) => a.sort - b.sort);

  if (loading) {
    return (
      <div className="faqs-container">
        <div className="loading">Loading FAQs...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="faqs-container">
        <div className="error-message">
          {error}
          <button onClick={fetchFaqs} className="btn-retry">
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="faqs-container">
      <div className="faqs-header">
        <h2>Frequently Asked Questions</h2>
        <p>Find answers to common questions about our legal services</p>
      </div>

      {faqs.length > 0 && (
        <div className="search-container">
          <div className="search-box">
            <input
              type="text"
              placeholder="Search FAQs..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="search-input"
            />
            <span className="search-icon">🔍</span>
          </div>
          <div className="search-results">
            {searchTerm && (
              <span className="results-count">
                {filteredFaqs.length} of {faqs.length} FAQs
              </span>
            )}
          </div>
        </div>
      )}

      {sortedFaqs.length === 0 ? (
        <div className="no-results">
          {faqs.length === 0 ? (
            <p>No FAQs available yet.</p>
          ) : (
            <p>No FAQs match your search.</p>
          )}
        </div>
      ) : (
        <div className="faqs-list">
          {sortedFaqs.map(faq => (
            <div key={faq.id} className="faq-item">
              <div 
                className="faq-question"
                onClick={() => toggleFaq(faq.id)}
              >
                <h3>{faq.question}</h3>
                <span className="expand-icon">
                  {expandedFaqs.has(faq.id) ? '−' : '+'}
                </span>
              </div>
              
              {expandedFaqs.has(faq.id) && (
                <div className="faq-answer">
                  <p>{faq.answer}</p>
                  <div className="faq-meta">
                    <span className="status">
                      {faq.isActive ? 'Active' : 'Inactive'}
                    </span>
                    <span className="date">
                      {new Date(faq.createdAt).toLocaleDateString()}
                    </span>
                  </div>
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      {faqs.length > 0 && (
        <div className="faqs-footer">
          <p>Can't find what you're looking for? <a href="#contact">Contact us</a> for more information.</p>
        </div>
      )}
    </div>
  );
};

export default Faqs;
