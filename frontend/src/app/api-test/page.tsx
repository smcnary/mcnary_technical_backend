'use client'

import Header from '../../components/Header';

export default function ApiTestPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header onOpenLogin={() => {}} />

      <main className="flex-1">
        <div className="max-w-4xl mx-auto px-6 py-12">
          <h2 className="section-title">API Test</h2>
          <p className="section-subtitle">Test the connection to the backend API</p>
          <button className="btn-primary">Test Connection</button>
        </div>
      </main>
    </div>
  );
}
