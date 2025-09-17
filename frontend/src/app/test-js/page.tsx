"use client";
import { useState } from 'react';

export default function TestJS() {
  const [count, setCount] = useState(0);
  const [message, setMessage] = useState('JavaScript is working!');

  const handleClick = () => {
    setCount(count + 1);
    setMessage(`Button clicked ${count + 1} times!`);
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#0c0a17] to-black text-white p-8">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">JavaScript Test</h1>
        
        <div className="space-y-6">
          <div className="bg-white/5 rounded-xl p-6 border border-white/10">
            <h2 className="text-xl font-semibold mb-4">Interactive Test</h2>
            <p className="text-white/80 mb-4">{message}</p>
            <button
              onClick={handleClick}
              className="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-lg font-medium transition"
            >
              Click Me (Count: {count})
            </button>
          </div>

          <div className="bg-white/5 rounded-xl p-6 border border-white/10">
            <h2 className="text-xl font-semibold mb-4">Environment Check</h2>
            <div className="space-y-2 text-sm">
              <div>NODE_ENV: {process.env.NODE_ENV}</div>
              <div>Stripe Key: {process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY ? 'Set' : 'Not Set'}</div>
              <div>Client Side: {typeof window !== 'undefined' ? 'Yes' : 'No'}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
