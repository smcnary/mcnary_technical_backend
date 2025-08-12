import React, { useState, useEffect } from 'react';
import { apiService, CaseStudy } from '../services/api';
import './CaseStudies.css';

const CaseStudies: React.FC = () => {
  const [caseStudies, setCaseStudies] = useState<CaseStudy[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [filter, setFilter] = useState({
    practiceArea: '',
    isActive: true,
  });

  useEffect(() => {
    fetchCaseStudies();
  }, []);

  const fetchCaseStudies = async () => {
    try {
      setLoading(true);
      const response = await apiService.getCaseStudies();
      setCaseStudies(response.member || []);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to fetch case studies');
    } finally {
      setLoading(false);
    }
  };

  const filteredCaseStudies = caseStudies.filter(study => {
    if (filter.practiceArea && study.practiceArea !== filter.practiceArea) {
      return false;
    }
    if (filter.isActive !== undefined && study.isActive !== filter.isActive) {
      return false;
    }
    return true;
  });

  const practiceAreas = Array.from(
    new Set(caseStudies.map(study => study.practiceArea).filter(Boolean))
  );

  if (loading) {
    return (
      <div className="case-studies-container">
        <div className="loading">Loading case studies...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="case-studies-container">
        <div className="error-message">
          {error}
          <button onClick={fetchCaseStudies} className="btn-retry">
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="case-studies-container">
      <div className="case-studies-header">
        <h2>Case Studies</h2>
        <p>Explore our successful legal cases and outcomes</p>
      </div>

      {caseStudies.length > 0 && (
        <div className="filters">
          <div className="filter-group">
            <label htmlFor="practiceArea">Practice Area:</label>
            <select
              id="practiceArea"
              value={filter.practiceArea}
              onChange={(e) => setFilter(prev => ({ ...prev, practiceArea: e.target.value }))}
            >
              <option value="">All Practice Areas</option>
              {practiceAreas.map(area => (
                <option key={area} value={area}>{area}</option>
              ))}
            </select>
          </div>

          <div className="filter-group">
            <label>
              <input
                type="checkbox"
                checked={filter.isActive}
                onChange={(e) => setFilter(prev => ({ ...prev, isActive: e.target.checked }))}
              />
              Show Active Only
            </label>
          </div>
        </div>
      )}

      {filteredCaseStudies.length === 0 ? (
        <div className="no-results">
          {caseStudies.length === 0 ? (
            <p>No case studies available yet.</p>
          ) : (
            <p>No case studies match your current filters.</p>
          )}
        </div>
      ) : (
        <div className="case-studies-grid">
          {filteredCaseStudies
            .sort((a, b) => a.sort - b.sort)
            .map(study => (
              <div key={study.id} className="case-study-card">
                {study.heroImage && (
                  <div className="case-study-image">
                    <img src={study.heroImage} alt={study.title} />
                  </div>
                )}
                
                <div className="case-study-content">
                  <h3>{study.title}</h3>
                  
                  {study.practiceArea && (
                    <span className="practice-area">{study.practiceArea}</span>
                  )}
                  
                  {study.summary && (
                    <p className="summary">{study.summary}</p>
                  )}
                  
                  {study.metricsJson && Object.keys(study.metricsJson).length > 0 && (
                    <div className="metrics">
                      {Object.entries(study.metricsJson).map(([key, value]) => (
                        <div key={key} className="metric">
                          <span className="metric-label">{key}:</span>
                          <span className="metric-value">{String(value)}</span>
                        </div>
                      ))}
                    </div>
                  )}
                  
                  <div className="case-study-footer">
                    <span className="status">
                      {study.isActive ? 'Active' : 'Inactive'}
                    </span>
                    <span className="date">
                      {new Date(study.createdAt).toLocaleDateString()}
                    </span>
                  </div>
                </div>
              </div>
            ))}
        </div>
      )}
    </div>
  );
};

export default CaseStudies;
