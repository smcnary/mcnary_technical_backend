"use client";

import React from "react";
import { useEffect, useState } from "react";
import api, { ApiResponse, Lead } from "@/services/api";
import { TrendingUp, TrendingDown, Phone, Eye, MapPin, Building2, Users, Target } from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from "recharts";
import UserGreeting from "./UserGreeting";
import { useAuth } from "@/hooks/useAuth";
import { useTheme } from "@/contexts/ThemeContext";
import { useOnboarding } from "@/contexts/OnboardingContext";
import { useData } from "@/hooks/useData";
import { ThemeToggle } from "@/components/ui/theme-toggle";
import OnboardingModal from "@/components/onboarding/OnboardingModal";
import DashboardTour from "@/components/onboarding/DashboardTour";

function cx(...cls: (string | false | null | undefined)[]) {
  return cls.filter(Boolean).join(" ");
}

function KpiCard({ label, value, delta, icon: Icon, help }: { label: string; value: string | number; delta?: { value: number }; icon: React.ElementType; help?: string; }) {
  const isUp = (delta?.value ?? 0) >= 0;
  return (
    <div className="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm hover:shadow-md transition-shadow h-full">
      <div className="p-5 flex h-full flex-col justify-between">
        <div className="flex items-start justify-between">
          <div>
            <p className="text-sm font-medium text-slate-500 dark:text-slate-400">{label}</p>
            <div className="mt-2 flex items-baseline gap-3">
              <h3 className="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">{value}</h3>
              {typeof delta?.value === "number" && (
                <span className={cx("inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium", isUp ? "bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400" : "bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400")}>
                  {isUp ? <TrendingUp className="h-3.5 w-3.5" /> : <TrendingDown className="h-3.5 w-3.5" />}
                  {isUp ? `+${delta!.value}%` : `-${Math.abs(delta!.value)}%`}
                </span>
              )}
            </div>
          </div>
          <div className="rounded-xl bg-slate-50 dark:bg-slate-700 p-3 text-slate-500 dark:text-slate-400">
            <Icon className="h-5 w-5" />
          </div>
        </div>
        {help && <p className="mt-3 text-xs text-slate-500 dark:text-slate-400">{help}</p>}
      </div>
    </div>
  );
}



