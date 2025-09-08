'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  LineChart, 
  Line, 
  AreaChart, 
  Area, 
  BarChart, 
  Bar, 
  PieChart, 
  Pie, 
  Cell,
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  Legend, 
  ResponsiveContainer,
  ScatterChart,
  Scatter
} from 'recharts';
import { 
  TrendingUp, 
  TrendingDown, 
  Users, 
  Target, 
  BarChart3, 
  PieChart as PieChartIcon,
  Calendar,
  Download,
  RefreshCw,
  Filter,
  Eye,
  Loader2,
  AlertCircle
} from 'lucide-react';

interface AnalyticsData {
  leads: {
    total: number;
    byStatus: { status: string; count: number }[];
    byMonth: { month: string; count: number }[];
    bySource: { source: string; count: number }[];
    conversionRate: number;
  };
  campaigns: {
    total: number;
    active: number;
    byType: { type: string; count: number }[];
    performance: { campaign: string; leads: number; conversions: number }[];
  };
  rankings: {
    averagePosition: number;
    topKeywords: { keyword: string; position: number; change: number }[];
    positionHistory: { date: string; position: number }[];
  };
  traffic: {
    totalViews: number;
    byMonth: { month: string; views: number; sessions: number }[];
    topPages: { page: string; views: number }[];
  };
}

