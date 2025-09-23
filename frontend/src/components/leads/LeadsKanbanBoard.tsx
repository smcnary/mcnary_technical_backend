'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Badge } from '../ui/badge';
import { 
  Users, 
  MessageSquare, 
  FileText, 
  Search, 
  CheckCircle, 
  Phone,
  Mail,
  Phone as PhoneIcon,
  MapPin,
  Building,
  Calendar,
  ArrowRight
} from 'lucide-react';

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
  createdAt?: string;
  updatedAt?: string;
}

interface LeadsKanbanBoardProps {
  leads: Lead[];
  onLeadClick?: (lead: Lead) => void;
}

interface StatusColumn {
  id: string;
  title: string;
  icon: React.ReactNode;
  color: string;
  bgColor: string;
  statuses: string[];
}

const statusColumns: StatusColumn[] = [
  {
    id: 'leads',
    title: 'Leads',
    icon: <Users className="h-5 w-5" />,
    color: 'text-blue-600',
    bgColor: 'bg-blue-50 dark:bg-blue-950/20',
    statuses: ['new_lead', 'contacted']
  },
  {
    id: 'interviews',
    title: 'Interviews',
    icon: <MessageSquare className="h-5 w-5" />,
    color: 'text-green-600',
    bgColor: 'bg-green-50 dark:bg-green-950/20',
    statuses: ['interview_scheduled', 'interview_completed']
  },
  {
    id: 'applications',
    title: 'Applications',
    icon: <FileText className="h-5 w-5" />,
    color: 'text-orange-600',
    bgColor: 'bg-orange-50 dark:bg-orange-950/20',
    statuses: ['application_received']
  },
  {
    id: 'audits',
    title: 'Audits',
    icon: <Search className="h-5 w-5" />,
    color: 'text-purple-600',
    bgColor: 'bg-purple-50 dark:bg-purple-950/20',
    statuses: ['audit_in_progress', 'audit_complete']
  },
  {
    id: 'enrolled',
    title: 'Enrolled',
    icon: <CheckCircle className="h-5 w-5" />,
    color: 'text-emerald-600',
    bgColor: 'bg-emerald-50 dark:bg-emerald-950/20',
    statuses: ['enrolled']
  },
  {
    id: 'openphone',
    title: 'OpenPhone',
    icon: <Phone className="h-5 w-5" />,
    color: 'text-cyan-600',
    bgColor: 'bg-cyan-50 dark:bg-cyan-950/20',
    statuses: [] // This would be for leads that have been synced to OpenPhone
  }
];

export default function LeadsKanbanBoard({ leads, onLeadClick }: LeadsKanbanBoardProps) {
  const [isClient, setIsClient] = useState(false);

  // Prevent hydration mismatch
  useEffect(() => {
    setIsClient(true);
  }, []);

  // Debug: Log leads data
  useEffect(() => {
    console.log('LeadsKanbanBoard received leads:', leads);
    console.log('LeadsKanbanBoard leads count:', leads.length);
    if (leads.length > 0) {
      console.log('First lead:', leads[0]);
    }
  }, [leads]);

  const getStatusLabel = (status: string) => {
    const statusMap: Record<string, string> = {
      'new_lead': 'New',
      'contacted': 'Contacted',
      'interview_scheduled': 'Scheduled',
      'interview_completed': 'Completed',
      'application_received': 'Received',
      'audit_in_progress': 'In Progress',
      'audit_complete': 'Complete',
      'enrolled': 'Enrolled'
    };
    return statusMap[status] || status;
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

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const getLeadsForColumn = (column: StatusColumn) => {
    if (column.id === 'openphone') {
      // For OpenPhone, we could filter leads that have been synced
      // For now, return empty array as this would need additional logic
      return [];
    }
    return leads.filter(lead => column.statuses.includes(lead.status));
  };

  // Always show content (removed loading state for debugging)

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 p-6">
      {statusColumns.map((column) => {
        const columnLeads = getLeadsForColumn(column);
        
        return (
          <div key={column.id} className="flex flex-col">
            <Card className="h-full">
              <CardHeader className="pb-3">
                <CardTitle className="flex items-center gap-2 text-sm font-medium">
                  <div className={`p-1 rounded ${column.bgColor}`}>
                    <div className={column.color}>
                      {column.icon}
                    </div>
                  </div>
                  {column.title}
                  <Badge variant="secondary" className="ml-auto text-xs">
                    {columnLeads.length}
                  </Badge>
                </CardTitle>
              </CardHeader>
              <CardContent className="pt-0">
                <div className="space-y-3">
                  {columnLeads.length === 0 ? (
                    <div className="text-center py-4">
                      <p className="text-sm text-gray-500 dark:text-gray-400">
                        {column.id === 'openphone' ? 'Click to view openphone' : 'No leads in this stage'}
                      </p>
                    </div>
                  ) : (
                    columnLeads.map((lead) => (
                      <div
                        key={lead.id}
                        className="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group"
                        onClick={() => onLeadClick?.(lead)}
                      >
                        <div className="flex items-start justify-between mb-2">
                          <h4 className="font-medium text-sm leading-tight">{lead.fullName}</h4>
                          <ArrowRight className="h-3 w-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>
                        
                        <div className="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                          {lead.firm && (
                            <div className="flex items-center gap-1">
                              <Building className="h-3 w-3" />
                              <span className="truncate">{lead.firm}</span>
                            </div>
                          )}
                          
                          {lead.email && (
                            <div className="flex items-center gap-1">
                              <Mail className="h-3 w-3" />
                              <span className="truncate">{lead.email}</span>
                            </div>
                          )}
                          
                          {(lead.city || lead.state) && (
                            <div className="flex items-center gap-1">
                              <MapPin className="h-3 w-3" />
                              <span className="truncate">
                                {[lead.city, lead.state].filter(Boolean).join(', ')}
                              </span>
                            </div>
                          )}
                        </div>
                        
                        <div className="flex items-center justify-between mt-2">
                          <Badge variant={getStatusBadgeVariant(lead.status)} className="text-xs">
                            {getStatusLabel(lead.status)}
                          </Badge>
                          <span className="text-xs text-gray-500">
                            {lead.createdAt ? formatDate(lead.createdAt) : 'Unknown'}
                          </span>
                        </div>
                      </div>
                    ))
                  )}
                </div>
              </CardContent>
            </Card>
          </div>
        );
      })}
    </div>
  );
}
