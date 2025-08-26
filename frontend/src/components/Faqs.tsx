import React, { useState, useEffect, useCallback } from 'react';
import { apiService, Faq } from '../services/api';

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
      const newSet = new Set(prev);
      if (newSet.has(faqId)) {
        newSet.delete(faqId);
      } else {
        newSet.add(faqId);
      }
      return newSet;
    });
  }, []);

  const handleKeyPress = useCallback((faqId: string, e: React.KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
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
      <div className="max-w-4xl mx-auto px-6 py-12">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
          <p className="text-gray-600">Loading FAQs...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-4xl mx-auto px-6 py-12">
        <div className="bg-red-50 border border-red-200 rounded-lg p-8 text-center">
          <h3 className="text-xl font-semibold text-red-800 mb-4">Error Loading FAQs</h3>
          <p className="text-red-700 mb-6">{error}</p>
          <button onClick={fetchFaqs} className="btn-primary">
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto px-6 py-12">
      <div className="text-center mb-12">
        <h2 className="section-title">Frequently Asked Questions</h2>
        <p className="section-subtitle">Find answers to common questions about tulsa-seo.com services</p>
      </div>

      {faqs.length > 0 && (
        <div className="mb-8" role="search">
          <div className="relative max-w-md mx-auto">
            <input
              type="text"
              placeholder="Search FAQs..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              aria-label="Search frequently asked questions"
            />
            <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" aria-hidden="true">🔍</span>
          </div>
          <div className="text-center mt-2">
            {searchTerm && (
              <span className="text-sm text-gray-500">
                {filteredFaqs.length} of {faqs.length} FAQs
              </span>
            )}
          </div>
        </div>
      )}

      {sortedFaqs.length === 0 ? (
        <div className="text-center py-12">
          {faqs.length === 0 ? (
            <p className="text-gray-500 text-lg">No FAQs available yet.</p>
          ) : (
            <p className="text-gray-500 text-lg">No FAQs match your search.</p>
          )}
        </div>
      ) : (
        <div className="space-y-4" role="region" aria-label="Frequently asked questions">
          {sortedFaqs.map(faq => (
            <div key={faq.id} className="card">
              <div 
                className="flex justify-between items-center cursor-pointer hover:bg-gray-50 p-4 -m-6 rounded-lg transition-colors duration-200"
                onClick={() => toggleFaq(faq.id)}
                onKeyDown={(e) => handleKeyPress(faq.id, e)}
                tabIndex={0}
                role="button"
                aria-expanded={expandedFaqs.has(faq.id)}
                aria-controls={`faq-answer-${faq.id}`}
              >
                <h3 className="text-lg font-semibold text-gray-900 pr-4">{faq.question}</h3>
                <span className="text-2xl font-bold text-gray-400 flex-shrink-0" aria-hidden="true">
                  {expandedFaqs.has(faq.id) ? '−' : '+'}
                </span>
              </div>
              
              {expandedFaqs.has(faq.id) && (
                <div 
                  id={`faq-answer-${faq.id}`}
                  className="pt-4 border-t border-gray-200"
                  role="region"
                  aria-label={`Answer to: ${faq.question}`}
                >
                  <p className="text-gray-700 mb-4 leading-relaxed">{faq.answer}</p>
                  <div className="flex justify-between items-center text-sm text-gray-500">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                      faq.isActive 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-gray-100 text-gray-800'
                    }`}>
                      {faq.isActive ? 'Active' : 'Inactive'}
                    </span>
                    <span>
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
        <div className="text-center mt-12 pt-8 border-t border-gray-200">
          <p className="text-gray-600">
            Can&apos;t find what you&apos;re looking for?{' '}
            <a href="#contact" className="text-blue-600 hover:text-blue-800 font-medium">
              Contact tulsa-seo.com
            </a>{' '}
            for more information.
          </p>
        </div>
      )}
    </div>
  );
};

export default Faqs;
