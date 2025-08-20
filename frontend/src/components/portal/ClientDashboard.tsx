"use client";

import { useState } from "react";

export default function ClientDashboard() {
  const [range] = useState("Last 30 days");
  const [loc] = useState("All locations");

  return (
    <div className="min-h-screen bg-[#F5F7FB]">
      {/* Topbar */}
      <header className="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <span className="text-sm text-gray-500">Client Portal</span>
            <span className="text-gray-300">/</span>
            <h1 className="text-lg font-semibold text-gray-900">Dashboard</h1>
          </div>
          <div className="flex items-center gap-3">
            <span className="text-sm text-gray-600">Range: {range}</span>
            <span className="text-sm text-gray-600">Location: {loc}</span>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-6 py-8">
        {/* Simple KPI Cards */}
        <section className="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div className="rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">Local Visibility</p>
                <div className="mt-1 flex items-end gap-2">
                  <span className="text-2xl font-semibold text-gray-900">72</span>
                  <span className="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                    +6%
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <div className="rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">GBP Views</p>
                <div className="mt-1 flex items-end gap-2">
                  <span className="text-2xl font-semibold text-gray-900">18,340</span>
                  <span className="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                    +12%
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <div className="rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">Phone Calls</p>
                <div className="mt-1 flex items-end gap-2">
                  <span className="text-2xl font-semibold text-gray-900">348</span>
                  <span className="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                    +4%
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <div className="rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">Leads</p>
                <div className="mt-1 flex items-end gap-2">
                  <span className="text-2xl font-semibold text-gray-900">129</span>
                  <span className="inline-flex items-center gap-1 text-xs font-medium text-red-600">
                    -3%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Simple Content */}
        <section className="mt-6">
          <div className="rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-5">
            <h3 className="text-base font-semibold text-gray-900 mb-4">Dashboard Status</h3>
            <p className="text-gray-600">Dashboard is working! Charts will be added back once we resolve the rendering issues.</p>
          </div>
        </section>
      </main>
    </div>
  );
}
