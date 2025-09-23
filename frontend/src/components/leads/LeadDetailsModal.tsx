'use client';

import React, { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '../ui/dialog';
import { Badge } from '../ui/badge';
import { Button } from '../ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { 
  Mail, 
  Phone, 
  MapPin, 
  Building, 
  Globe, 
  Calendar, 
  User,
  MessageSquare,
  Tag,
  ExternalLink,
  ArrowRight,
  ArrowLeft
} from 'lucide-react';
import TechStackDisplay from './TechStackDisplay';
import { apiService } from '../../services/api';
import { TechStackResult } from '../../services/techStackService';

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
  statusLabel?: string;
  source?: string;
  client?: string;
  utmJson?: any[];
  techStack?: {
    url: string;
    technologies: Array<{
      name: string;
      confidence: number;
      version?: string;
      categories: string[];
      website?: string;
      description?: string;
    }>;
    lastAnalyzed?: string;
    error?: string;
  };
  createdAt?: string;
  updatedAt?: string;
}

interface LeadDetailsModalProps {
  lead: Lead | null;
  isOpen: boolean;
  onClose: () => void;
  onStatusChange?: (leadId: string, newStatus: string) => void;
}

export default function LeadDetailsModal({ 
  lead, 
  isOpen, 
  onClose, 
  onStatusChange 
}: LeadDetailsModalProps) {
  const [techStack, setTechStack] = useState<TechStackResult | undefined>(lead?.techStack);
  const [isAnalyzingTechStack, setIsAnalyzingTechStack] = useState(false);

  // Update tech stack when lead changes
  useEffect(() => {
    if (lead?.techStack) {
      setTechStack(lead.techStack);
    } else {
      setTechStack(undefined);
    }
  }, [lead]);

  if (!lead) return null;

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
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
    const statusMap: Record<string, string> = {
      'new_lead': 'New Lead',
      'contacted': 'Contacted',
      'interview_scheduled': 'Interview Scheduled',
      'interview_completed': 'Interview Completed',
      'application_received': 'Application Received',
      'audit_in_progress': 'Audit In Progress',
      'audit_complete': 'Audit Complete',
      'enrolled': 'Enrolled'
    };
    return statusMap[status] || status;
  };

  const handleStatusChange = (newStatus: string) => {
    if (onStatusChange) {
      onStatusChange(lead.id, newStatus);
    }
  };

  const handleAnalyzeTechStack = async () => {
    if (!lead.website) return;
    
    setIsAnalyzingTechStack(true);
    try {
      const result = await apiService.analyzeLeadTechStack(lead.id);
      setTechStack(result);
    } catch (error) {
      console.error('Error analyzing tech stack:', error);
      setTechStack({
        url: lead.website,
        technologies: [],
        error: 'Failed to analyze technology stack'
      });
    } finally {
      setIsAnalyzingTechStack(false);
    }
  };

  const statusOptions = [
    { value: 'new_lead', label: 'New Lead', group: 'Leads' },
    { value: 'contacted', label: 'Contacted', group: 'Leads' },
    { value: 'interview_scheduled', label: 'Interview Scheduled', group: 'Interviews' },
    { value: 'interview_completed', label: 'Interview Completed', group: 'Interviews' },
    { value: 'application_received', label: 'Application Received', group: 'Applications' },
    { value: 'audit_in_progress', label: 'Audit In Progress', group: 'Audits' },
    { value: 'audit_complete', label: 'Audit Complete', group: 'Audits' },
    { value: 'enrolled', label: 'Enrolled', group: 'Enrolled' }
  ];

  const getNextStatus = (currentStatus: string) => {
    const statusFlow = {
      'new_lead': 'contacted',
      'contacted': 'interview_scheduled',
      'interview_scheduled': 'interview_completed',
      'interview_completed': 'application_received',
      'application_received': 'audit_in_progress',
      'audit_in_progress': 'audit_complete',
      'audit_complete': 'enrolled',
      'enrolled': null
    };
    return statusFlow[currentStatus as keyof typeof statusFlow] || null;
  };

  const getPreviousStatus = (currentStatus: string) => {
    const reverseStatusFlow = {
      'contacted': 'new_lead',
      'interview_scheduled': 'contacted',
      'interview_completed': 'interview_scheduled',
      'application_received': 'interview_completed',
      'audit_in_progress': 'application_received',
      'audit_complete': 'audit_in_progress',
      'enrolled': 'audit_complete'
    };
    return reverseStatusFlow[currentStatus as keyof typeof reverseStatusFlow] || null;
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-3">
            <User className="h-6 w-6" />
            {lead.fullName}
            <Badge variant={getStatusBadgeVariant(lead.status)} className="ml-auto">
              {getStatusLabel(lead.status)}
            </Badge>
          </DialogTitle>
        </DialogHeader>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Contact Information */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Contact Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {lead.email && (
                <div className="flex items-center gap-3">
                  <Mail className="h-4 w-4 text-gray-500" />
                  <div>
                    <p className="text-sm font-medium">Email</p>
                    <a 
                      href={`mailto:${lead.email}`}
                      className="text-blue-600 hover:text-blue-800 text-sm"
                    >
                      {lead.email}
                    </a>
                  </div>
                </div>
              )}
              
              {lead.phone && (
                <div className="flex items-center gap-3">
                  <Phone className="h-4 w-4 text-gray-500" />
                  <div>
                    <p className="text-sm font-medium">Phone</p>
                    <a 
                      href={`tel:${lead.phone}`}
                      className="text-blue-600 hover:text-blue-800 text-sm"
                    >
                      {lead.phone}
                    </a>
                  </div>
                </div>
              )}
              
              {lead.firm && (
                <div className="flex items-center gap-3">
                  <Building className="h-4 w-4 text-gray-500" />
                  <div>
                    <p className="text-sm font-medium">Firm</p>
                    <p className="text-sm text-gray-700">{lead.firm}</p>
                  </div>
                </div>
              )}
              
              {lead.website && (
                <div className="flex items-center gap-3">
                  <Globe className="h-4 w-4 text-gray-500" />
                  <div>
                    <p className="text-sm font-medium">Website</p>
                    <a 
                      href={lead.website}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1"
                    >
                      {lead.website}
                      <ExternalLink className="h-3 w-3" />
                    </a>
                  </div>
                </div>
              )}
              
              {(lead.city || lead.state) && (
                <div className="flex items-center gap-3">
                  <MapPin className="h-4 w-4 text-gray-500" />
                  <div>
                    <p className="text-sm font-medium">Location</p>
                    <p className="text-sm text-gray-700">
                      {[lead.city, lead.state, lead.zipCode].filter(Boolean).join(', ')}
                    </p>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Lead Details */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Lead Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center gap-3">
                <Tag className="h-4 w-4 text-gray-500" />
                <div>
                  <p className="text-sm font-medium">Source</p>
                  <p className="text-sm text-gray-700">{lead.source || 'Unknown'}</p>
                </div>
              </div>
              
              {lead.practiceAreas && lead.practiceAreas.length > 0 && (
                <div className="flex items-start gap-3">
                  <Tag className="h-4 w-4 text-gray-500 mt-1" />
                  <div>
                    <p className="text-sm font-medium">Practice Areas</p>
                    <div className="flex flex-wrap gap-1 mt-1">
                      {lead.practiceAreas.map((area, index) => (
                        <Badge key={index} variant="outline" className="text-xs">
                          {area}
                        </Badge>
                      ))}
                    </div>
                  </div>
                </div>
              )}
              
              {lead.message && (
                <div className="flex items-start gap-3">
                  <MessageSquare className="h-4 w-4 text-gray-500 mt-1" />
                  <div>
                    <p className="text-sm font-medium">Message</p>
                    <p className="text-sm text-gray-700 mt-1">{lead.message}</p>
                  </div>
                </div>
              )}
              
              <div className="flex items-center gap-3">
                <Calendar className="h-4 w-4 text-gray-500" />
                <div>
                  <p className="text-sm font-medium">Created</p>
                  <p className="text-sm text-gray-700">
                    {lead.createdAt ? formatDate(lead.createdAt) : 'Unknown'}
                  </p>
                </div>
              </div>
              
              {lead.updatedAt && lead.updatedAt !== lead.createdAt && (
                <div className="flex items-center gap-3">
                  <Calendar className="h-4 w-4 text-gray-500" />
                  <div>
                    <p className="text-sm font-medium">Last Updated</p>
                    <p className="text-sm text-gray-700">
                      {formatDate(lead.updatedAt)}
                    </p>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Technology Stack */}
        {lead.website && (
          <TechStackDisplay
            techStack={techStack}
            isLoading={isAnalyzingTechStack}
            onAnalyze={handleAnalyzeTechStack}
            website={lead.website}
          />
        )}

        {/* Status Change Section */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Change Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
              {statusOptions.map((status) => (
                <Button
                  key={status.value}
                  variant={lead.status === status.value ? "default" : "outline"}
                  size="sm"
                  onClick={() => handleStatusChange(status.value)}
                  className="text-xs"
                >
                  {status.label}
                </Button>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Quick Actions */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Quick Actions</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex flex-wrap gap-2 mb-4">
              {getNextStatus(lead.status) && (
                <Button 
                  onClick={() => handleStatusChange(getNextStatus(lead.status)!)} 
                  className="bg-green-600 hover:bg-green-700"
                >
                  <ArrowRight className="h-4 w-4 mr-2" />
                  Move to {statusOptions.find(s => s.value === getNextStatus(lead.status))?.label}
                </Button>
              )}
              {getPreviousStatus(lead.status) && (
                <Button 
                  variant="outline" 
                  onClick={() => handleStatusChange(getPreviousStatus(lead.status)!)} 
                >
                  <ArrowLeft className="h-4 w-4 mr-2" />
                  Move to {statusOptions.find(s => s.value === getPreviousStatus(lead.status))?.label}
                </Button>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Actions */}
        <div className="flex justify-between items-center gap-2 pt-4 border-t">
          <div className="flex gap-2">
            {lead.email && (
              <Button asChild>
                <a href={`mailto:${lead.email}`}>
                  <Mail className="h-4 w-4 mr-2" />
                  Send Email
                </a>
              </Button>
            )}
            {lead.phone && (
              <Button variant="outline" asChild>
                <a href={`tel:${lead.phone}`}>
                  <Phone className="h-4 w-4 mr-2" />
                  Call
                </a>
              </Button>
            )}
          </div>
          <Button variant="outline" onClick={onClose}>
            Close
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
