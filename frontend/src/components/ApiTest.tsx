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
    <div className="max-w-4xl mx-auto px-6 py-12">
      <h2 className="section-title">API Connection Test</h2>
      
      <div className="bg-gray-50 rounded-lg p-6 mb-8">
        <h3 className="text-lg font-semibold text-gray-900 mb-2">Status: {apiStatus}</h3>
        {error && <p className="text-red-600 font-medium">Error: {error}</p>}
      </div>

      <div className="flex flex-col sm:flex-row gap-4 mb-8">
        <button onClick={testApiConnection} className="btn-primary">
          Test Connection
        </button>
        <button onClick={testHealthCheck} className="btn-secondary">
          Health Check
        </button>
      </div>

      {apiInfo && (
        <div className="card mb-8">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">API Information</h3>
          <pre className="bg-gray-100 rounded-lg p-4 overflow-x-auto text-sm">{JSON.stringify(apiInfo, null, 2)}</pre>
        </div>
      )}

      <div className="card">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Available Endpoints</h3>
        <ul className="space-y-2 text-gray-700">
          <li><strong>API Base:</strong> {import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'}</li>
          <li><strong>API Docs:</strong> <a href={`${import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'}/api`} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:text-blue-800 underline">View API Documentation</a></li>
          <li><strong>Health Check:</strong> /api (GET)</li>
          <li><strong>Authentication:</strong> /api/auth/login (POST)</li>
          <li><strong>Current User:</strong> /api/v1/me (GET)</li>
        </ul>
      </div>
    </div>
  );
};

export default ApiTest;
