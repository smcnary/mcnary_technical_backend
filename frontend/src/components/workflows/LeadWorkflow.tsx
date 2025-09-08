'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '@/components/ui/table';
import { 
  Plus, 
  Phone, 
  Mail, 
  Calendar, 
  User, 
  Building2,
  CheckCircle,
  Clock,
  AlertCircle,
  TrendingUp,
  Target,
  Loader2,
  Eye,
  Edit,
  Trash2
} from 'lucide-react';
import { Lead } from '@/services/api';

interface LeadWorkflowProps {
  onLeadCreated?: (lead: Lead) => void;
  onLeadUpdated?: (lead: Lead) => void;
}

export default function LeadWorkflow({ onLeadCreated, onLeadUpdated }: LeadWorkflowProps) {
  const { user, isClientAdmin, isClientStaff } = useAuth();
  const { 
    leads, 
    getLeads, 
    submitLead, 
    updateClient,
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [showCreateForm, setShowCreateForm] = useState(false);
  const [editingLead, setEditingLead] = useState<Lead | null>(null);
  const [selectedLeads, setSelectedLeads] = useState<string[]>([]);
  const [bulkAction, setBulkAction] = useState<string>('');
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    firm: '',
    website: '',
    practiceAreas: [] as string[],
    city: '',
    state: '',
    budget: '',
    timeline: '',
    notes: '',
    status: 'pending' as 'pending' | 'contacted' | 'qualified' | 'disqualified'
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isLoading = getLoadingState('leads');
  const leadsError = getErrorState('leads');

  const practiceAreas = [
    'Personal Injury',
    'Criminal Defense',
    'Family Law',
    'Estate Planning',
    'Business Law',
    'Real Estate',
    'Employment Law',
    'Immigration',
    'Bankruptcy',
    'Other'
  ];

  useEffect(() => {
    loadLeads();
  }, []);

  const loadLeads = async () => {
    try {
      clearError('leads');
      await getLeads({ per_page: 50, sort: '-createdAt' });
    } catch (err) {
      console.error('Failed to load leads:', err);
    }
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  const handlePracticeAreaToggle = (area: string) => {
    setFormData(prev => ({
      ...prev,
      practiceAreas: prev.practiceAreas.includes(area)
        ? prev.practiceAreas.filter(pa => pa !== area)
        : [...prev.practiceAreas, area]
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name || !formData.email) return;

    setIsSubmitting(true);
    setError(null);

    try {
      const leadData = {
        ...formData,
        consent: true,
        practiceAreas: formData.practiceAreas
      };

      if (editingLead) {
        // Update existing lead
        const updatedLead = await updateClient(editingLead.id, leadData);
        onLeadUpdated?.(updatedLead);
        setEditingLead(null);
      } else {
        // Create new lead
        const newLead = await submitLead(leadData);
        onLeadCreated?.(newLead);
      }

      // Reset form
      setFormData({
        name: '',
        email: '',
        phone: '',
        firm: '',
        website: '',
        practiceAreas: [],
        city: '',
        state: '',
        budget: '',
        timeline: '',
        notes: '',
        status: 'pending'
      });
      setShowCreateForm(false);
      await loadLeads();
    } catch (err: any) {
      setError(err?.message || 'Failed to save lead');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleBulkAction = async () => {
    if (!bulkAction || selectedLeads.length === 0) return;

    setIsSubmitting(true);
    try {
      // Update multiple leads with the same status
      for (const leadId of selectedLeads) {
        await updateClient(leadId, { status: bulkAction });
      }
      
      setSelectedLeads([]);
      setBulkAction('');
      await loadLeads();
    } catch (err: any) {
      setError(err?.message || 'Failed to update leads');
    } finally {
      setIsSubmitting(false);
    }
  };

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'pending':
        return 'secondary';
      case 'contacted':
        return 'default';
      case 'qualified':
        return 'default';
      case 'disqualified':
        return 'destructive';
      default:
        return 'secondary';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'pending':
        return <Clock className="h-4 w-4 text-yellow-500" />;
      case 'contacted':
        return <Phone className="h-4 w-4 text-blue-500" />;
      case 'qualified':
        return <CheckCircle className="h-4 w-4 text-green-500" />;
      case 'disqualified':
        return <AlertCircle className="h-4 w-4 text-red-500" />;
      default:
        return <Clock className="h-4 w-4 text-gray-500" />;
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const recentLeads = leads.slice(0, 10);
  const pendingLeads = leads.filter(lead => lead.status === 'pending').length;
  const qualifiedLeads = leads.filter(lead => lead.status === 'qualified').length;

  return (
    <div className="space-y-6">
      {/* Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Leads</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{leads.length}</div>
            <p className="text-xs text-muted-foreground">All time</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pending</CardTitle>
            <Clock className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{pendingLeads}</div>
            <p className="text-xs text-muted-foreground">Awaiting contact</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Qualified</CardTitle>
            <CheckCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{qualifiedLeads}</div>
            <p className="text-xs text-muted-foreground">Ready to convert</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Conversion Rate</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {leads.length > 0 ? Math.round((qualifiedLeads / leads.length) * 100) : 0}%
            </div>
            <p className="text-xs text-muted-foreground">Qualified rate</p>
          </CardContent>
        </Card>
      </div>

      {/* Actions Bar */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>Lead Management</CardTitle>
              <CardDescription>Manage your incoming leads and track conversion progress</CardDescription>
            </div>
            <div className="flex items-center gap-2">
              {(isClientAdmin || isClientStaff) && (
                <Button onClick={() => setShowCreateForm(true)}>
                  <Plus className="h-4 w-4 mr-2" />
                  Add Lead
                </Button>
              )}
            </div>
          </div>
        </CardHeader>
        <CardContent>
          {/* Bulk Actions */}
          {selectedLeads.length > 0 && (
            <div className="flex items-center gap-2 mb-4 p-3 bg-blue-50 rounded-lg">
              <span className="text-sm font-medium">
                {selectedLeads.length} lead{selectedLeads.length > 1 ? 's' : ''} selected
              </span>
              <Select value={bulkAction} onValueChange={setBulkAction}>
                <SelectTrigger className="w-40">
                  <SelectValue placeholder="Bulk action" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="contacted">Mark as Contacted</SelectItem>
                  <SelectItem value="qualified">Mark as Qualified</SelectItem>
                  <SelectItem value="disqualified">Mark as Disqualified</SelectItem>
                </SelectContent>
              </Select>
              <Button 
                size="sm" 
                onClick={handleBulkAction}
                disabled={!bulkAction || isSubmitting}
              >
                {isSubmitting ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  'Apply'
                )}
              </Button>
              <Button 
                variant="outline" 
                size="sm" 
                onClick={() => setSelectedLeads([])}
              >
                Cancel
              </Button>
            </div>
          )}

          {/* Error Message */}
          {error && (
            <div className="flex items-center gap-2 text-red-600 bg-red-50 p-3 rounded-lg mb-4">
              <AlertCircle className="w-4 h-4" />
              {error}
            </div>
          )}

          {/* Leads Table */}
          {isLoading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-6 w-6 animate-spin mr-2" />
              Loading leads...
            </div>
          ) : leadsError ? (
            <div className="flex items-center justify-center py-8 text-red-600">
              <AlertCircle className="h-6 w-6 mr-2" />
              {leadsError}
            </div>
          ) : (
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-12">
                      <input
                        type="checkbox"
                        checked={selectedLeads.length === recentLeads.length && recentLeads.length > 0}
                        onChange={(e) => {
                          if (e.target.checked) {
                            setSelectedLeads(recentLeads.map(lead => lead.id));
                          } else {
                            setSelectedLeads([]);
                          }
                        }}
                      />
                    </TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Phone</TableHead>
                    <TableHead>Firm</TableHead>
                    <TableHead>Practice Areas</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Created</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {recentLeads.length === 0 ? (
                    <TableRow>
                      <TableCell colSpan={9} className="text-center py-8 text-muted-foreground">
                        No leads found. Create your first lead to get started.
                      </TableCell>
                    </TableRow>
                  ) : (
                    recentLeads.map((lead) => (
                      <TableRow key={lead.id}>
                        <TableCell>
                          <input
                            type="checkbox"
                            checked={selectedLeads.includes(lead.id)}
                            onChange={(e) => {
                              if (e.target.checked) {
                                setSelectedLeads(prev => [...prev, lead.id]);
                              } else {
                                setSelectedLeads(prev => prev.filter(id => id !== lead.id));
                              }
                            }}
                          />
                        </TableCell>
                        <TableCell className="font-medium">
                          {lead.name || '—'}
                        </TableCell>
                        <TableCell>
                          <div className="flex items-center gap-2">
                            <Mail className="h-4 w-4 text-muted-foreground" />
                            {lead.email}
                          </div>
                        </TableCell>
                        <TableCell>
                          {lead.phone ? (
                            <div className="flex items-center gap-2">
                              <Phone className="h-4 w-4 text-muted-foreground" />
                              {lead.phone}
                            </div>
                          ) : (
                            '—'
                          )}
                        </TableCell>
                        <TableCell>{lead.firm || '—'}</TableCell>
                        <TableCell>
                          {lead.practiceAreas && lead.practiceAreas.length > 0 ? (
                            <div className="flex flex-wrap gap-1">
                              {lead.practiceAreas.slice(0, 2).map((area, index) => (
                                <Badge key={index} variant="outline" className="text-xs">
                                  {area}
                                </Badge>
                              ))}
                              {lead.practiceAreas.length > 2 && (
                                <Badge variant="outline" className="text-xs">
                                  +{lead.practiceAreas.length - 2}
                                </Badge>
                              )}
                            </div>
                          ) : (
                            '—'
                          )}
                        </TableCell>
                        <TableCell>
                          <div className="flex items-center gap-2">
                            {getStatusIcon(lead.status)}
                            <Badge variant={getStatusBadgeVariant(lead.status)}>
                              {lead.status}
                            </Badge>
                          </div>
                        </TableCell>
                        <TableCell>
                          <div className="flex items-center gap-2">
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                            {formatDate(lead.createdAt)}
                          </div>
                        </TableCell>
                        <TableCell className="text-right">
                          <div className="flex items-center justify-end gap-2">
                            <Button variant="outline" size="sm">
                              <Eye className="h-4 w-4 mr-2" />
                              View
                            </Button>
                            {(isClientAdmin || isClientStaff) && (
                              <Button 
                                variant="outline" 
                                size="sm"
                                onClick={() => {
                                  setEditingLead(lead);
                                  setFormData({
                                    name: lead.name || '',
                                    email: lead.email,
                                    phone: lead.phone || '',
                                    firm: lead.firm || '',
                                    website: lead.website || '',
                                    practiceAreas: lead.practiceAreas || [],
                                    city: lead.city || '',
                                    state: lead.state || '',
                                    budget: lead.budget || '',
                                    timeline: lead.timeline || '',
                                    notes: lead.notes || '',
                                    status: lead.status
                                  });
                                  setShowCreateForm(true);
                                }}
                              >
                                <Edit className="h-4 w-4 mr-2" />
                                Edit
                              </Button>
                            )}
                          </div>
                        </TableCell>
                      </TableRow>
                    ))
                  )}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Create/Edit Lead Modal */}
      {showCreateForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <Card className="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <CardHeader>
              <CardTitle>{editingLead ? 'Edit Lead' : 'Add New Lead'}</CardTitle>
              <CardDescription>
                {editingLead ? 'Update lead information' : 'Enter lead details'}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="name">Name *</Label>
                    <Input
                      id="name"
                      value={formData.name}
                      onChange={(e) => handleInputChange('name', e.target.value)}
                      placeholder="Lead's full name"
                      required
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="email">Email *</Label>
                    <Input
                      id="email"
                      type="email"
                      value={formData.email}
                      onChange={(e) => handleInputChange('email', e.target.value)}
                      placeholder="lead@example.com"
                      required
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="phone">Phone</Label>
                    <Input
                      id="phone"
                      value={formData.phone}
                      onChange={(e) => handleInputChange('phone', e.target.value)}
                      placeholder="(555) 123-4567"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="firm">Firm</Label>
                    <Input
                      id="firm"
                      value={formData.firm}
                      onChange={(e) => handleInputChange('firm', e.target.value)}
                      placeholder="Law firm name"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="website">Website</Label>
                  <Input
                    id="website"
                    value={formData.website}
                    onChange={(e) => handleInputChange('website', e.target.value)}
                    placeholder="https://example.com"
                  />
                </div>

                <div className="space-y-2">
                  <Label>Practice Areas</Label>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
                    {practiceAreas.map((area) => (
                      <button
                        key={area}
                        type="button"
                        onClick={() => handlePracticeAreaToggle(area)}
                        className={`p-2 rounded-lg border text-sm ${
                          formData.practiceAreas.includes(area)
                            ? 'border-blue-500 bg-blue-50 text-blue-900'
                            : 'border-gray-200 hover:border-gray-300'
                        }`}
                      >
                        {area}
                      </button>
                    ))}
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="city">City</Label>
                    <Input
                      id="city"
                      value={formData.city}
                      onChange={(e) => handleInputChange('city', e.target.value)}
                      placeholder="City"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="state">State</Label>
                    <Input
                      id="state"
                      value={formData.state}
                      onChange={(e) => handleInputChange('state', e.target.value)}
                      placeholder="State"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="status">Status</Label>
                    <Select value={formData.status} onValueChange={(value) => handleInputChange('status', value)}>
                      <SelectTrigger>
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="contacted">Contacted</SelectItem>
                        <SelectItem value="qualified">Qualified</SelectItem>
                        <SelectItem value="disqualified">Disqualified</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="notes">Notes</Label>
                  <Textarea
                    id="notes"
                    value={formData.notes}
                    onChange={(e) => handleInputChange('notes', e.target.value)}
                    placeholder="Additional notes about this lead..."
                    rows={3}
                  />
                </div>

                <div className="flex items-center justify-end gap-2 pt-4">
                  <Button 
                    type="button" 
                    variant="outline" 
                    onClick={() => {
                      setShowCreateForm(false);
                      setEditingLead(null);
                    }}
                  >
                    Cancel
                  </Button>
                  <Button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? (
                      <>
                        <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                        Saving...
                      </>
                    ) : (
                      editingLead ? 'Update Lead' : 'Create Lead'
                    )}
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
}
