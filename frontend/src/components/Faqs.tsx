import React, { useState, useEffect, useCallback } from 'react';
import { apiService, Faq } from '../services/api';
import './Faqs.css';

const Faqs: React.FC = () => {
  const [faqs, setFaqs] = useState<Faq[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [expandedFaqs, setExpandedFaqs] = useState<Set<string>>(new Set());

  const fetchFaqs = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await apiService.getFaqs();
      setFaqs(response.member || []);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to fetch FAQs';
      setError(errorMessage);
      console.error('Error fetching FAQs:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchFaqs();
  }, [fetchFaqs]);

  const toggleFaq = useCallback((faqId: string) => {
    setExpandedFaqs(prev => {
      const newExpanded = new Set(prev);
      if (newExpanded.has(faqId)) {
        newExpanded.delete(faqId);
      } else {
        newExpanded.add(faqId);
      }
      return newExpanded;
    });
  }, []);

  const handleKeyPress = useCallback((faqId: string, event: React.KeyboardEvent) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      toggleFaq(faqId);
    }
  }, [toggleFaq]);

  const filteredFaqs = faqs.filter(faq => {
    if (!searchTerm) return true;
    const searchLower = searchTerm.toLowerCase();
    return (
      faq.question.toLowerCase().includes(searchLower) ||
      faq.answer.toLowerCase().includes(searchLower)
    );
  });

  const sortedFaqs = filteredFaqs.sort((a, b) => (a.sort || 0) - (b.sort || 0));

  if (loading) {
    return (
      <div className="faqs-container">
        <div className="loading">
          <div className="loading-spinner"></div>
          <p>Loading FAQs...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="faqs-container">
        <div className="error-message">
          <h3>Error Loading FAQs</h3>
          <p>{error}</p>
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
        <p>Find answers to common questions about CounselRank.legal services</p>
      </div>

      {faqs.length > 0 && (
        <div className="search-container" role="search">
          <div className="search-box">
            <input
              type="text"
              placeholder="Search FAQs..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="search-input"
              aria-label="Search frequently asked questions"
            />
            <span className="search-icon" aria-hidden="true">🔍</span>
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
        <div className="faqs-list" role="region" aria-label="Frequently asked questions">
          {sortedFaqs.map(faq => (
            <div key={faq.id} className="faq-item">
              <div 
                className="faq-question"
                onClick={() => toggleFaq(faq.id)}
                onKeyDown={(e) => handleKeyPress(faq.id, e)}
                tabIndex={0}
                role="button"
                aria-expanded={expandedFaqs.has(faq.id)}
                aria-controls={`faq-answer-${faq.id}`}
              >
                <h3>{faq.question}</h3>
                <span className="expand-icon" aria-hidden="true">
                  {expandedFaqs.has(faq.id) ? '−' : '+'}
                </span>
              </div>
              
              {expandedFaqs.has(faq.id) && (
                <div 
                  id={`faq-answer-${faq.id}`}
                  className="faq-answer"
                  role="region"
                  aria-label={`Answer to: ${faq.question}`}
                >
                  <p>{faq.answer}</p>
                  <div className="faq-meta">
                    <span className={`status status-${faq.isActive ? 'active' : 'inactive'}`}>
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
          <p>Can&apos;t find what you&apos;re looking for? <a href="#contact">Contact CounselRank.legal</a> for more information.</p>
        </div>
      )}
    </div>
  );
};

export default Faqs;
