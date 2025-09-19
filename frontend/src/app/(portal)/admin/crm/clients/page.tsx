'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
  Search,
  Filter,
  Plus,
  Eye,
  Edit,
  Phone,
  Mail,
  Calendar,
  CheckCircle,
  Clock,
  AlertCircle,
  User,
  Building2,
  MapPin,
  Globe,
  RefreshCw,
  Users,
  Target,
  TrendingUp,
  DollarSign,
  Activity,
  FileText
} from 'lucide-react';

export default function ClientsManagement() {
  const { user, isAuthenticated, isAdmin } = useAuth();
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

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [industryFilter, setIndustryFilter] = useState('all');
  const [selectedClient, setSelectedClient] = useState<any>(null);
  const [isClientDialogOpen, setIsClientDialogOpen] = useState(false);
  const [isEditMode, setIsEditMode] = useState(false);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [activeTab, setActiveTab] = useState('overview');

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

  // Filter clients based on search and filters
  const filteredClients = clients.filter(client => {
    const matchesSearch = !searchTerm || 
      client.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      client.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      client.city?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      client.state?.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || client.status === statusFilter;
    const matchesIndustry = industryFilter === 'all' || client.industry === industryFilter;
    
    return matchesSearch && matchesStatus && matchesIndustry;
  });

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'active': return 'default';
      case 'inactive': return 'secondary';
      case 'suspended': return 'destructive';
      default: return 'secondary';
    }
  };

  const handleClientAction = async (clientId: string, action: string, data?: any) => {
    try {
      // TODO: Implement client actions API calls
      console.log(`Client action: ${action} for client ${clientId}`, data);
      
      // Refresh data after action
      await loadInitialData();
      
      // Close dialog if editing
      if (action === 'update') {
        setIsClientDialogOpen(false);
        setIsEditMode(false);
      }
    } catch (error) {
      console.error('Failed to perform client action:', error);
    }
  };

  const handleViewClient = (client: any) => {
    setSelectedClient(client);
    setIsEditMode(false);
    setIsClientDialogOpen(true);
  };

  const handleEditClient = (client: any) => {
    setSelectedClient(client);
    setIsEditMode(true);
    setIsClientDialogOpen(true);
  };

  const handleStatusChange = async (clientId: string, newStatus: string) => {
    await handleClientAction(clientId, 'update', { status: newStatus });
  };

  // Get client statistics
  const getClientStats = (client: any) => {
    const clientLeads = leads.filter(lead => lead.client?.id === client.id);
    const clientCampaigns = campaigns.filter(campaign => campaign.client?.id === client.id);
    const activeCampaigns = clientCampaigns.filter(campaign => campaign.status === 'active');
    
    return {
      totalLeads: clientLeads.length,
      convertedLeads: clientLeads.filter(lead => lead.status === 'converted').length,
      totalCampaigns: clientCampaigns.length,
      activeCampaigns: activeCampaigns.length,
    };
  };

  if (!isAuthenticated) {
    return <div>Please log in to access clients management.</div>;
  }

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Clients Management" 
        description="Manage client relationships and information"
        action={
          <div className="flex gap-2">
            <Button variant="outline" size="sm" onClick={handleRefresh} disabled={isRefreshing}>
              <RefreshCw className={`w-4 h-4 mr-2 ${isRefreshing ? 'animate-spin' : ''}`} />
              Refresh
            </Button>
            <Button size="sm">
              <Plus className="w-4 h-4 mr-2" />
              Add Client
            </Button>
          </div>
        }
      />

      {/* Client Overview Cards */}
      <div className="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Clients</CardTitle>
            <Building2 className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{clients.length}</div>
            <p className="text-xs text-muted-foreground">
              {clients.filter(c => c.status === 'active').length} active
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Clients</CardTitle>
            <CheckCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{clients.filter(c => c.status === 'active').length}</div>
            <p className="text-xs text-muted-foreground">
              {clients.length > 0 ? Math.round((clients.filter(c => c.status === 'active').length / clients.length) * 100) : 0}% active rate
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Campaigns</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{campaigns.length}</div>
            <p className="text-xs text-muted-foreground">
              {campaigns.filter(c => c.status === 'active').length} active
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Client Leads</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{leads.filter(l => l.client).length}</div>
            <p className="text-xs text-muted-foreground">
              {leads.filter(l => l.client && l.status === 'converted').length} converted
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="relative">
              <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search clients..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-8"
              />
            </div>
            
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="active">Active</SelectItem>
                <SelectItem value="inactive">Inactive</SelectItem>
                <SelectItem value="suspended">Suspended</SelectItem>
              </SelectContent>
            </Select>

            <Select value={industryFilter} onValueChange={setIndustryFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Industry" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Industries</SelectItem>
                <SelectItem value="law">Law</SelectItem>
                <SelectItem value="healthcare">Healthcare</SelectItem>
                <SelectItem value="finance">Finance</SelectItem>
                <SelectItem value="technology">Technology</SelectItem>
                <SelectItem value="other">Other</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Clients Table */}
      <Card>
        <CardHeader>
          <CardTitle>Clients ({filteredClients.length})</CardTitle>
          <CardDescription>Manage client relationships and information</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Client</TableHead>
                <TableHead>Contact</TableHead>
                <TableHead>Industry</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Statistics</TableHead>
                <TableHead>Created</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredClients.map((client) => {
                const stats = getClientStats(client);
                return (
                  <TableRow key={client.id}>
                    <TableCell>
                      <div className="flex items-center space-x-2">
                        <Building2 className="w-4 h-4 text-muted-foreground" />
                        <div>
                          <div className="font-medium">{client.name}</div>
                          {client.slug && (
                            <div className="text-xs text-muted-foreground">
                              {client.slug}
                            </div>
                          )}
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="space-y-1">
                        {client.email && (
                          <div className="flex items-center text-sm">
                            <Mail className="w-3 h-3 mr-1 text-muted-foreground" />
                            {client.email}
                          </div>
                        )}
                        {client.phone && (
                          <div className="flex items-center text-sm">
                            <Phone className="w-3 h-3 mr-1 text-muted-foreground" />
                            {client.phone}
                          </div>
                        )}
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline">{client.industry}</Badge>
                    </TableCell>
                    <TableCell>
                      <Select 
                        value={client.status} 
                        onValueChange={(value) => handleStatusChange(client.id, value)}
                      >
                        <SelectTrigger className="w-[120px]">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="active">Active</SelectItem>
                          <SelectItem value="inactive">Inactive</SelectItem>
                          <SelectItem value="suspended">Suspended</SelectItem>
                        </SelectContent>
                      </Select>
                    </TableCell>
                    <TableCell>
                      <div className="space-y-1 text-xs">
                        <div className="flex items-center space-x-1">
                          <Users className="w-3 h-3 text-muted-foreground" />
                          <span>{stats.totalLeads} leads</span>
                        </div>
                        <div className="flex items-center space-x-1">
                          <Target className="w-3 h-3 text-muted-foreground" />
                          <span>{stats.activeCampaigns} campaigns</span>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center space-x-1">
                        <Calendar className="w-3 h-3 text-muted-foreground" />
                        <span className="text-sm">
                          {new Date(client.createdAt).toLocaleDateString()}
                        </span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center space-x-2">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleViewClient(client)}
                        >
                          <Eye className="w-4 h-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleEditClient(client)}
                        >
                          <Edit className="w-4 h-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      {/* Client Detail Dialog */}
      <Dialog open={isClientDialogOpen} onOpenChange={setIsClientDialogOpen}>
        <DialogContent className="max-w-6xl max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>
              {isEditMode ? 'Edit Client' : 'Client Details'}
            </DialogTitle>
            <DialogDescription>
              {isEditMode ? 'Update client information' : 'View client information and activities'}
            </DialogDescription>
          </DialogHeader>
          {selectedClient && (
            <div className="space-y-6">
              <Tabs value={activeTab} onValueChange={setActiveTab}>
                <TabsList className="grid w-full grid-cols-4">
                  <TabsTrigger value="overview">Overview</TabsTrigger>
                  <TabsTrigger value="details">Details</TabsTrigger>
                  <TabsTrigger value="leads">Leads</TabsTrigger>
                  <TabsTrigger value="campaigns">Campaigns</TabsTrigger>
                </TabsList>

                <TabsContent value="overview" className="space-y-4">
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <Card>
                      <CardHeader className="pb-2">
                        <CardTitle className="text-sm">Total Leads</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <div className="text-2xl font-bold">
                          {leads.filter(l => l.client?.id === selectedClient.id).length}
                        </div>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader className="pb-2">
                        <CardTitle className="text-sm">Converted Leads</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <div className="text-2xl font-bold">
                          {leads.filter(l => l.client?.id === selectedClient.id && l.status === 'converted').length}
                        </div>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader className="pb-2">
                        <CardTitle className="text-sm">Active Campaigns</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <div className="text-2xl font-bold">
                          {campaigns.filter(c => c.client?.id === selectedClient.id && c.status === 'active').length}
                        </div>
                      </CardContent>
                    </Card>
                    <Card>
                      <CardHeader className="pb-2">
                        <CardTitle className="text-sm">Total Campaigns</CardTitle>
                      </CardHeader>
                      <CardContent>
                        <div className="text-2xl font-bold">
                          {campaigns.filter(c => c.client?.id === selectedClient.id).length}
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </TabsContent>

                <TabsContent value="details" className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Client Name</Label>
                      <Input 
                        value={selectedClient.name} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, name: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Slug</Label>
                      <Input 
                        value={selectedClient.slug || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, slug: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Email</Label>
                      <Input 
                        value={selectedClient.email || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, email: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Phone</Label>
                      <Input 
                        value={selectedClient.phone || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, phone: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Website</Label>
                      <Input 
                        value={selectedClient.websiteUrl || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, websiteUrl: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Industry</Label>
                      <Select 
                        value={selectedClient.industry} 
                        disabled={!isEditMode}
                        onValueChange={isEditMode ? (value) => setSelectedClient({...selectedClient, industry: value}) : undefined}
                      >
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="law">Law</SelectItem>
                          <SelectItem value="healthcare">Healthcare</SelectItem>
                          <SelectItem value="finance">Finance</SelectItem>
                          <SelectItem value="technology">Technology</SelectItem>
                          <SelectItem value="other">Other</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                    <div>
                      <Label>Status</Label>
                      <Select 
                        value={selectedClient.status} 
                        disabled={!isEditMode}
                        onValueChange={isEditMode ? (value) => setSelectedClient({...selectedClient, status: value}) : undefined}
                      >
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="active">Active</SelectItem>
                          <SelectItem value="inactive">Inactive</SelectItem>
                          <SelectItem value="suspended">Suspended</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                  </div>

                  <div className="grid grid-cols-3 gap-4">
                    <div>
                      <Label>Address</Label>
                      <Input 
                        value={selectedClient.address || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, address: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>City</Label>
                      <Input 
                        value={selectedClient.city || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, city: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>State</Label>
                      <Input 
                        value={selectedClient.state || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, state: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Postal Code</Label>
                      <Input 
                        value={selectedClient.postalCode || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, postalCode: e.target.value}) : undefined}
                      />
                    </div>
                    <div>
                      <Label>Country</Label>
                      <Input 
                        value={selectedClient.country || ''} 
                        readOnly={!isEditMode}
                        onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, country: e.target.value}) : undefined}
                      />
                    </div>
                  </div>

                  <div>
                    <Label>Description</Label>
                    <Textarea 
                      value={selectedClient.description || ''} 
                      readOnly={!isEditMode}
                      rows={4}
                      onChange={isEditMode ? (e) => setSelectedClient({...selectedClient, description: e.target.value}) : undefined}
                    />
                  </div>
                </TabsContent>

                <TabsContent value="leads" className="space-y-4">
                  <div className="space-y-2">
                    {leads.filter(l => l.client?.id === selectedClient.id).map((lead) => (
                      <Card key={lead.id}>
                        <CardContent className="p-4">
                          <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-3">
                              <User className="w-4 h-4 text-muted-foreground" />
                              <div>
                                <div className="font-medium">{lead.fullName}</div>
                                <div className="text-sm text-muted-foreground">{lead.email}</div>
                              </div>
                            </div>
                            <div className="flex items-center space-x-2">
                              <Badge variant={getStatusBadgeVariant(lead.status)}>
                                {lead.status}
                              </Badge>
                              <span className="text-sm text-muted-foreground">
                                {new Date(lead.createdAt).toLocaleDateString()}
                              </span>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                </TabsContent>

                <TabsContent value="campaigns" className="space-y-4">
                  <div className="space-y-2">
                    {campaigns.filter(c => c.client?.id === selectedClient.id).map((campaign) => (
                      <Card key={campaign.id}>
                        <CardContent className="p-4">
                          <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-3">
                              <Target className="w-4 h-4 text-muted-foreground" />
                              <div>
                                <div className="font-medium">{campaign.name}</div>
                                <div className="text-sm text-muted-foreground">{campaign.type}</div>
                              </div>
                            </div>
                            <div className="flex items-center space-x-2">
                              <Badge variant={campaign.status === 'active' ? 'default' : 'secondary'}>
                                {campaign.status}
                              </Badge>
                              <span className="text-sm text-muted-foreground">
                                {new Date(campaign.createdAt).toLocaleDateString()}
                              </span>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                </TabsContent>
              </Tabs>

              {/* Actions */}
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setIsClientDialogOpen(false)}>
                  Close
                </Button>
                {isEditMode ? (
                  <Button onClick={() => handleClientAction(selectedClient.id, 'update', selectedClient)}>
                    Save Changes
                  </Button>
                ) : (
                  <Button onClick={() => setIsEditMode(true)}>
                    Edit Client
                  </Button>
                )}
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
