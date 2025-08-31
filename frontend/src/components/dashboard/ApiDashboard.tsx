import React, { useEffect, useState } from 'react';
import { useAuth } from '../../hooks/useAuth';
import { useData } from '../../hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '../ui/tabs';
import { 
  Loader2, 
  Users, 
  Target, 
  Package, 
  FileText, 
  Image, 
  HelpCircle, 
  TrendingUp,
  Plus,
  RefreshCw
} from 'lucide-react';
import { ProtectedRoute } from '../auth/ProtectedRoute';

export default function ApiDashboard() {
  const { user, isAuthenticated, isAdmin, isClientAdmin } = useAuth();
  const {
    clients,
    campaigns,
    packages: servicePackages,
    pages,
    mediaAssets,
    faqs,
    caseStudies,
    leads,
    users,
    getClients,
    getCampaigns,
    getPackages,
    getPages,
    getMediaAssets,
    getFaqs,
    getCaseStudies,
    getLeads,
    getUsers,
    getLoadingState,
    getErrorState,
    clearError,
    refreshAllData,
  } = useData();

  const [activeTab, setActiveTab] = useState('overview');
  const [isRefreshing, setIsRefreshing] = useState(false);

  // Load data on component mount
  useEffect(() => {
    if (isAuthenticated) {
      loadInitialData();
    }
  }, [isAuthenticated]);

  const loadInitialData = async () => {
    try {
      await Promise.all([
        getClients(),
        getCampaigns(),
        getPackages(),
        getPages(),
        getMediaAssets(),
        getFaqs(),
        getCaseStudies(),
        getLeads(),
        ...(isAdmin ? [getUsers()] : []),
      ]);
    } catch (error) {
      console.error('Failed to load initial data:', error);
    }
  };

  const handleRefreshAll = async () => {
    setIsRefreshing(true);
    try {
      await refreshAllData();
    } catch (error) {
      console.error('Failed to refresh data:', error);
    } finally {
      setIsRefreshing(false);
    }
  };

  const renderError = (dataType: string) => {
    const error = getErrorState(dataType);
    if (!error) return null;

    return (
      <div className="flex items-center justify-between p-3 bg-destructive/10 border border-destructive/20 rounded-lg">
        <span className="text-sm text-destructive">{error}</span>
        <Button
          variant="ghost"
          size="sm"
          onClick={() => clearError(dataType)}
          className="text-destructive hover:text-destructive/80"
        >
          Dismiss
        </Button>
      </div>
    );
  };

  const renderLoadingState = (dataType: string) => {
    if (getLoadingState(dataType)) {
      return (
        <div className="flex items-center justify-center p-4">
          <Loader2 className="h-4 w-4 animate-spin mr-2" />
          <span className="text-sm text-muted-foreground">Loading...</span>
        </div>
      );
    }
    return null;
  };

  const renderDataCard = (
    title: string,
    count: number,
    icon: React.ReactNode,
    description: string,
    color: string = 'bg-blue-500'
  ) => (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium">{title}</CardTitle>
        <div className={`p-2 rounded-full ${color} text-white`}>
          {icon}
        </div>
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{count}</div>
        <p className="text-xs text-muted-foreground">{description}</p>
      </CardContent>
    </Card>
  );

  if (!isAuthenticated) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="w-full max-w-md">
          <CardHeader className="text-center">
            <CardTitle>Authentication Required</CardTitle>
            <CardDescription>Please sign in to view the dashboard</CardDescription>
          </CardHeader>
        </Card>
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="container mx-auto p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">API Dashboard</h1>
            <p className="text-muted-foreground">
              Welcome back, {user?.name || user?.email}
            </p>
          </div>
          <Button
            onClick={handleRefreshAll}
            disabled={isRefreshing}
            variant="outline"
          >
            {isRefreshing ? (
              <Loader2 className="h-4 w-4 animate-spin mr-2" />
            ) : (
              <RefreshCw className="h-4 w-4 mr-2" />
            )}
            Refresh All
          </Button>
        </div>

        {/* Overview Cards */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          {renderDataCard(
            'Clients',
            clients.length,
            <Users className="h-4 w-4" />,
            'Total clients',
            'bg-blue-500'
          )}
          {renderDataCard(
            'Campaigns',
            campaigns.length,
            <Target className="h-4 w-4" />,
            'Active campaigns',
            'bg-green-500'
          )}
          {renderDataCard(
            'Packages',
            servicePackages.length,
            <Package className="h-4 w-4" />,
            'Service packages',
            'bg-purple-500'
          )}
          {renderDataCard(
            'Leads',
            leads.length,
            <TrendingUp className="h-4 w-4" />,
            'Total leads',
            'bg-orange-500'
          )}
        </div>

        {/* Main Content Tabs */}
        <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-4">
          <TabsList className="grid w-full grid-cols-6">
            <TabsTrigger value="overview">Overview</TabsTrigger>
            <TabsTrigger value="clients">Clients</TabsTrigger>
            <TabsTrigger value="campaigns">Campaigns</TabsTrigger>
            <TabsTrigger value="content">Content</TabsTrigger>
            <TabsTrigger value="leads">Leads</TabsTrigger>
            {isAdmin && <TabsTrigger value="admin">Admin</TabsTrigger>}
          </TabsList>

          {/* Overview Tab */}
          <TabsContent value="overview" className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Card>
                <CardHeader>
                  <CardTitle>Recent Activity</CardTitle>
                  <CardDescription>Latest updates across the system</CardDescription>
                </CardHeader>
                <CardContent className="space-y-3">
                  <div className="flex items-center justify-between text-sm">
                    <span>New clients this month</span>
                    <Badge variant="secondary">{clients.filter(c => 
                      new Date(c.createdAt).getMonth() === new Date().getMonth()
                    ).length}</Badge>
                  </div>
                  <div className="flex items-center justify-between text-sm">
                    <span>Active campaigns</span>
                    <Badge variant="secondary">{campaigns.filter(c => c.status === 'active').length}</Badge>
                  </div>
                  <div className="flex items-center justify-between text-sm">
                    <span>Published pages</span>
                    <Badge variant="secondary">{pages.filter(p => p.status === 'published').length}</Badge>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Quick Actions</CardTitle>
                  <CardDescription>Common tasks and shortcuts</CardDescription>
                </CardHeader>
                <CardContent className="space-y-3">
                  <Button variant="outline" className="w-full justify-start">
                    <Plus className="h-4 w-4 mr-2" />
                    Add New Client
                  </Button>
                  <Button variant="outline" className="w-full justify-start">
                    <Plus className="h-4 w-4 mr-2" />
                    Create Campaign
                  </Button>
                  <Button variant="outline" className="w-full justify-start">
                    <Plus className="h-4 w-4 mr-2" />
                    Submit Lead
                  </Button>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Clients Tab */}
          <TabsContent value="clients" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Clients</CardTitle>
                <CardDescription>Manage your client relationships</CardDescription>
              </CardHeader>
              <CardContent>
                {renderLoadingState('clients')}
                {renderError('clients')}
                {!getLoadingState('clients') && (
                  <div className="space-y-3">
                    {clients.map((client) => (
                      <div key={client.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                          <h4 className="font-medium">{client.name}</h4>
                          <p className="text-sm text-muted-foreground">{client.website || 'No website'}</p>
                        </div>
                        <Badge variant={client.status === 'active' ? 'default' : 'secondary'}>
                          {client.status || 'Unknown'}
                        </Badge>
                      </div>
                    ))}
                    {clients.length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No clients found</p>
                    )}
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          {/* Campaigns Tab */}
          <TabsContent value="campaigns" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Campaigns</CardTitle>
                <CardDescription>Track your marketing campaigns</CardDescription>
              </CardHeader>
              <CardContent>
                {renderLoadingState('campaigns')}
                {renderError('campaigns')}
                {!getLoadingState('campaigns') && (
                  <div className="space-y-3">
                    {campaigns.map((campaign) => (
                      <div key={campaign.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                          <h4 className="font-medium">{campaign.name}</h4>
                          <p className="text-sm text-muted-foreground">{campaign.description || 'No description'}</p>
                        </div>
                        <Badge variant={campaign.status === 'active' ? 'default' : 'secondary'}>
                          {campaign.status}
                        </Badge>
                      </div>
                    ))}
                    {campaigns.length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No campaigns found</p>
                    )}
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          {/* Content Tab */}
          <TabsContent value="content" className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Card>
                <CardHeader>
                  <CardTitle>Pages</CardTitle>
                  <CardDescription>Manage your content pages</CardDescription>
                </CardHeader>
                <CardContent>
                  {renderLoadingState('pages')}
                  {renderError('pages')}
                  {!getLoadingState('pages') && (
                    <div className="space-y-2">
                      {pages.slice(0, 5).map((page) => (
                        <div key={page.id} className="flex items-center justify-between text-sm">
                          <span>{page.title}</span>
                          <Badge variant={page.status === 'published' ? 'default' : 'secondary'}>
                            {page.status}
                          </Badge>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Media Assets</CardTitle>
                  <CardDescription>Your uploaded files and images</CardDescription>
                </CardHeader>
                <CardContent>
                  {renderLoadingState('mediaAssets')}
                  {renderError('mediaAssets')}
                  {!getLoadingState('mediaAssets') && (
                    <div className="space-y-2">
                      {mediaAssets.slice(0, 5).map((asset) => (
                        <div key={asset.id} className="flex items-center justify-between text-sm">
                          <span className="truncate">{asset.originalName}</span>
                          <Badge variant="outline">{asset.type || 'Unknown'}</Badge>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Leads Tab */}
          <TabsContent value="leads" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Leads</CardTitle>
                <CardDescription>Track potential clients and inquiries</CardDescription>
              </CardHeader>
              <CardContent>
                {renderLoadingState('leads')}
                {renderError('leads')}
                {!getLoadingState('leads') && (
                  <div className="space-y-3">
                    {leads.map((lead) => (
                      <div key={lead.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                          <h4 className="font-medium">{lead.name}</h4>
                          <p className="text-sm text-muted-foreground">{lead.email}</p>
                        </div>
                        <Badge variant={lead.status === 'pending' ? 'secondary' : 'default'}>
                          {lead.status}
                        </Badge>
                      </div>
                    ))}
                    {leads.length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No leads found</p>
                    )}
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          {/* Admin Tab */}
          {isAdmin && (
            <TabsContent value="admin" className="space-y-4">
              <Card>
                <CardHeader>
                  <CardTitle>User Management</CardTitle>
                  <CardDescription>Manage system users and permissions</CardDescription>
                </CardHeader>
                <CardContent>
                  {renderLoadingState('users')}
                  {renderError('users')}
                  {!getLoadingState('users') && (
                    <div className="space-y-3">
                      {users.map((user) => (
                        <div key={user.id} className="flex items-center justify-between p-3 border rounded-lg">
                          <div>
                            <h4 className="font-medium">{user.name || user.email}</h4>
                            <p className="text-sm text-muted-foreground">{user.email}</p>
                          </div>
                          <div className="flex gap-2">
                            {user.roles.map((role) => (
                              <Badge key={role} variant="outline">{role}</Badge>
                            ))}
                          </div>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
          )}
        </Tabs>
      </div>
    </ProtectedRoute>
  );
}
