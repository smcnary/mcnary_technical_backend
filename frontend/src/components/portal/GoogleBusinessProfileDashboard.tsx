import React from 'react';

interface GoogleBusinessProfileDashboardProps {
  className?: string;
}

export default function GoogleBusinessProfileDashboard({ className }: GoogleBusinessProfileDashboardProps) {
  return (
    <div className={`bg-white rounded-lg shadow-sm p-6 ${className || ''}`}>
      <h2 className="text-xl font-semibold text-gray-900 mb-4">Google Business Profile Dashboard</h2>
      <p className="text-gray-600">Google Business Profile dashboard coming soon...</p>
    </div>
  );
}