export default function AnalyticsDashboard() {
  const { user } = useAuth();
  const { 
    leads, 
    campaigns, 
    getLeads, 
    getCampaigns,
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [analyticsData, setAnalyticsData] = useState<AnalyticsData | null>(null);
  const [timeRange, setTimeRange] = useState<'7d' | '30d' | '90d' | '1y'>('30d');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isLoadingLeads = getLoadingState('leads');
  const isLoadingCampaigns = getLoadingState('campaigns');

  useEffect(() => {
    loadAnalyticsData();
  }, [timeRange, leads, campaigns]);

  const loadAnalyticsData = async () => {
    setIsLoading(true);
    setError(null);

    try {
      // Load fresh data
      await Promise.all([
        getLeads({ per_page: 1000, sort: '-createdAt' }),
        getCampaigns({ per_page: 1000, sort: '-createdAt' })
      ]);

      // Process the data into analytics format
      const processedData = processAnalyticsData();
      setAnalyticsData(processedData);
    } catch (err: any) {
      setError(err?.message || 'Failed to load analytics data');
    } finally {
      setIsLoading(false);
    }
  };

  const processAnalyticsData = (): AnalyticsData => {
    const now = new Date();
    const timeRangeDays = {
      '7d': 7,
      '30d': 30,
      '90d': 90,
      '1y': 365
    }[timeRange];

    const startDate = new Date(now.getTime() - timeRangeDays * 24 * 60 * 60 * 1000);

    // Filter data by time range
    const filteredLeads = leads.filter(lead => 
      new Date(lead.createdAt) >= startDate
    );
    const filteredCampaigns = campaigns.filter(campaign => 
      new Date(campaign.createdAt) >= startDate
    );

    // Process leads data
    const leadsByStatus = filteredLeads.reduce((acc, lead) => {
      acc[lead.status] = (acc[lead.status] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);

    const leadsByMonth = filteredLeads.reduce((acc, lead) => {
      const month = new Date(lead.createdAt).toLocaleDateString('en-US', { month: 'short' });
      acc[month] = (acc[month] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);

    const leadsBySource = filteredLeads.reduce((acc, lead) => {
      const source = lead.practiceAreas?.[0] || 'Unknown';
      acc[source] = (acc[source] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);

    const qualifiedLeads = filteredLeads.filter(lead => lead.status === 'qualified').length;
    const conversionRate = filteredLeads.length > 0 ? (qualifiedLeads / filteredLeads.length) * 100 : 0;

    // Process campaigns data
    const campaignsByType = filteredCampaigns.reduce((acc, campaign) => {
      acc[campaign.type] = (acc[campaign.type] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);

    const campaignPerformance = filteredCampaigns.map(campaign => ({
      campaign: campaign.name,
      leads: Math.floor(Math.random() * 50) + 10, // Mock data - would come from actual campaign tracking
      conversions: Math.floor(Math.random() * 20) + 5
    }));

    // Mock rankings data (would come from actual ranking API)
    const mockRankings = {
      averagePosition: 15.2,
      topKeywords: [
        { keyword: 'personal injury lawyer', position: 3, change: 2 },
        { keyword: 'car accident attorney', position: 7, change: -1 },
        { keyword: 'workers compensation', position: 12, change: 3 },
        { keyword: 'medical malpractice', position: 18, change: -2 },
        { keyword: 'slip and fall lawyer', position: 22, change: 1 }
      ],
      positionHistory: Array.from({ length: 30 }, (_, i) => ({
        date: new Date(now.getTime() - (29 - i) * 24 * 60 * 60 * 1000).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
        position: 20 + Math.sin(i / 5) * 5 + Math.random() * 3
      }))
    };

    // Mock traffic data (would come from Google Analytics API)
    const mockTraffic = {
      totalViews: 15420,
      byMonth: Object.entries(leadsByMonth).map(([month, count]) => ({
        month,
        views: count * 45 + Math.floor(Math.random() * 200),
        sessions: count * 32 + Math.floor(Math.random() * 150)
      })),
      topPages: [
        { page: '/practice-areas/personal-injury', views: 3240 },
        { page: '/practice-areas/car-accidents', views: 2890 },
        { page: '/contact', views: 2150 },
        { page: '/about', views: 1890 },
        { page: '/practice-areas/workers-comp', views: 1650 }
      ]
    };

    return {
      leads: {
        total: filteredLeads.length,
        byStatus: Object.entries(leadsByStatus).map(([status, count]) => ({ status, count })),
        byMonth: Object.entries(leadsByMonth).map(([month, count]) => ({ month, count })),
        bySource: Object.entries(leadsBySource).map(([source, count]) => ({ source, count })),
        conversionRate
      },
      campaigns: {
        total: filteredCampaigns.length,
        active: filteredCampaigns.filter(c => c.status === 'active').length,
        byType: Object.entries(campaignsByType).map(([type, count]) => ({ type, count })),
        performance: campaignPerformance
      },
      rankings: mockRankings,
      traffic: mockTraffic
    };
  };

  const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

  if (isLoading || isLoadingLeads || isLoadingCampaigns) {
    return (
      <div className="flex items-center justify-center py-12">
        <Loader2 className="h-8 w-8 animate-spin mr-2" />
        <span>Loading analytics data...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center py-12 text-red-600">
        <AlertCircle className="h-8 w-8 mr-2" />
        <span>{error}</span>
      </div>
    );
  }

  if (!analyticsData) {
    return (
      <div className="text-center py-12">
        <BarChart3 className="h-12 w-12 mx-auto mb-4 text-gray-400" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">No Analytics Data</h3>
        <p className="text-gray-600">Start creating campaigns and tracking leads to see analytics.</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Analytics Dashboard</h2>
          <p className="text-gray-600">Comprehensive insights into your SEO performance</p>
        </div>
        <div className="flex items-center gap-2">
          <Select value={timeRange} onValueChange={(value: any) => setTimeRange(value)}>
            <SelectTrigger className="w-32">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="7d">Last 7 days</SelectItem>
              <SelectItem value="30d">Last 30 days</SelectItem>
              <SelectItem value="90d">Last 90 days</SelectItem>
              <SelectItem value="1y">Last year</SelectItem>
            </SelectContent>
          </Select>
          <Button variant="outline" onClick={loadAnalyticsData}>
            <RefreshCw className="h-4 w-4 mr-2" />
            Refresh
          </Button>
          <Button variant="outline">
            <Download className="h-4 w-4 mr-2" />
            Export
          </Button>
        </div>
      </div>

      {/* Key Metrics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Leads</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analyticsData.leads.total}</div>
            <p className="text-xs text-muted-foreground">
              {timeRange} period
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Conversion Rate</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analyticsData.leads.conversionRate.toFixed(1)}%</div>
            <p className="text-xs text-muted-foreground">
              Lead to qualified
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Campaigns</CardTitle>
            <BarChart3 className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analyticsData.campaigns.active}</div>
            <p className="text-xs text-muted-foreground">
              Currently running
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Avg. Position</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analyticsData.rankings.averagePosition.toFixed(1)}</div>
            <p className="text-xs text-muted-foreground">
              Top keywords
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Charts Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Leads Over Time */}
        <Card>
          <CardHeader>
            <CardTitle>Leads Over Time</CardTitle>
            <CardDescription>Lead generation trends by month</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={analyticsData.leads.byMonth}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="month" />
                  <YAxis />
                  <Tooltip />
                  <Area 
                    type="monotone" 
                    dataKey="count" 
                    stroke="#3b82f6" 
                    fill="#3b82f6" 
                    fillOpacity={0.3}
                  />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        {/* Lead Sources */}
        <Card>
          <CardHeader>
            <CardTitle>Lead Sources</CardTitle>
            <CardDescription>Leads by practice area</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={analyticsData.leads.bySource}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={({ source, percent }) => `${source} ${(percent * 100).toFixed(0)}%`}
                    outerRadius={80}
                    fill="#8884d8"
                    dataKey="count"
                  >
                    {analyticsData.leads.bySource.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        {/* Campaign Performance */}
        <Card>
          <CardHeader>
            <CardTitle>Campaign Performance</CardTitle>
            <CardDescription>Leads and conversions by campaign</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={analyticsData.campaigns.performance}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="campaign" />
                  <YAxis />
                  <Tooltip />
                  <Legend />
                  <Bar dataKey="leads" fill="#3b82f6" name="Leads" />
                  <Bar dataKey="conversions" fill="#10b981" name="Conversions" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        {/* Keyword Rankings */}
        <Card>
          <CardHeader>
            <CardTitle>Keyword Rankings</CardTitle>
            <CardDescription>Average position over time</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={analyticsData.rankings.positionHistory}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="date" />
                  <YAxis reversed />
                  <Tooltip />
                  <Line 
                    type="monotone" 
                    dataKey="position" 
                    stroke="#f59e0b" 
                    strokeWidth={2}
                    dot={{ fill: '#f59e0b', strokeWidth: 2, r: 4 }}
                  />
                </LineChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Top Keywords Table */}
      <Card>
        <CardHeader>
          <CardTitle>Top Performing Keywords</CardTitle>
          <CardDescription>Your best ranking keywords and their changes</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="text-left py-2">Keyword</th>
                  <th className="text-left py-2">Position</th>
                  <th className="text-left py-2">Change</th>
                  <th className="text-left py-2">Status</th>
                </tr>
              </thead>
              <tbody>
                {analyticsData.rankings.topKeywords.map((keyword, index) => (
                  <tr key={index} className="border-b">
                    <td className="py-2 font-medium">{keyword.keyword}</td>
                    <td className="py-2">#{keyword.position}</td>
                    <td className="py-2">
                      <div className="flex items-center gap-1">
                        {keyword.change > 0 ? (
                          <TrendingUp className="h-4 w-4 text-green-500" />
                        ) : (
                          <TrendingDown className="h-4 w-4 text-red-500" />
                        )}
                        <span className={keyword.change > 0 ? 'text-green-600' : 'text-red-600'}>
                          {Math.abs(keyword.change)}
                        </span>
                      </div>
                    </td>
                    <td className="py-2">
                      <Badge variant={keyword.position <= 10 ? 'default' : 'secondary'}>
                        {keyword.position <= 10 ? 'Top 10' : 'Page 2+'}
                      </Badge>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
