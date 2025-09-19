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
  RefreshCw
} from 'lucide-react';

export default function LeadsManagement() {
  const { user, isAuthenticated, isAdmin, isSalesConsultant } = useAuth();
  const {
    leads,
    clients,
    getLeads,
    getClients,
    getLoadingState,
    getErrorState,
    clearError,
  } = useData();

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [sourceFilter, setSourceFilter] = useState('all');
  const [dateFilter, setDateFilter] = useState('all');
  const [selectedLead, setSelectedLead] = useState<any>(null);
  const [isLeadDialogOpen, setIsLeadDialogOpen] = useState(false);
  const [isEditMode, setIsEditMode] = useState(false);
  const [isRefreshing, setIsRefreshing] = useState(false);

  // Load data on component mount
  useEffect(() => {
    if (isAuthenticated && (isAdmin() || isSalesConsultant())) {
      loadInitialData();
    }
  }, [isAuthenticated, isAdmin, isSalesConsultant]);

  const loadInitialData = async () => {
    try {
      await Promise.all([
        getLeads(),
        getClients(),
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

  // Filter leads based on search and filters
  const filteredLeads = leads.filter(lead => {
    const matchesSearch = !searchTerm || 
      lead.fullName?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      lead.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      lead.firm?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      lead.city?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      lead.state?.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === 'all' || lead.status === statusFilter;
    const matchesSource = sourceFilter === 'all' || lead.source?.name === sourceFilter;
    
    let matchesDate = true;
    if (dateFilter !== 'all') {
      const leadDate = new Date(lead.createdAt);
      const now = new Date();
      const daysDiff = Math.floor((now.getTime() - leadDate.getTime()) / (1000 * 60 * 60 * 24));
      
      switch (dateFilter) {
        case 'today':
          matchesDate = daysDiff === 0;
          break;
        case 'week':
          matchesDate = daysDiff <= 7;
          break;
        case 'month':
          matchesDate = daysDiff <= 30;
          break;
        case 'quarter':
          matchesDate = daysDiff <= 90;
          break;
      }
    }
    
    return matchesSearch && matchesStatus && matchesSource && matchesDate;
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

  const handleLeadAction = async (leadId: string, action: string, data?: any) => {
    try {
      // TODO: Implement lead actions API calls
      console.log(`Lead action: ${action} for lead ${leadId}`, data);
      
      // Refresh data after action
      await loadInitialData();
      
      // Close dialog if editing
      if (action === 'update') {
        setIsLeadDialogOpen(false);
        setIsEditMode(false);
      }
    } catch (error) {
      console.error('Failed to perform lead action:', error);
    }
  };

  const handleViewLead = (lead: any) => {
    setSelectedLead(lead);
    setIsEditMode(false);
    setIsLeadDialogOpen(true);
  };

  const handleEditLead = (lead: any) => {
    setSelectedLead(lead);
    setIsEditMode(true);
    setIsLeadDialogOpen(true);
  };

  const handleStatusChange = async (leadId: string, newStatus: string) => {
    await handleLeadAction(leadId, 'update', { status: newStatus });
  };

  const handleAssignToClient = async (leadId: string, clientId: string) => {
    await handleLeadAction(leadId, 'update', { clientId });
  };

  if (!isAuthenticated) {
    return <div>Please log in to access leads management.</div>;
  }

  if (!isAdmin() && !isSalesConsultant()) {
    return <div>Access denied. You need admin or sales consultant permissions to access leads management.</div>;
  }

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Leads Management" 
        description="Manage and track all leads"
        action={
          <div className="flex gap-2">
            <Button variant="outline" size="sm" onClick={handleRefresh} disabled={isRefreshing}>
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

      {/* Filters */}
      <Card>
        <CardHeader>
          <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="relative">
              <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search leads..."
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
                <SelectItem value="new">New</SelectItem>
                <SelectItem value="qualified">Qualified</SelectItem>
                <SelectItem value="converted">Converted</SelectItem>
                <SelectItem value="lost">Lost</SelectItem>
              </SelectContent>
            </Select>

            <Select value={sourceFilter} onValueChange={setSourceFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Source" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Sources</SelectItem>
                <SelectItem value="website">Website</SelectItem>
                <SelectItem value="phone">Phone</SelectItem>
                <SelectItem value="email">Email</SelectItem>
                <SelectItem value="referral">Referral</SelectItem>
              </SelectContent>
            </Select>

            <Select value={dateFilter} onValueChange={setDateFilter}>
              <SelectTrigger>
                <SelectValue placeholder="Date Range" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Time</SelectItem>
                <SelectItem value="today">Today</SelectItem>
                <SelectItem value="week">This Week</SelectItem>
                <SelectItem value="month">This Month</SelectItem>
                <SelectItem value="quarter">This Quarter</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Leads Table */}
      <Card>
        <CardHeader>
          <CardTitle>Leads ({filteredLeads.length})</CardTitle>
          <CardDescription>Manage and track all leads</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Contact</TableHead>
                <TableHead>Firm</TableHead>
                <TableHead>Location</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Source</TableHead>
                <TableHead>Created</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredLeads.map((lead) => (
                <TableRow key={lead.id}>
                  <TableCell>
                    <div className="flex items-center space-x-2">
                      <User className="w-4 h-4 text-muted-foreground" />
                      <div>
                        <div className="font-medium">{lead.fullName}</div>
                        {lead.practiceAreas && lead.practiceAreas.length > 0 && (
                          <div className="text-xs text-muted-foreground">
                            {lead.practiceAreas.join(', ')}
                          </div>
                        )}
                      </div>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="space-y-1">
                      <div className="flex items-center text-sm">
                        <Mail className="w-3 h-3 mr-1 text-muted-foreground" />
                        {lead.email}
                      </div>
                      {lead.phone && (
                        <div className="flex items-center text-sm">
                          <Phone className="w-3 h-3 mr-1 text-muted-foreground" />
                          {lead.phone}
                        </div>
                      )}
                    </div>
                  </TableCell>
                  <TableCell>
                    {lead.firm ? (
                      <div className="flex items-center space-x-1">
                        <Building2 className="w-3 h-3 text-muted-foreground" />
                        <span>{lead.firm}</span>
                      </div>
                    ) : (
                      <span className="text-muted-foreground">-</span>
                    )}
                  </TableCell>
                  <TableCell>
                    {lead.city || lead.state ? (
                      <div className="flex items-center space-x-1">
                        <MapPin className="w-3 h-3 text-muted-foreground" />
                        <span>{[lead.city, lead.state].filter(Boolean).join(', ')}</span>
                      </div>
                    ) : (
                      <span className="text-muted-foreground">-</span>
                    )}
                  </TableCell>
                  <TableCell>
                    <Select 
                      value={lead.status} 
                      onValueChange={(value) => handleStatusChange(lead.id, value)}
                    >
                      <SelectTrigger className="w-[120px]">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="new">New</SelectItem>
                        <SelectItem value="qualified">Qualified</SelectItem>
                        <SelectItem value="converted">Converted</SelectItem>
                        <SelectItem value="lost">Lost</SelectItem>
                      </SelectContent>
                    </Select>
                  </TableCell>
                  <TableCell>
                    <Badge variant="outline">
                      {lead.source?.name || 'Unknown'}
                    </Badge>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center space-x-1">
                      <Calendar className="w-3 h-3 text-muted-foreground" />
                      <span className="text-sm">
                        {new Date(lead.createdAt).toLocaleDateString()}
                      </span>
                    </div>
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
                        onClick={() => handleEditLead(lead)}
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

      {/* Lead Detail Dialog */}
      <Dialog open={isLeadDialogOpen} onOpenChange={setIsLeadDialogOpen}>
        <DialogContent className="max-w-4xl max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>
              {isEditMode ? 'Edit Lead' : 'Lead Details'}
            </DialogTitle>
            <DialogDescription>
              {isEditMode ? 'Update lead information' : 'View lead information'}
            </DialogDescription>
          </DialogHeader>
          {selectedLead && (
            <div className="space-y-6">
              {/* Basic Information */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Full Name</Label>
                  <Input 
                    value={selectedLead.fullName} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, fullName: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Email</Label>
                  <Input 
                    value={selectedLead.email} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, email: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Phone</Label>
                  <Input 
                    value={selectedLead.phone || ''} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, phone: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Firm</Label>
                  <Input 
                    value={selectedLead.firm || ''} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, firm: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Website</Label>
                  <Input 
                    value={selectedLead.website || ''} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, website: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Status</Label>
                  <Select 
                    value={selectedLead.status} 
                    disabled={!isEditMode}
                    onValueChange={isEditMode ? (value) => setSelectedLead({...selectedLead, status: value}) : undefined}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="new">New</SelectItem>
                      <SelectItem value="qualified">Qualified</SelectItem>
                      <SelectItem value="converted">Converted</SelectItem>
                      <SelectItem value="lost">Lost</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              {/* Location Information */}
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <Label>City</Label>
                  <Input 
                    value={selectedLead.city || ''} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, city: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>State</Label>
                  <Input 
                    value={selectedLead.state || ''} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, state: e.target.value}) : undefined}
                  />
                </div>
                <div>
                  <Label>Zip Code</Label>
                  <Input 
                    value={selectedLead.zipCode || ''} 
                    readOnly={!isEditMode}
                    onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, zipCode: e.target.value}) : undefined}
                  />
                </div>
              </div>

              {/* Practice Areas */}
              <div>
                <Label>Practice Areas</Label>
                <div className="flex flex-wrap gap-2 mt-2">
                  {selectedLead.practiceAreas?.map((area: string, index: number) => (
                    <Badge key={index} variant="secondary">{area}</Badge>
                  ))}
                </div>
              </div>

              {/* Message */}
              <div>
                <Label>Message</Label>
                <Textarea 
                  value={selectedLead.message || ''} 
                  readOnly={!isEditMode}
                  rows={4}
                  onChange={isEditMode ? (e) => setSelectedLead({...selectedLead, message: e.target.value}) : undefined}
                />
              </div>

              {/* UTM Data */}
              {selectedLead.utmJson && Object.keys(selectedLead.utmJson).length > 0 && (
                <div>
                  <Label>UTM Parameters</Label>
                  <div className="mt-2 p-3 bg-muted rounded-md">
                    <pre className="text-xs">
                      {JSON.stringify(selectedLead.utmJson, null, 2)}
                    </pre>
                  </div>
                </div>
              )}

              {/* Actions */}
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setIsLeadDialogOpen(false)}>
                  Close
                </Button>
                {isEditMode ? (
                  <Button onClick={() => handleLeadAction(selectedLead.id, 'update', selectedLead)}>
                    Save Changes
                  </Button>
                ) : (
                  <Button onClick={() => setIsEditMode(true)}>
                    Edit Lead
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
