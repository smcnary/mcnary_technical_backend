'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
  Users, 
  Building2, 
  Target, 
  TrendingUp, 
  DollarSign,
  Phone,
  Mail,
  Calendar,
  Plus,
  RefreshCw,
  Filter,
  Search,
  Eye,
  Edit,
  Trash2,
  CheckCircle,
  Clock,
  AlertCircle,
  ArrowRight,
  Activity,
  BarChart3
} from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

export default function CrmDashboard() {
  const { user, isAuthenticated, isAdmin } = useAuth();
  const {
    clients,
    leads,
    users,
    getClients,
    getLeads,
    getUsers,
    getLoadingState,
    getErrorState,
    clearError,
  } = useData();

  const [activeTab, setActiveTab] = useState('overview');
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedLead, setSelectedLead] = useState<any>(null);
  const [isLeadDialogOpen, setIsLeadDialogOpen] = useState(false);

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
        ...(isAdmin ? [getUsers()] : []),
      ]);
    } catch (error) {
      console.error('Failed to load initial data:', error);
    }
  };

  const handleRefreshAll = async () => {
    setIsRefreshing(true);
    try {
      await loadInitialData();
    } catch (error) {
      console.error('Failed to refresh data:', error);
    } finally {
      setIsRefreshing(false);
    }
  };

  // Calculate CRM metrics
  const totalLeads = leads.length;
  const newLeads = leads.filter(lead => lead.status === 'new').length;
  const qualifiedLeads = leads.filter(lead => lead.status === 'qualified').length;
  const convertedLeads = leads.filter(lead => lead.status === 'converted').length;
  const totalClients = clients.length;
  const activeClients = clients.filter(client => client.status === 'active').length;

  // Filter leads based on search and status
  const filteredLeads = leads.filter(lead => {
    const matchesSearch = !searchTerm || 
      lead.fullName?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      lead.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      lead.firm?.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || lead.status === statusFilter;
    
    return matchesSearch && matchesStatus;
  });

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'new': return 'secondary';
      case 'qualified': return 'default';
      case 'converted': return 'default';
      case 'lost': return 'destructive';
      default: return 'secondary';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'new': return <Clock className="w-4 h-4" />;
      case 'qualified': return <CheckCircle className="w-4 h-4" />;
      case 'converted': return <CheckCircle className="w-4 h-4" />;
      case 'lost': return <AlertCircle className="w-4 h-4" />;
      default: return <Clock className="w-4 h-4" />;
    }
  };

  const handleLeadAction = async (leadId: string, action: string) => {
    // TODO: Implement lead actions (update status, assign, etc.)
    console.log(`Lead action: ${action} for lead ${leadId}`);
  };

  const handleViewLead = (lead: any) => {
    setSelectedLead(lead);
    setIsLeadDialogOpen(true);
  };

  if (!isAuthenticated) {
    return <div>Please log in to access the CRM dashboard.</div>;
  }

  return (
    <div className="space-y-6">
      <PageHeader 
        title="CRM Dashboard" 
        description="Manage leads, clients, and customer relationships"
        action={
          <div className="flex gap-2">
            <Button variant="outline" size="sm" onClick={handleRefreshAll} disabled={isRefreshing}>
              <RefreshCw className={`w-4 h-4 mr-2 ${isRefreshing ? 'animate-spin' : ''}`} />
              Refresh
            </Button>
            <Button size="sm">
              <Plus className="w-4 h-4 mr-2" />
              Add Lead
            </Button>
          </div>
        }
      />

      {/* CRM Overview Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Leads</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{totalLeads}</div>
            <p className="text-xs text-muted-foreground">
              +{newLeads} new this month
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Qualified Leads</CardTitle>
            <CheckCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{qualifiedLeads}</div>
            <p className="text-xs text-muted-foreground">
              {totalLeads > 0 ? Math.round((qualifiedLeads / totalLeads) * 100) : 0}% qualification rate
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Converted Leads</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{convertedLeads}</div>
            <p className="text-xs text-muted-foreground">
              {totalLeads > 0 ? Math.round((convertedLeads / totalLeads) * 100) : 0}% conversion rate
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
      </div>

        {/* Quick Navigation Cards */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => window.location.href = '/admin/crm/leads'}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Leads Management</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{totalLeads}</div>
              <p className="text-xs text-muted-foreground flex items-center">
                Manage leads and track conversions
                <ArrowRight className="w-3 h-3 ml-1" />
              </p>
            </CardContent>
          </Card>

          <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => window.location.href = '/admin/crm/clients'}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Clients Management</CardTitle>
              <Building2 className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{totalClients}</div>
              <p className="text-xs text-muted-foreground flex items-center">
                Manage client relationships
                <ArrowRight className="w-3 h-3 ml-1" />
              </p>
            </CardContent>
          </Card>

          <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => window.location.href = '/admin/crm/activities'}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Activities</CardTitle>
              <Activity className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">12</div>
              <p className="text-xs text-muted-foreground flex items-center">
                Track tasks and interactions
                <ArrowRight className="w-3 h-3 ml-1" />
              </p>
            </CardContent>
          </Card>

          <Card className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => window.location.href = '/admin/crm/reports'}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Reports & Analytics</CardTitle>
              <BarChart3 className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{leadToConvertedRate}%</div>
              <p className="text-xs text-muted-foreground flex items-center">
                View detailed analytics
                <ArrowRight className="w-3 h-3 ml-1" />
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Main Content Tabs */}
        <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-4">
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="overview">Overview</TabsTrigger>
            <TabsTrigger value="leads">Leads</TabsTrigger>
            <TabsTrigger value="clients">Clients</TabsTrigger>
            <TabsTrigger value="activities">Activities</TabsTrigger>
          </TabsList>

        {/* Overview Tab */}
        <TabsContent value="overview" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2">
            <Card>
              <CardHeader>
                <CardTitle>Recent Leads</CardTitle>
                <CardDescription>Latest lead submissions</CardDescription>
              </CardHeader>
              <CardContent className="space-y-3">
                {leads.slice(0, 5).map((lead) => (
                  <div key={lead.id} className="flex items-center justify-between text-sm">
                    <div className="flex items-center space-x-2">
                      <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
                      <span>{lead.fullName}</span>
                    </div>
                    <Badge variant={getStatusBadgeVariant(lead.status)}>
                      {lead.status}
                    </Badge>
                  </div>
                ))}
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Lead Sources</CardTitle>
                <CardDescription>Where leads are coming from</CardDescription>
              </CardHeader>
              <CardContent className="space-y-3">
                <div className="flex items-center justify-between text-sm">
                  <span>Website Contact Form</span>
                  <Badge variant="secondary">45%</Badge>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span>Phone Calls</span>
                  <Badge variant="secondary">25%</Badge>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span>Email Inquiries</span>
                  <Badge variant="secondary">20%</Badge>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span>Referrals</span>
                  <Badge variant="secondary">10%</Badge>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Leads Tab */}
        <TabsContent value="leads" className="space-y-4">
          <div className="flex items-center space-x-2">
            <div className="relative flex-1">
              <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search leads..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-8"
              />
            </div>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-[180px]">
                <SelectValue placeholder="Filter by status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="new">New</SelectItem>
                <SelectItem value="qualified">Qualified</SelectItem>
                <SelectItem value="converted">Converted</SelectItem>
                <SelectItem value="lost">Lost</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Leads Management</CardTitle>
              <CardDescription>Manage and track all leads</CardDescription>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Firm</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Created</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredLeads.map((lead) => (
                    <TableRow key={lead.id}>
                      <TableCell className="font-medium">{lead.fullName}</TableCell>
                      <TableCell>{lead.email}</TableCell>
                      <TableCell>{lead.firm || '-'}</TableCell>
                      <TableCell>
                        <Badge variant={getStatusBadgeVariant(lead.status)} className="flex items-center gap-1 w-fit">
                          {getStatusIcon(lead.status)}
                          {lead.status}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        {new Date(lead.createdAt).toLocaleDateString()}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center space-x-2">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleViewLead(lead)}
                          >
                            <Eye className="w-4 h-4" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleLeadAction(lead.id, 'edit')}
                          >
                            <Edit className="w-4 h-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Clients Tab */}
        <TabsContent value="clients" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Client Management</CardTitle>
              <CardDescription>Manage client relationships and information</CardDescription>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Client Name</TableHead>
                    <TableHead>Industry</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Contact</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {clients.map((client) => (
                    <TableRow key={client.id}>
                      <TableCell className="font-medium">{client.name}</TableCell>
                      <TableCell>{client.industry}</TableCell>
                      <TableCell>
                        <Badge variant={client.status === 'active' ? 'default' : 'secondary'}>
                          {client.status}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div className="space-y-1">
                          {client.email && (
                            <div className="flex items-center text-sm text-muted-foreground">
                              <Mail className="w-3 h-3 mr-1" />
                              {client.email}
                            </div>
                          )}
                          {client.phone && (
                            <div className="flex items-center text-sm text-muted-foreground">
                              <Phone className="w-3 h-3 mr-1" />
                              {client.phone}
                            </div>
                          )}
                        </div>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center space-x-2">
                          <Button variant="ghost" size="sm">
                            <Eye className="w-4 h-4" />
                          </Button>
                          <Button variant="ghost" size="sm">
                            <Edit className="w-4 h-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Activities Tab */}
        <TabsContent value="activities" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Recent Activities</CardTitle>
              <CardDescription>Track all CRM activities and interactions</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="flex items-center space-x-4 text-sm">
                  <Calendar className="w-4 h-4 text-muted-foreground" />
                  <span>New lead submitted: John Doe from Smith & Associates</span>
                  <Badge variant="secondary">2 hours ago</Badge>
                </div>
                <div className="flex items-center space-x-4 text-sm">
                  <Phone className="w-4 h-4 text-muted-foreground" />
                  <span>Phone call scheduled with ABC Law Firm</span>
                  <Badge variant="secondary">1 day ago</Badge>
                </div>
                <div className="flex items-center space-x-4 text-sm">
                  <Mail className="w-4 h-4 text-muted-foreground" />
                  <span>Follow-up email sent to XYZ Corporation</span>
                  <Badge variant="secondary">3 days ago</Badge>
                </div>
                <div className="flex items-center space-x-4 text-sm">
                  <CheckCircle className="w-4 h-4 text-muted-foreground" />
                  <span>Lead converted to client: Legal Solutions Inc.</span>
                  <Badge variant="secondary">1 week ago</Badge>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Lead Detail Dialog */}
      <Dialog open={isLeadDialogOpen} onOpenChange={setIsLeadDialogOpen}>
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>Lead Details</DialogTitle>
            <DialogDescription>
              View and manage lead information
            </DialogDescription>
          </DialogHeader>
          {selectedLead && (
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Full Name</Label>
                  <Input value={selectedLead.fullName} readOnly />
                </div>
                <div>
                  <Label>Email</Label>
                  <Input value={selectedLead.email} readOnly />
                </div>
                <div>
                  <Label>Phone</Label>
                  <Input value={selectedLead.phone || ''} readOnly />
                </div>
                <div>
                  <Label>Firm</Label>
                  <Input value={selectedLead.firm || ''} readOnly />
                </div>
                <div>
                  <Label>City</Label>
                  <Input value={selectedLead.city || ''} readOnly />
                </div>
                <div>
                  <Label>State</Label>
                  <Input value={selectedLead.state || ''} readOnly />
                </div>
              </div>
              <div>
                <Label>Message</Label>
                <Textarea value={selectedLead.message || ''} readOnly rows={4} />
              </div>
              <div>
                <Label>Practice Areas</Label>
                <div className="flex flex-wrap gap-2 mt-2">
                  {selectedLead.practiceAreas?.map((area: string, index: number) => (
                    <Badge key={index} variant="secondary">{area}</Badge>
                  ))}
                </div>
              </div>
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setIsLeadDialogOpen(false)}>
                  Close
                </Button>
                <Button onClick={() => handleLeadAction(selectedLead.id, 'qualify')}>
                  Qualify Lead
                </Button>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
