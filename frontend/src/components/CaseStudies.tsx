import React, { useState, useEffect, useCallback } from 'react';
import { apiService, CaseStudy } from '../services/api';

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
      <div className="max-w-6xl mx-auto px-6 py-12">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
          <p className="text-gray-600">Loading case studies...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-6xl mx-auto px-6 py-12">
        <div className="bg-red-50 border border-red-200 rounded-lg p-8 text-center">
          <h3 className="text-xl font-semibold text-red-800 mb-4">Error Loading Case Studies</h3>
          <p className="text-red-700 mb-6">{error}</p>
          <button onClick={fetchCaseStudies} className="btn-primary">
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto px-6 py-12">
      <div className="text-center mb-12">
        <h2 className="section-title">Case Studies</h2>
        <p className="section-subtitle">Explore our successful legal cases and outcomes at CounselRank.legal</p>
      </div>

      {caseStudies.length > 0 && (
        <div className="bg-gray-50 rounded-lg p-6 mb-8" role="region" aria-label="Case study filters">
          <div className="flex flex-col sm:flex-row gap-6">
            <div className="flex-1">
              <label htmlFor="practiceArea" className="block text-sm font-medium text-gray-700 mb-2">Practice Area:</label>
              <select
                id="practiceArea"
                value={filter.practiceArea}
                onChange={(e) => handleFilterChange('practiceArea', e.target.value)}
                aria-label="Filter by practice area"
                className="input-field"
              >
                <option value="">All Practice Areas</option>
                {practiceAreas.map(area => (
                  <option key={area} value={area}>{area}</option>
                ))}
              </select>
            </div>

            <div className="flex items-center">
              <label htmlFor="activeFilter" className="flex items-center space-x-2 cursor-pointer">
                <input
                  id="activeFilter"
                  type="checkbox"
                  checked={filter.isActive}
                  onChange={(e) => handleFilterChange('isActive', e.target.checked)}
                  aria-label="Show active case studies only"
                  className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <span className="text-sm text-gray-700">Show Active Only</span>
              </label>
            </div>
          </div>
        </div>
      )}

      {filteredCaseStudies.length === 0 ? (
        <div className="text-center py-12">
          {caseStudies.length === 0 ? (
            <p className="text-gray-500 text-lg">No case studies available yet.</p>
          ) : (
            <p className="text-gray-500 text-lg">No case studies match your current filters.</p>
          )}
        </div>
      ) : (
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8" role="region" aria-label="Case studies list">
          {filteredCaseStudies
            .sort((a, b) => (a.sort || 0) - (b.sort || 0))
            .map(study => (
              <article key={study.id} className="card hover:shadow-lg transition-shadow duration-200">
                {study.heroImage && (
                  <div className="mb-4">
                    <img 
                      src={study.heroImage} 
                      alt={`${study.title} case study`}
                      loading="lazy"
                      className="w-full h-48 object-cover rounded-lg"
                    />
                  </div>
                )}
                
                <div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">{study.title}</h3>
                  
                  {study.practiceArea && (
                    <span className="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mb-3" aria-label={`Practice area: ${study.practiceArea}`}>
                      {study.practiceArea}
                    </span>
                  )}
                  
                  {study.summary && (
                    <p className="text-gray-600 mb-4">{study.summary}</p>
                  )}
                  
                  {study.metricsJson && typeof study.metricsJson === 'object' && Object.keys(study.metricsJson).length > 0 && (
                    <div className="bg-gray-50 rounded-lg p-4 mb-4" role="region" aria-label="Case study metrics">
                      <div className="grid grid-cols-2 gap-2">
                        {Object.entries(study.metricsJson).map(([key, value]) => (
                          <div key={key} className="text-sm">
                            <span className="font-medium text-gray-700">{key}:</span>
                            <span className="text-gray-600 ml-1">{String(value)}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                  
                  <div className="flex justify-between items-center text-sm text-gray-500">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                      study.isActive 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-gray-100 text-gray-800'
                    }`}>
                      {study.isActive ? 'Active' : 'Inactive'}
                    </span>
                    <span>
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