function WeeklyChart({ leads, campaigns }: { leads: any[], campaigns: any[] }) {
  const [activeTab, setActiveTab] = React.useState<'weekly' | 'monthly' | 'yearly'>('weekly');

  // Generate real data based on actual leads and campaigns
  const generateChartData = () => {
    const now = new Date();
    const dataPoints = activeTab === 'weekly' ? 4 : activeTab === 'monthly' ? 6 : 3;
    
    const data = [];
    for (let i = dataPoints - 1; i >= 0; i--) {
      let period: string;
      let periodStart: Date;
      
      if (activeTab === 'weekly') {
        periodStart = new Date(now.getTime() - i * 7 * 24 * 60 * 60 * 1000);
        period = `Week ${dataPoints - i}`;
      } else if (activeTab === 'monthly') {
        periodStart = new Date(now.getFullYear(), now.getMonth() - i, 1);
        period = periodStart.toLocaleDateString('en-US', { month: 'short' });
      } else {
        periodStart = new Date(now.getFullYear() - i, 0, 1);
        period = periodStart.getFullYear().toString();
      }
      
      const periodEnd = new Date(periodStart.getTime() + (activeTab === 'weekly' ? 7 : activeTab === 'monthly' ? 30 : 365) * 24 * 60 * 60 * 1000);
      
      // Filter leads for this period
      const periodLeads = leads.filter(lead => {
        const leadDate = new Date(lead.createdAt);
        return leadDate >= periodStart && leadDate < periodEnd;
      });
      
      // Calculate metrics based on real data
      const leadCount = periodLeads.length;
      const qualifiedLeads = periodLeads.filter(lead => lead.status === 'qualified').length;
      const conversionRate = leadCount > 0 ? (qualifiedLeads / leadCount) * 100 : 0;
      
      // Mock additional metrics (would come from Google Analytics, GBP, etc.)
      const baseViews = Math.floor(leadCount * 45 + Math.random() * 200);
      const baseCalls = Math.floor(leadCount * 3 + Math.random() * 20);
      
      data.push({
        period,
        localVisibility: Math.min(100, Math.max(0, 60 + conversionRate * 0.5 + Math.random() * 10)),
        gbpViews: baseViews,
        phoneCalls: baseCalls,
        leads: leadCount,
        conversions: qualifiedLeads
      });
    }
    
    return data;
  };

  const chartData = generateChartData();



  return (
    <div className="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-sm font-semibold text-slate-900 dark:text-white">Performance Trends</h3>
        
        {/* Tabs */}
        <div className="flex rounded-lg bg-slate-100 dark:bg-slate-700 p-1">
          <button
            onClick={() => setActiveTab('weekly')}
            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
              activeTab === 'weekly'
                ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
            }`}
          >
            Weekly
          </button>
          <button
            onClick={() => setActiveTab('monthly')}
            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
              activeTab === 'monthly'
                ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
            }`}
          >
            Monthly
          </button>
          <button
            onClick={() => setActiveTab('yearly')}
            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
              activeTab === 'yearly'
                ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm'
                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
            }`}
          >
            Yearly
          </button>
        </div>
      </div>
      
      <div className="h-56 w-full">
        <ResponsiveContainer width="100%" height="100%">
          <LineChart data={chartData}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" className="dark:stroke-slate-600" />
            <XAxis 
              dataKey="period" 
              stroke="#64748b"
              className="dark:stroke-slate-400"
              fontSize={12}
              tickLine={false}
              axisLine={false}
            />
            <YAxis 
              stroke="#64748b"
              className="dark:stroke-slate-400"
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
                boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                color: '#1e293b'
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
              className="dark:text-white"
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
  const { user } = useAuth();
  const { theme } = useTheme();
  const { isOnboardingActive } = useOnboarding();
  const { 
    leads, 
    campaigns, 
    caseStudies,
    getLeads, 
    getCampaigns, 
    getCaseStudies,
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();
  
  const [gbpData, setGbpData] = useState<any>(null);
  const [isLoadingGbp, setIsLoadingGbp] = useState<boolean>(false);
  const [gbpError, setGbpError] = useState<string | null>(null);
  const [showOnboarding, setShowOnboarding] = useState(false);
  const [showTour, setShowTour] = useState(false);

  const getUserInitials = (name?: string) => {
    if (!name) return 'U';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
  };

  const isLoadingLeads = getLoadingState('leads');
  const leadsError = getErrorState('leads');
  const isLoadingCampaigns = getLoadingState('campaigns');
  const isLoadingCaseStudies = getLoadingState('caseStudies');

  useEffect(() => {
    let isMounted = true;
    
    async function loadDashboardData() {
      try {
        // Load leads, campaigns, and case studies in parallel
        await Promise.all([
          getLeads({ per_page: 5, page: 1, sort: "-createdAt" }),
          getCampaigns({ per_page: 3, page: 1, sort: "-createdAt" }),
          getCaseStudies()
        ]);
      } catch (err) {
        console.error('Failed to load dashboard data:', err);
      }
    }

    async function loadGbpData() {
      if (!user?.clientId) return;
      
      setIsLoadingGbp(true);
      setGbpError(null);
      try {
        const gbpResponse = await api.getGbpKpi(user.clientId);
        if (isMounted) setGbpData(gbpResponse);
      } catch (err: any) {
        if (isMounted) setGbpError(err?.message || "Failed to load GBP data");
      } finally {
        if (isMounted) setIsLoadingGbp(false);
      }
    }

    loadDashboardData();
    loadGbpData();
    
    return () => {
      isMounted = false;
    };
  }, [user?.clientId, getLeads, getCampaigns, getCaseStudies]);

  // Handle onboarding flow
  useEffect(() => {
    if (isOnboardingActive) {
      setShowOnboarding(true);
    }
  }, [isOnboardingActive]);

  const handleOnboardingComplete = () => {
    setShowOnboarding(false);
    // Show tour after onboarding completion
    setTimeout(() => {
      setShowTour(true);
    }, 1000);
  };

  const handleTourComplete = () => {
    setShowTour(false);
  };

  return (
    <div className="min-h-screen bg-slate-100/70 dark:bg-slate-900">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div className="flex gap-6">
          <main className="flex-1 py-8">
            {/* Header with theme toggle */}
            <div className="flex items-center justify-between mb-6" data-tour="dashboard-header">
              <UserGreeting 
                fallbackData={{
                  userName: "John Doe",
                  organizationName: "McNary SEO Services",
                  userRole: "Client Admin"
                }}
              />
              <ThemeToggle />
            </div>

            {/* KPI grid */}
            <section className="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4" data-tour="kpi-cards">
              <KpiCard 
                label="Total Leads" 
                value={leads.length} 
                delta={{ value: leads.filter(l => new Date(l.createdAt) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)).length }} 
                icon={Users} 
                help="Total leads in your system." 
              />
              <KpiCard 
                label="Qualified Leads" 
                value={leads.filter(l => l.status === 'qualified').length} 
                delta={{ value: leads.filter(l => l.status === 'qualified' && new Date(l.createdAt) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)).length }} 
                icon={Target} 
                help="Leads that have been qualified." 
              />
              <KpiCard 
                label="Active Campaigns" 
                value={campaigns.filter(c => c.status === 'active').length} 
                delta={{ value: campaigns.filter(c => c.status === 'active' && new Date(c.createdAt) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)).length }} 
                icon={TrendingUp} 
                help="Currently running marketing campaigns." 
              />
              <KpiCard 
                label="Conversion Rate" 
                value={`${leads.length > 0 ? ((leads.filter(l => l.status === 'qualified').length / leads.length) * 100).toFixed(1) : 0}%`} 
                delta={{ value: 2.3 }} 
                icon={Building2} 
                help="Percentage of leads that convert to qualified." 
              />
            </section>

            {/* Charts / modules */}
            <section className="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div className="lg:col-span-2" data-tour="performance-chart">
                <WeeklyChart leads={leads} campaigns={campaigns} />
              </div>
              <div className="lg:col-span-1" data-tour="activity-feed">
                <div className="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
                  <h3 className="text-sm font-semibold text-slate-900 dark:text-white">Recent Activity</h3>
                  <ul className="mt-4 space-y-3 text-sm">
                    {leads.length > 0 ? (
                      <li className="flex items-start gap-3">
                        <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-emerald-500" />
                        <p className="text-slate-700 dark:text-slate-300">
                          <strong>{leads.length} total leads</strong> in your system
                        </p>
                      </li>
                    ) : (
                      <li className="flex items-start gap-3">
                        <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-emerald-500" />
                        <p className="text-slate-700 dark:text-slate-300">No leads yet - start tracking!</p>
                      </li>
                    )}
                    
                    {leads.filter(l => l.status === 'qualified').length > 0 ? (
                      <li className="flex items-start gap-3">
                        <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-purple-500" />
                        <p className="text-slate-700 dark:text-slate-300">
                          <strong>{leads.filter(l => l.status === 'qualified').length} qualified leads</strong> ready to convert
                        </p>
                      </li>
                    ) : (
                      <li className="flex items-start gap-3">
                        <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-purple-500" />
                        <p className="text-slate-700 dark:text-slate-300">Work on qualifying your leads</p>
                      </li>
                    )}
                    
                    <li className="flex items-start gap-3">
                      <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-orange-500" />
                      <p className="text-slate-700 dark:text-slate-300">
                        <strong>{campaigns.filter(c => c.status === 'active').length} active campaigns</strong> running
                      </p>
                    </li>
                  </ul>
                  <button className="mt-5 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-600">View details</button>
                </div>
              </div>
            </section>

            {/* Leads table */}
            <section className="mt-8 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm" data-tour="leads-table">
              <div className="flex items-center justify-between">
                <h3 className="text-sm font-semibold text-slate-900 dark:text-white">Leads</h3>
                <button className="rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 px-3 py-1.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-600">Manage columns</button>
              </div>

              <div className="mt-4 overflow-x-auto">
                {isLoadingLeads ? (
                  <div className="py-8 text-center text-slate-500 dark:text-slate-400 text-sm">Loading leads…</div>
                ) : leadsError ? (
                  <div className="py-8 text-center text-rose-600 dark:text-rose-400 text-sm">{leadsError}</div>
                ) : (
                  <table className="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    <thead className="bg-slate-50 dark:bg-slate-700">
                      <tr>
                        <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600 dark:text-slate-300">Date</th>
                        <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600 dark:text-slate-300">Name</th>
                        <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600 dark:text-slate-300">Channel</th>
                        <th scope="col" className="px-4 py-3 text-left font-medium text-slate-600 dark:text-slate-300">Status</th>
                        <th scope="col" className="px-4 py-3 text-right font-medium text-slate-600 dark:text-slate-300">Email</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100 dark:divide-slate-700">
                      {leads.length === 0 ? (
                        <tr>
                          <td colSpan={5} className="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No leads yet</td>
                        </tr>
                      ) : (
                        leads.map((lead) => (
                          <tr key={lead.id} className="hover:bg-slate-50/60 dark:hover:bg-slate-700/60">
                            <td className="px-4 py-3 text-slate-700 dark:text-slate-300">{new Date(lead.createdAt).toLocaleDateString()}</td>
                            <td className="px-4 py-3 text-slate-700 dark:text-slate-300">{lead.name || '—'}</td>
                            <td className="px-4 py-3 text-slate-700 dark:text-slate-300">{lead.practiceAreas?.[0] || '—'}</td>
                            <td className="px-4 py-3">
                              <span className="inline-flex rounded-full bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:text-emerald-400 capitalize">
                                {lead.status}
                              </span>
                            </td>
                            <td className="px-4 py-3 text-right font-medium text-slate-900 dark:text-white">{lead.email}</td>
                          </tr>
                        ))
                      )}
                    </tbody>
                  </table>
                )}
              </div>

              <div className="mt-4 flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
                <p>Showing 1–5 of 42</p>
                <div className="flex items-center gap-2">
                  <button className="rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300">Prev</button>
                  <button className="rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300">Next</button>
                </div>
              </div>
            </section>
          </main>
        </div>
      </div>

      {/* Onboarding Modal */}
      <OnboardingModal 
        isOpen={showOnboarding} 
        onClose={handleOnboardingComplete} 
      />

      {/* Dashboard Tour */}
      <DashboardTour 
        isOpen={showTour} 
        onClose={handleTourComplete}
        onComplete={handleTourComplete}
      />
    </div>
  );
}
