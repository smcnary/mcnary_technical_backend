import React, { useState, useEffect } from 'react';
import { apiService } from '../services/api';

const ApiTest: React.FC = () => {
  const [apiStatus, setApiStatus] = useState<string>('Checking...');
  const [apiInfo, setApiInfo] = useState<any>(null);
  const [error, setError] = useState<string>('');

  useEffect(() => {
    testApiConnection();
  }, []);

  const testApiConnection = async () => {
    try {
      setApiStatus('Testing connection...');
      
      // Test basic API connection
      const info = await apiService.getApiInfo();
      setApiInfo(info);
      setApiStatus('Connected successfully!');
      setError('');
    } catch (err) {
      setApiStatus('Connection failed');
      setError(err instanceof Error ? err.message : 'Unknown error');
    }
  };

  const testHealthCheck = async () => {
    try {
      const health = await apiService.healthCheck();
      setApiStatus(`Health: ${health.status}`);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Health check failed');
    }
  };

  return (
    <div className="api-test">
      <h2>API Connection Test</h2>
      
      <div className="status-section">
        <h3>Status: {apiStatus}</h3>
        {error && <p className="error">Error: {error}</p>}
      </div>

      <div className="actions">
        <button onClick={testApiConnection} className="btn-primary">
          Test Connection
        </button>
        <button onClick={testHealthCheck} className="btn-secondary">
          Health Check
        </button>
      </div>

      {apiInfo && (
        <div className="api-info">
          <h3>API Information</h3>
          <pre>{JSON.stringify(apiInfo, null, 2)}</pre>
        </div>
      )}

      <div className="endpoints">
        <h3>Available Endpoints</h3>
        <ul>
          <li><strong>API Base:</strong> {import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'}</li>
          <li><strong>API Docs:</strong> <a href={`${import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'}/api`} target="_blank" rel="noopener noreferrer">View API Documentation</a></li>
          <li><strong>Health Check:</strong> /api (GET)</li>
          <li><strong>Authentication:</strong> /api/auth/login (POST)</li>
          <li><strong>Current User:</strong> /api/v1/me (GET)</li>
        </ul>
      </div>
    </div>
  );
};

export default ApiTest;
