'use client';

import React, { useState, useEffect } from 'react';
import { useData } from '../../hooks/useData';
import { useAuth } from '../../hooks/useAuth';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Input } from '../ui/input';
import { 
  Users, 
  Plus, 
  Upload, 
  Search, 
  Filter,
  MoreHorizontal,
  Mail,
  Phone,
  MapPin,
  Building,
  Calendar,
  Database,
  X
} from 'lucide-react';
import CsvUploadModal from './CsvUploadModal';
import LeadgenImportModal from './LeadgenImportModal';
import LeadStatistics from './LeadStatistics';
import ProtectedRoute from '../auth/ProtectedRoute';

interface Lead {
  id: string;
  fullName: string;
  email: string;
  phone?: string;
  firm?: string;
  website?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  message?: string;
  practiceAreas?: string[];
  status: string;
  createdAt: string;
  updatedAt: string;
}

export default function LeadsManagement() {
  const { user, isAdmin, isSalesConsultant } = useAuth();
  const { 
    leads, 
    getLeads, 
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [isCsvModalOpen, setIsCsvModalOpen] = useState(false);
  const [isLeadgenModalOpen, setIsLeadgenModalOpen] = useState(false);
  const [selectedLead, setSelectedLead] = useState<Lead | null>(null);
  const [filteredLeads, setFilteredLeads] = useState<Lead[]>([]);

  const isLoading = getLoadingState('leads');
  const error = getErrorState('leads');

  // Load leads on component mount
  useEffect(() => {
    getLeads();
  }, [getLeads]);

  // Filter leads based on search and status
  useEffect(() => {
    let filtered = leads || [];
    
    if (searchTerm) {
      filtered = filtered.filter(lead => 
        lead.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
        lead.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (lead.firm && lead.firm.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (lead.city && lead.city.toLowerCase().includes(searchTerm.toLowerCase()))
      );
    }
    
    if (statusFilter) {
      filtered = filtered.filter(lead => lead.status === statusFilter);
    }
    
    setFilteredLeads(filtered);
  }, [leads, searchTerm, statusFilter]);

  const handleCsvUploadSuccess = (result: any) => {
    console.log('CSV upload successful:', result);
    // Refresh leads data
    getLeads();
  };

  const handleLeadgenUploadSuccess = (result: any) => {
    console.log('Leadgen upload successful:', result);
    // Refresh leads data
    getLeads();
  };

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case 'new_lead':
        return 'default';
      case 'contacted':
        return 'secondary';
      case 'interview_scheduled':
        return 'outline';
      case 'interview_completed':
        return 'secondary';
      case 'application_received':
        return 'outline';
      case 'audit_in_progress':
        return 'secondary';
      case 'audit_complete':
        return 'outline';
      case 'enrolled':
        return 'default';
      default:
        return 'secondary';
    }
  };

  const getStatusLabel = (status: string) => {
    switch (status) {
      case 'new_lead':
        return 'New Lead';
      case 'contacted':
        return 'Contacted';
      case 'interview_scheduled':
        return 'Interview Scheduled';
      case 'interview_completed':
        return 'Interview Completed';
      case 'application_received':
        return 'Application Received';
      case 'audit_in_progress':
        return 'Audit in Progress';
      case 'audit_complete':
        return 'Audit Complete';
      case 'enrolled':
        return 'Enrolled';
      default:
        return status;
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  if (!isAdmin() && !isSalesConsultant()) {
    return (
      <div className="p-6">
        <Card>
          <CardContent className="p-6">
            <p className="text-center text-gray-500">
              You don't have permission to view leads management.
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Leads Management</h1>
            <p className="text-gray-600 dark:text-gray-400 mt-1">
              Manage and track your SEO leads pipeline
            </p>
          </div>
          <div className="flex gap-3">
            <Button 
              variant="outline" 
              onClick={() => setIsCsvModalOpen(true)}
              className="flex items-center gap-2"
            >
              <Upload className="h-4 w-4" />
              Import CSV
            </Button>
            <Button 
              variant="outline" 
              onClick={() => setIsLeadgenModalOpen(true)}
              className="flex items-center gap-2"
            >
              <Database className="h-4 w-4" />
              Import Leadgen
            </Button>
            <Button className="flex items-center gap-2">
              <Plus className="h-4 w-4" />
              Add Lead
            </Button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <Users className="h-8 w-8 text-blue-600" />
                <div>
                  <p className="text-sm text-gray-600 dark:text-gray-400">Total Leads</p>
                  <p className="text-2xl font-bold">{leads?.length || 0}</p>
                </div>
              </div>
            </CardContent>
          </Card>
          
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="h-8 w-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                  <span className="text-green-600 dark:text-green-400 text-sm font-semibold">N</span>
                </div>
                <div>
                  <p className="text-sm text-gray-600 dark:text-gray-400">New Leads</p>
                  <p className="text-2xl font-bold">
                    {leads?.filter(lead => lead.status === 'new_lead').length || 0}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
          
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="h-8 w-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                  <span className="text-yellow-600 dark:text-yellow-400 text-sm font-semibold">C</span>
                </div>
                <div>
                  <p className="text-sm text-gray-600 dark:text-gray-400">Contacted</p>
                  <p className="text-2xl font-bold">
                    {leads?.filter(lead => lead.status === 'contacted').length || 0}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
          
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="h-8 w-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                  <span className="text-purple-600 dark:text-purple-400 text-sm font-semibold">E</span>
                </div>
                <div>
                  <p className="text-sm text-gray-600 dark:text-gray-400">Enrolled</p>
                  <p className="text-2xl font-bold">
                    {leads?.filter(lead => lead.status === 'enrolled').length || 0}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Filters */}
        <Card>
          <CardContent className="p-4">
            <div className="flex flex-col sm:flex-row gap-4">
              <div className="flex-1">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                  <Input
                    placeholder="Search leads by name, email, firm, or city..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10"
                  />
                </div>
              </div>
              <div className="sm:w-48">
                <select
                  value={statusFilter}
                  onChange={(e) => setStatusFilter(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                >
                  <option value="">All Statuses</option>
                  <option value="new_lead">New Lead</option>
                  <option value="contacted">Contacted</option>
                  <option value="interview_scheduled">Interview Scheduled</option>
                  <option value="interview_completed">Interview Completed</option>
                  <option value="application_received">Application Received</option>
                  <option value="audit_in_progress">Audit in Progress</option>
                  <option value="audit_complete">Audit Complete</option>
                  <option value="enrolled">Enrolled</option>
                </select>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Error Display */}
        {error && (
          <Card className="border-red-200 bg-red-50 dark:bg-red-950/20">
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <p className="text-red-800 dark:text-red-200">{error}</p>
                <Button variant="ghost" size="sm" onClick={clearError}>
                  Dismiss
                </Button>
              </div>
            </CardContent>
          </Card>
        )}

        {/* Leads List */}
        <Card>
          <CardHeader>
            <CardTitle>Leads ({filteredLeads.length})</CardTitle>
            <CardDescription>
              {isLoading ? 'Loading leads...' : 'Manage your leads pipeline'}
            </CardDescription>
          </CardHeader>
          <CardContent>
            {isLoading ? (
              <div className="flex items-center justify-center py-8">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span className="ml-2 text-gray-600 dark:text-gray-400">Loading leads...</span>
              </div>
            ) : filteredLeads.length === 0 ? (
              <div className="text-center py-8">
                <Users className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                <p className="text-gray-500 dark:text-gray-400">
                  {searchTerm || statusFilter ? 'No leads match your filters.' : 'No leads found.'}
                </p>
                {!searchTerm && !statusFilter && (
                  <Button 
                    className="mt-4" 
                    onClick={() => setIsCsvModalOpen(true)}
                  >
                    Import your first leads
                  </Button>
                )}
              </div>
            ) : (
              <div className="space-y-4">
                {filteredLeads.map((lead) => (
                  <div 
                    key={lead.id} 
                    className="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer"
                    onClick={() => setSelectedLead(lead)}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-3 mb-2">
                          <h3 className="font-semibold text-lg">{lead.fullName}</h3>
                          <Badge variant={getStatusBadgeVariant(lead.status)}>
                            {getStatusLabel(lead.status)}
                          </Badge>
                        </div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm text-gray-600 dark:text-gray-400">
                          {lead.email && (
                            <div className="flex items-center gap-2">
                              <Mail className="h-4 w-4" />
                              <span>{lead.email}</span>
                            </div>
                          )}
                          
                          {lead.phone && (
                            <div className="flex items-center gap-2">
                              <Phone className="h-4 w-4" />
                              <span>{lead.phone}</span>
                            </div>
                          )}
                          
                          {lead.firm && (
                            <div className="flex items-center gap-2">
                              <Building className="h-4 w-4" />
                              <span>{lead.firm}</span>
                            </div>
                          )}
                          
                          {(lead.city || lead.state) && (
                            <div className="flex items-center gap-2">
                              <MapPin className="h-4 w-4" />
                              <span>{[lead.city, lead.state].filter(Boolean).join(', ')}</span>
                            </div>
                          )}
                          
                          <div className="flex items-center gap-2">
                            <Calendar className="h-4 w-4" />
                            <span>Added {formatDate(lead.createdAt)}</span>
                          </div>
                        </div>
                        
                        {lead.message && (
                          <p className="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                            {lead.message}
                          </p>
                        )}
                        
                        {lead.practiceAreas && lead.practiceAreas.length > 0 && (
                          <div className="mt-2 flex flex-wrap gap-1">
                            {lead.practiceAreas.map((area, index) => (
                              <Badge key={index} variant="outline" className="text-xs">
                                {area}
                              </Badge>
                            ))}
                          </div>
                        )}
                      </div>
                      
                      <Button variant="ghost" size="sm">
                        <MoreHorizontal className="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Lead Statistics Modal */}
        {selectedLead && (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <Card className="w-full max-w-4xl max-h-[90vh] overflow-y-auto">
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
                <div>
                  <CardTitle>Lead Statistics</CardTitle>
                  <CardDescription>Track interactions for {selectedLead.fullName}</CardDescription>
                </div>
                <Button variant="ghost" size="sm" onClick={() => setSelectedLead(null)}>
                  <X className="h-4 w-4" />
                </Button>
              </CardHeader>
              <CardContent>
                <LeadStatistics leadId={selectedLead.id} leadName={selectedLead.fullName} />
              </CardContent>
            </Card>
          </div>
        )}

        {/* CSV Upload Modal */}
        <CsvUploadModal
          isOpen={isCsvModalOpen}
          onClose={() => setIsCsvModalOpen(false)}
          onSuccess={handleCsvUploadSuccess}
        />

        {/* Leadgen Import Modal */}
        <LeadgenImportModal
          isOpen={isLeadgenModalOpen}
          onClose={() => setIsLeadgenModalOpen(false)}
          onSuccess={handleLeadgenUploadSuccess}
        />
      </div>
    </ProtectedRoute>
  );
}
