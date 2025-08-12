import React, { useState, useEffect, useCallback } from 'react';
import { apiService, CaseStudy } from '../services/api';
import './CaseStudies.css';

interface FilterState {
  practiceArea: string;
  isActive: boolean;
}

const CaseStudies: React.FC = () => {
  const [caseStudies, setCaseStudies] = useState<CaseStudy[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [filter, setFilter] = useState<FilterState>({
    practiceArea: '',
    isActive: true,
  });

  const fetchCaseStudies = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await apiService.getCaseStudies();
      setCaseStudies(response.member || []);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to fetch case studies';
      setError(errorMessage);
      console.error('Error fetching case studies:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchCaseStudies();
  }, [fetchCaseStudies]);

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
  ).sort();

  const handleFilterChange = (key: keyof FilterState, value: string | boolean) => {
    setFilter(prev => ({ ...prev, [key]: value }));
  };

  if (loading) {
    return (
      <div className="case-studies-container">
        <div className="loading">
          <div className="loading-spinner"></div>
          <p>Loading case studies...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="case-studies-container">
        <div className="error-message">
          <h3>Error Loading Case Studies</h3>
          <p>{error}</p>
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
        <div className="filters" role="region" aria-label="Case study filters">
          <div className="filter-group">
            <label htmlFor="practiceArea">Practice Area:</label>
            <select
              id="practiceArea"
              value={filter.practiceArea}
              onChange={(e) => handleFilterChange('practiceArea', e.target.value)}
              aria-label="Filter by practice area"
            >
              <option value="">All Practice Areas</option>
              {practiceAreas.map(area => (
                <option key={area} value={area}>{area}</option>
              ))}
            </select>
          </div>

          <div className="filter-group">
            <label htmlFor="activeFilter">
              <input
                id="activeFilter"
                type="checkbox"
                checked={filter.isActive}
                onChange={(e) => handleFilterChange('isActive', e.target.checked)}
                aria-label="Show active case studies only"
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
        <div className="case-studies-grid" role="region" aria-label="Case studies list">
          {filteredCaseStudies
            .sort((a, b) => (a.sort || 0) - (b.sort || 0))
            .map(study => (
              <article key={study.id} className="case-study-card">
                {study.heroImage && (
                  <div className="case-study-image">
                    <img 
                      src={study.heroImage} 
                      alt={`${study.title} case study`}
                      loading="lazy"
                    />
                  </div>
                )}
                
                <div className="case-study-content">
                  <h3>{study.title}</h3>
                  
                  {study.practiceArea && (
                    <span className="practice-area" aria-label={`Practice area: ${study.practiceArea}`}>
                      {study.practiceArea}
                    </span>
                  )}
                  
                  {study.summary && (
                    <p className="summary">{study.summary}</p>
                  )}
                  
                  {study.metricsJson && typeof study.metricsJson === 'object' && Object.keys(study.metricsJson).length > 0 && (
                    <div className="metrics" role="region" aria-label="Case study metrics">
                      {Object.entries(study.metricsJson).map(([key, value]) => (
                        <div key={key} className="metric">
                          <span className="metric-label">{key}:</span>
                          <span className="metric-value">{String(value)}</span>
                        </div>
                      ))}
                    </div>
                  )}
                  
                  <div className="case-study-footer">
                    <span className={`status status-${study.isActive ? 'active' : 'inactive'}`}>
                      {study.isActive ? 'Active' : 'Inactive'}
                    </span>
                    <span className="date">
                      {new Date(study.createdAt).toLocaleDateString()}
                    </span>
                  </div>
                </div>
              </article>
            ))}
        </div>
      )}
    </div>
  );
};

export default CaseStudies;
