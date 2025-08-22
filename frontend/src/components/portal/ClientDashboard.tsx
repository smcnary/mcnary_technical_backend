"use client";

import React from "react";
import { TrendingUp, TrendingDown, Phone, Eye, MapPin, RefreshCcw, AlertTriangle, Building2 } from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from "recharts";

function cx(...cls: (string | false | null | undefined)[]) {
  return cls.filter(Boolean).join(" ");
}

function KpiCard({ label, value, delta, icon: Icon, help }: { label: string; value: string | number; delta?: { value: number }; icon: React.ElementType; help?: string; }) {
  const isUp = (delta?.value ?? 0) >= 0;
  return (
    <div className="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow h-full">
      <div className="p-5 flex h-full flex-col justify-between">
        <div className="flex items-start justify-between">
          <div>
            <p className="text-sm font-medium text-slate-500">{label}</p>
            <div className="mt-2 flex items-baseline gap-3">
              <h3 className="text-3xl font-semibold tracking-tight text-slate-900">{value}</h3>
              {typeof delta?.value === "number" && (
                <span className={cx("inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium", isUp ? "bg-emerald-50 text-emerald-700" : "bg-rose-50 text-rose-700")}>
                  {isUp ? <TrendingUp className="h-3.5 w-3.5" /> : <TrendingDown className="h-3.5 w-3.5" />}
                  {isUp ? `+${delta!.value}%` : `-${Math.abs(delta!.value)}%`}
                </span>
              )}
            </div>
          </div>
          <div className="rounded-xl bg-slate-50 p-3 text-slate-500">
            <Icon className="h-5 w-5" />
          </div>
        </div>
        {help && <p className="mt-3 text-xs text-slate-500">{help}</p>}
      </div>
    </div>
  );
}



