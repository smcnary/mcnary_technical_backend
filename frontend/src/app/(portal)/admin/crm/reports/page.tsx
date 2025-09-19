'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
  TrendingUp,
  TrendingDown,
  Users,
  Building2,
  Target,
  DollarSign,
  Phone,
  Mail,
  Calendar,
  CheckCircle,
  Clock,
  AlertCircle,
  RefreshCw,
  Download,
  BarChart3,
  PieChart,
  LineChart,
  Activity,
  User,
  Mail as MailIcon,
  PhoneCall,
  MessageSquare,
  Star,
  Award,
  Zap
} from 'lucide-react';

export default function CrmReports() {
  const { user, isAuthenticated, isAdmin, isSalesConsultant } = useAuth();
  const {
    clients,
    leads,
    campaigns,
    getClients,
    getLeads,
    getCampaigns,
    getLoadingState,
    getErrorState,
    clearError,
  } = useData();

  const [dateRange, setDateRange] = useState('30');
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [activeTab, setActiveTab] = useState('overview');

  // Load data on component mount
  useEffect(() => {
    if (isAuthenticated && (isAdmin() || isSalesConsultant())) {
      loadInitialData();
    }
  }, [isAuthenticated, isAdmin, isSalesConsultant]);

  const loadInitialData = async () => {
    try {
      await Promise.all([
        getClients(),
        getLeads(),
        getCampaigns(),
      ]);
    } catch (error) {
      console.error('Failed to load initial data:', error);
    }
  };

  const handleRefresh = async () => {
    setIsRefreshing(true);
    try {
      await loadInitialData();
    } catch (error) {
      console.error('Failed to refresh data:', error);
    } finally {
      setIsRefreshing(false);
    }
  };

  const handleExportReport = (reportType: string) => {
    // TODO: Implement report export functionality
    console.log(`Exporting ${reportType} report`);
  };

  // Calculate metrics based on date range
  const getDateFilteredData = (data: any[], dateField: string = 'createdAt') => {
    const days = parseInt(dateRange);
    const cutoffDate = new Date();
    cutoffDate.setDate(cutoffDate.getDate() - days);
    
    return data.filter(item => new Date(item[dateField]) >= cutoffDate);
  };

  const recentLeads = getDateFilteredData(leads);
  const recentClients = getDateFilteredData(clients);
  const recentCampaigns = getDateFilteredData(campaigns);

  // Calculate key metrics
  const totalLeads = recentLeads.length;
  const newLeads = recentLeads.filter(lead => lead.status === 'new').length;
  const qualifiedLeads = recentLeads.filter(lead => lead.status === 'qualified').length;
  const convertedLeads = recentLeads.filter(lead => lead.status === 'converted').length;
  const totalClients = recentClients.length;
  const activeClients = recentClients.filter(client => client.status === 'active').length;
  const totalCampaigns = recentCampaigns.length;
  const activeCampaigns = recentCampaigns.filter(campaign => campaign.status === 'active').length;

  // Calculate conversion rates
  const leadToQualifiedRate = totalLeads > 0 ? Math.round((qualifiedLeads / totalLeads) * 100) : 0;
  const leadToConvertedRate = totalLeads > 0 ? Math.round((convertedLeads / totalLeads) * 100) : 0;
  const qualifiedToConvertedRate = qualifiedLeads > 0 ? Math.round((convertedLeads / qualifiedLeads) * 100) : 0;

  // Lead source analysis
  const leadSources = recentLeads.reduce((acc, lead) => {
    const source = lead.source?.name || 'Unknown';
    acc[source] = (acc[source] || 0) + 1;
    return acc;
  }, {} as Record<string, number>);

  // Industry analysis
  const industryAnalysis = recentClients.reduce((acc, client) => {
    acc[client.industry] = (acc[client.industry] || 0) + 1;
    return acc;
  }, {} as Record<string, number>);

  // Campaign type analysis
  const campaignTypeAnalysis = recentCampaigns.reduce((acc, campaign) => {
    acc[campaign.type] = (acc[campaign.type] || 0) + 1;
    return acc;
  }, {} as Record<string, number>);

  if (!isAuthenticated) {
    return <div>Please log in to access CRM reports.</div>;
  }

  if (!isAdmin() && !isSalesConsultant()) {
    return <div>Access denied. You need admin or sales consultant permissions to access CRM reports.</div>;
  }

  return (
    <div className="space-y-6">
      <PageHeader 
        title="CRM Reports & Analytics" 
        description="Comprehensive reports and analytics for your CRM data"
        action={
          <div className="flex gap-2">
            <Select value={dateRange} onValueChange={setDateRange}>
              <SelectTrigger className="w-[140px]">
                <SelectValue placeholder="Date Range" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="7">Last 7 days</SelectItem>
                <SelectItem value="30">Last 30 days</SelectItem>
                <SelectItem value="90">Last 90 days</SelectItem>
                <SelectItem value="365">Last year</SelectItem>
              </SelectContent>
            </Select>
            <Button variant="outline" size="sm" onClick={handleRefresh} disabled={isRefreshing}>
              <RefreshCw className={`w-4 h-4 mr-2 ${isRefreshing ? 'animate-spin' : ''}`} />
              Refresh
            </Button>
            <Button size="sm">
              <Download className="w-4 h-4 mr-2" />
              Export
            </Button>
          </div>
        }
      />

      {/* Key Metrics Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Leads</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{totalLeads}</div>
            <p className="text-xs text-muted-foreground">
              +{newLeads} new leads
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Conversion Rate</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{leadToConvertedRate}%</div>
            <p className="text-xs text-muted-foreground">
              {qualifiedToConvertedRate}% qualified to converted
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Clients</CardTitle>
            <Building2 className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activeClients}</div>
            <p className="text-xs text-muted-foreground">
              {totalClients} total clients
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Campaigns</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activeCampaigns}</div>
            <p className="text-xs text-muted-foreground">
              {totalCampaigns} total campaigns
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Main Reports Tabs */}
      <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-4">
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="leads">Leads</TabsTrigger>
          <TabsTrigger value="clients">Clients</TabsTrigger>
          <TabsTrigger value="campaigns">Campaigns</TabsTrigger>
        </TabsList>

        {/* Overview Tab */}
        <TabsContent value="overview" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2">
            <Card>
              <CardHeader>
                <CardTitle>Lead Funnel</CardTitle>
                <CardDescription>Lead progression through the sales funnel</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <Users className="w-4 h-4 text-muted-foreground" />
                    <span>New Leads</span>
                  </div>
                  <div className="text-right">
                    <div className="font-bold">{newLeads}</div>
                    <div className="text-xs text-muted-foreground">100%</div>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <CheckCircle className="w-4 h-4 text-muted-foreground" />
                    <span>Qualified</span>
                  </div>
                  <div className="text-right">
                    <div className="font-bold">{qualifiedLeads}</div>
                    <div className="text-xs text-muted-foreground">{leadToQualifiedRate}%</div>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <Award className="w-4 h-4 text-muted-foreground" />
                    <span>Converted</span>
                  </div>
                  <div className="text-right">
                    <div className="font-bold">{convertedLeads}</div>
                    <div className="text-xs text-muted-foreground">{leadToConvertedRate}%</div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Lead Sources</CardTitle>
                <CardDescription>Where leads are coming from</CardDescription>
              </CardHeader>
              <CardContent className="space-y-3">
                {Object.entries(leadSources).map(([source, count]) => {
                  const percentage = totalLeads > 0 ? Math.round((count / totalLeads) * 100) : 0;
                  return (
                    <div key={source} className="flex items-center justify-between">
                      <span className="text-sm">{source}</span>
                      <div className="flex items-center space-x-2">
                        <div className="w-16 bg-muted rounded-full h-2">
                          <div 
                            className="bg-primary h-2 rounded-full" 
                            style={{ width: `${percentage}%` }}
                          ></div>
                        </div>
                        <span className="text-sm font-medium w-8 text-right">{count}</span>
                      </div>
                    </div>
                  );
                })}
              </CardContent>
            </Card>
          </div>

          <div className="grid gap-4 md:grid-cols-2">
            <Card>
              <CardHeader>
                <CardTitle>Industry Distribution</CardTitle>
                <CardDescription>Client distribution by industry</CardDescription>
              </CardHeader>
              <CardContent className="space-y-3">
                {Object.entries(industryAnalysis).map(([industry, count]) => {
                  const percentage = totalClients > 0 ? Math.round((count / totalClients) * 100) : 0;
                  return (
                    <div key={industry} className="flex items-center justify-between">
                      <span className="text-sm capitalize">{industry}</span>
                      <div className="flex items-center space-x-2">
                        <div className="w-16 bg-muted rounded-full h-2">
                          <div 
                            className="bg-primary h-2 rounded-full" 
                            style={{ width: `${percentage}%` }}
                          ></div>
                        </div>
                        <span className="text-sm font-medium w-8 text-right">{count}</span>
                      </div>
                    </div>
                  );
                })}
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Campaign Types</CardTitle>
                <CardDescription>Distribution of campaign types</CardDescription>
              </CardHeader>
              <CardContent className="space-y-3">
                {Object.entries(campaignTypeAnalysis).map(([type, count]) => {
                  const percentage = totalCampaigns > 0 ? Math.round((count / totalCampaigns) * 100) : 0;
                  return (
                    <div key={type} className="flex items-center justify-between">
                      <span className="text-sm capitalize">{type}</span>
                      <div className="flex items-center space-x-2">
                        <div className="w-16 bg-muted rounded-full h-2">
                          <div 
                            className="bg-primary h-2 rounded-full" 
                            style={{ width: `${percentage}%` }}
                          ></div>
                        </div>
                        <span className="text-sm font-medium w-8 text-right">{count}</span>
                      </div>
                    </div>
                  );
                })}
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Leads Tab */}
        <TabsContent value="leads" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-3">
            <Card>
              <CardHeader>
                <CardTitle>Lead Status Breakdown</CardTitle>
              </CardHeader>
              <CardContent className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-sm">New</span>
                  <Badge variant="secondary">{newLeads}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Qualified</span>
                  <Badge variant="default">{qualifiedLeads}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Converted</span>
                  <Badge variant="default">{convertedLeads}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Lost</span>
                  <Badge variant="destructive">{recentLeads.filter(l => l.status === 'lost').length}</Badge>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Lead Quality Score</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center">
                  <div className="text-3xl font-bold text-green-600">
                    {Math.round((qualifiedLeads + convertedLeads) / Math.max(totalLeads, 1) * 100)}%
                  </div>
                  <p className="text-sm text-muted-foreground">Quality Score</p>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Top Lead Sources</CardTitle>
              </CardHeader>
              <CardContent className="space-y-2">
                {Object.entries(leadSources)
                  .sort(([,a], [,b]) => b - a)
                  .slice(0, 3)
                  .map(([source, count]) => (
                    <div key={source} className="flex items-center justify-between">
                      <span className="text-sm">{source}</span>
                      <span className="text-sm font-medium">{count}</span>
                    </div>
                  ))}
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Clients Tab */}
        <TabsContent value="clients" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2">
            <Card>
              <CardHeader>
                <CardTitle>Client Status</CardTitle>
              </CardHeader>
              <CardContent className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-sm">Active</span>
                  <Badge variant="default">{activeClients}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Inactive</span>
                  <Badge variant="secondary">{recentClients.filter(c => c.status === 'inactive').length}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Suspended</span>
                  <Badge variant="destructive">{recentClients.filter(c => c.status === 'suspended').length}</Badge>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Client Retention Rate</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center">
                  <div className="text-3xl font-bold text-blue-600">
                    {totalClients > 0 ? Math.round((activeClients / totalClients) * 100) : 0}%
                  </div>
                  <p className="text-sm text-muted-foreground">Retention Rate</p>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Campaigns Tab */}
        <TabsContent value="campaigns" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2">
            <Card>
              <CardHeader>
                <CardTitle>Campaign Status</CardTitle>
              </CardHeader>
              <CardContent className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-sm">Active</span>
                  <Badge variant="default">{activeCampaigns}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Draft</span>
                  <Badge variant="secondary">{recentCampaigns.filter(c => c.status === 'draft').length}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Paused</span>
                  <Badge variant="secondary">{recentCampaigns.filter(c => c.status === 'paused').length}</Badge>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">Completed</span>
                  <Badge variant="default">{recentCampaigns.filter(c => c.status === 'completed').length}</Badge>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Campaign Performance</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center">
                  <div className="text-3xl font-bold text-purple-600">
                    {totalCampaigns > 0 ? Math.round((activeCampaigns / totalCampaigns) * 100) : 0}%
                  </div>
                  <p className="text-sm text-muted-foreground">Active Campaign Rate</p>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>

      {/* Export Options */}
      <Card>
        <CardHeader>
          <CardTitle>Export Reports</CardTitle>
          <CardDescription>Download detailed reports in various formats</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4 md:grid-cols-3">
            <Button variant="outline" onClick={() => handleExportReport('leads')}>
              <Download className="w-4 h-4 mr-2" />
              Export Leads Report
            </Button>
            <Button variant="outline" onClick={() => handleExportReport('clients')}>
              <Download className="w-4 h-4 mr-2" />
              Export Clients Report
            </Button>
            <Button variant="outline" onClick={() => handleExportReport('campaigns')}>
              <Download className="w-4 h-4 mr-2" />
              Export Campaigns Report
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
