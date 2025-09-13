"use client";
import { useState } from 'react';

export default function TestSimple() {
  const [testInput, setTestInput] = useState('');

  return (
    <div className="min-h-screen bg-gray-900 text-white p-8">
      <h1 className="text-2xl font-bold mb-4">Simple Test Page</h1>
      <div className="space-y-4">
        <input 
          type="text"
          value={testInput}
          onChange={(e) => setTestInput(e.target.value)}
          placeholder="Type something here..."
          className="px-4 py-2 bg-gray-800 border border-gray-600 rounded text-white"
        />
        <p>You typed: {testInput}</p>
        <button 
          onClick={() => setTestInput('Button clicked!')}
          className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Click me
        </button>
      </div>
    </div>
  );
}