function WeeklyChart() {
  const [activeTab, setActiveTab] = React.useState<'weekly' | 'monthly' | 'yearly'>('weekly');

  // Sample data for different time periods
  const weeklyData = [
    {
      period: "Week 1",
      localVisibility: 68,
      gbpViews: 16500,
      phoneCalls: 312,
      leads: 115
    },
    {
      period: "Week 2", 
      localVisibility: 70,
      gbpViews: 17200,
      phoneCalls: 325,
      leads: 122
    },
    {
      period: "Week 3",
      localVisibility: 71,
      gbpViews: 17900,
      phoneCalls: 338,
      leads: 128
    },
    {
      period: "Week 4",
      localVisibility: 72,
      gbpViews: 18340,
      phoneCalls: 348,
      leads: 129
    }
  ];

  const monthlyData = [
    {
      period: "Jan",
      localVisibility: 65,
      gbpViews: 15800,
      phoneCalls: 298,
      leads: 108
    },
    {
      period: "Feb",
      localVisibility: 67,
      gbpViews: 16200,
      phoneCalls: 305,
      leads: 112
    },
    {
      period: "Mar",
      localVisibility: 69,
      gbpViews: 16800,
      phoneCalls: 318,
      leads: 118
    },
    {
      period: "Apr",
      localVisibility: 71,
      gbpViews: 17500,
      phoneCalls: 332,
      leads: 125
    },
    {
      period: "May",
      localVisibility: 73,
      gbpViews: 18100,
      phoneCalls: 345,
      leads: 131
    },
    {
      period: "Jun",
      localVisibility: 72,
      gbpViews: 18340,
      phoneCalls: 348,
      leads: 129
    }
  ];

  const yearlyData = [
    {
      period: "2022",
      localVisibility: 58,
      gbpViews: 12500,
      phoneCalls: 245,
      leads: 89
    },
    {
      period: "2023",
      localVisibility: 65,
      gbpViews: 15800,
      phoneCalls: 298,
      leads: 108
    },
    {
      period: "2024",
      localVisibility: 72,
      gbpViews: 18340,
      phoneCalls: 348,
      leads: 129
    }
  ];

  const getChartData = () => {
    switch (activeTab) {
      case 'weekly':
        return weeklyData;
      case 'monthly':
        return monthlyData;
      case 'yearly':
        return yearlyData;
      default:
        return weeklyData;
    }
  };



  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-sm font-semibold text-slate-900">Performance Trends</h3>
        
        {/* Tabs */}
        <div className="flex rounded-lg bg-slate-100 p-1">
          <button
            onClick={() => setActiveTab('weekly')}
            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
              activeTab === 'weekly'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Weekly
          </button>
          <button
            onClick={() => setActiveTab('monthly')}
            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
              activeTab === 'monthly'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Monthly
          </button>
          <button
            onClick={() => setActiveTab('yearly')}
            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
              activeTab === 'yearly'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Yearly
          </button>
        </div>
      </div>
      
      <div className="h-56 w-full">
        <ResponsiveContainer width="100%" height="100%">
          <LineChart data={getChartData()}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
            <XAxis 
              dataKey="period" 
              stroke="#64748b"
              fontSize={12}
              tickLine={false}
              axisLine={false}
            />
            <YAxis 
              stroke="#64748b"
              fontSize={12}
              tickLine={false}
              axisLine={false}
              tickFormatter={(value) => {
                // Format Y-axis labels based on the data range
                if (value >= 1000) return `${(value / 1000).toFixed(0)}k`;
                if (value >= 100) return `${value}`;
                return `${value}`;
              }}
            />
            <Tooltip 
              contentStyle={{
                backgroundColor: 'white',
                border: '1px solid #e2e8f0',
                borderRadius: '8px',
                boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
              }}
              formatter={(value, name) => {
                if (name === 'gbpViews') return [value.toLocaleString(), 'GBP Views'];
                if (name === 'phoneCalls') return [value, 'Phone Calls'];
                if (name === 'leads') return [value, 'Leads'];
                if (name === 'localVisibility') return [`${value}%`, 'Local Visibility'];
                return [value, name];
              }}
            />
            <Legend 
              wrapperStyle={{
                paddingTop: '10px',
                fontSize: '12px'
              }}
            />
            <Line 
              type="monotone" 
              dataKey="localVisibility" 
              stroke="#3b82f6" 
              strokeWidth={2}
              dot={{ fill: '#3b82f6', strokeWidth: 2, r: 4 }}
              activeDot={{ r: 6, stroke: '#3b82f6', strokeWidth: 2 }}
            />
            <Line 
              type="monotone" 
              dataKey="gbpViews" 
              stroke="#10b981" 
              strokeWidth={2}
              dot={{ fill: '#10b981', strokeWidth: 2, r: 4 }}
              activeDot={{ r: 6, stroke: '#10b981', strokeWidth: 2 }}
            />
            <Line 
              type="monotone" 
              dataKey="phoneCalls" 
              stroke="#f59e0b" 
              strokeWidth={2}
              dot={{ fill: '#f59e0b', strokeWidth: 2, r: 4 }}
              activeDot={{ r: 6, stroke: '#f59e0b', strokeWidth: 2 }}
            />
            <Line 
              type="monotone" 
              dataKey="leads" 
              stroke="#8b5cf6" 
              strokeWidth={2}
              dot={{ fill: '#8b5cf6', strokeWidth: 2, r: 4 }}
              activeDot={{ r: 6, stroke: '#8b5cf6', strokeWidth: 2 }}
            />
          </LineChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

export default function ClientDashboard() {
  return (
    <div className="min-h-screen bg-slate-100/70">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div className="flex gap-6">
          <main className="flex-1 py-8">
            {/* Status banner */}
            <div className="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
              <div className="flex items-start gap-3">
                <div className="mt-0.5 rounded-lg bg-white/60 p-1.5 text-amber-700">
                  <AlertTriangle className="h-4 w-4" />
                </div>
                <div className="flex-1">
                  <p className="font-medium">Dashboard Status</p>
                  <p className="mt-1 text-sm">Charts are disabled while we resolve a rendering issue. KPIs and data export remain available.</p>
                </div>
                <button className="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-white/70 px-3 py-1.5 text-sm font-medium hover:bg-white">
                  <RefreshCcw className="h-4 w-4" /> Check again
                </button>
              </div>
            </div>

            {/* KPI grid */}
            <section className="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
              <KpiCard label="Local Visibility" value={72} delta={{ value: 6 }} icon={MapPin} help="Average GBP position & local pack presence." />
              <KpiCard label="GBP Views" value={18340} delta={{ value: 12 }} icon={Eye} help="Total profile & search views across properties." />
              <KpiCard label="Phone Calls" value={348} delta={{ value: 4 }} icon={Phone} help="Tracked from call extensions and GBP taps." />
              <KpiCard label="Leads" value={129} delta={{ value: -3 }} icon={Building2} help="Form fills, booked appointments, and tracked calls." />
            </section>

            {/* Charts / modules */}
            <section className="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div className="lg:col-span-2">
                <WeeklyChart />
              </div>
              <div className="lg:col-span-1">
                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                  <h3 className="text-sm font-semibold text-slate-900">Recent Activity</h3>
                  <ul className="mt-4 space-y-3 text-sm">
                    <li className="flex items-start gap-3">
                      <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-emerald-500" />
                      <p className="text-slate-700"><strong>12 calls</strong> from GBP last 24h</p>
                    </li>
                    <li className="flex items-start gap-3">
                      <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-sky-500" />
                      <p className="text-slate-700"><strong>+38 views</strong> vs prior day</p>
                    </li>
                    <li className="flex items-start gap-3">
                      <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-violet-500" />
                      <p className="text-slate-700">New review received on South Tulsa location</p>
                    </li>
                  </ul>
                  <button className="mt-5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-white">View details</button>
                </div>
              </div>
            </section>

            {/* Table placeholder */}
            <section className="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div className="flex items-center justify-between">
                <h3 className="text-sm font-semibold text-slate-900">Leads</h3>
                <button className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-white">Manage columns</button>
              </div>

              <div className="mt-4 overflow-x-auto">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                  <thead className="bg-slate-50">
                    <tr>
                      <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                      <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600">Source</th>
                      <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600">Channel</th>
                      <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                      <th scope="col" className="px-4 py-3 text-right font-medium text-slate-600">Value</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {[1,2,3,4,5].map((i) => (
                      <tr key={i} className="hover:bg-slate-50/60">
                        <td className="px-4 py-3 text-slate-700">2025-08-1{i}</td>
                        <td className="px-4 py-3 text-slate-700">Google Business Profile</td>
                        <td className="px-4 py-3 text-slate-700">Call</td>
                        <td className="px-4 py-3"><span className="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Qualified</span></td>
                        <td className="px-4 py-3 text-right font-medium text-slate-900">$450</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              <div className="mt-4 flex items-center justify-between text-sm text-slate-600">
                <p>Showing 1–5 of 42</p>
                <div className="flex items-center gap-2">
                  <button className="rounded-lg border border-slate-200 bg-white px-3 py-1.5 hover:bg-slate-50">Prev</button>
                  <button className="rounded-lg border border-slate-200 bg-white px-3 py-1.5 hover:bg-slate-50">Next</button>
                </div>
              </div>
            </section>
          </main>
        </div>
      </div>
    </div>
  );
}
