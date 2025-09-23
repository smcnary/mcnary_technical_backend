'use client';

import { useState, useEffect, useCallback } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '../ui/dialog';
import { Badge } from '../ui/badge';
import { Button } from '../ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '../ui/tabs';
import { DateTimePicker } from '../ui/datetime-picker';
import { 
  Phone, 
  Building, 
  Globe, 
  User,
  MessageSquare,
  Tag,
  ExternalLink,
  ArrowRight,
  ArrowLeft,
  Save,
  X,
  PhoneCall,
  FileText,
  Mail,
  Info,
  StickyNote,
  CalendarDays
} from 'lucide-react';
import TechStackDisplay from './TechStackDisplay';
import CallModal from './CallModal';
import { apiService, Lead } from '../../services/api';
import { TechStackResult } from '../../services/techStackService';


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
  const [currentStatus, setCurrentStatus] = useState(lead?.status || '');
  const [isSaving, setIsSaving] = useState(false);
  const [isClosing, setIsClosing] = useState(false);
  const [isCallModalOpen, setIsCallModalOpen] = useState(false);
  const [activeTab, setActiveTab] = useState('details');
  const [notes, setNotes] = useState('');
  const [isSavingNotes, setIsSavingNotes] = useState(false);
  const [interviewScheduled, setInterviewScheduled] = useState<Date | undefined>(undefined);
  const [followUpDate, setFollowUpDate] = useState<Date | undefined>(undefined);
  const [isSavingDates, setIsSavingDates] = useState(false);

  // Update tech stack when lead changes
  useEffect(() => {
    if (lead?.techStack) {
      setTechStack(lead.techStack);
    } else {
      setTechStack(undefined);
    }
  }, [lead]);

  // Update current status when lead changes
  useEffect(() => {
    if (lead?.status) {
      setCurrentStatus(lead.status);
    }
  }, [lead]);

  // Load date fields when lead changes
  useEffect(() => {
    if (lead) {
      // Parse dates from lead data (assuming they're stored as ISO strings)
      if (lead.interviewScheduled) {
        setInterviewScheduled(new Date(lead.interviewScheduled));
      }
      if (lead.followUpDate) {
        setFollowUpDate(new Date(lead.followUpDate));
      }
    }
  }, [lead]);

  const loadNotes = useCallback(async () => {
    if (!lead?.id) return;
    
    try {
      // Try to load from API first, fallback to localStorage
      try {
        const notesData = await apiService.getLeadNotes(lead.id);
        setNotes(notesData.notes || '');
      } catch (apiError) {
        console.log('API call failed, using localStorage fallback:', apiError);
        // Fallback: Load from localStorage
        const savedNotes = localStorage.getItem(`lead_notes_${lead.id}`);
        setNotes(savedNotes || '');
      }
    } catch (error) {
      console.error('Failed to load notes:', error);
    }
  }, [lead?.id]);

  // Load notes when lead changes
  useEffect(() => {
    if (lead?.id) {
      loadNotes();
    }
  }, [lead?.id, loadNotes]);

  const handleSaveNotes = async () => {
    if (!lead?.id) return;
    
    setIsSavingNotes(true);
    try {
      // Try to save to API first, fallback to localStorage
      try {
        await apiService.saveLeadNotes(lead.id, notes);
        console.log(`Notes saved to database for lead ${lead.id}`);
      } catch (apiError) {
        console.log('API call failed, saving to localStorage:', apiError);
        // Fallback: Save to localStorage
        localStorage.setItem(`lead_notes_${lead.id}`, notes);
        console.log(`Notes saved to localStorage for lead ${lead.id}`);
      }
    } catch (error) {
      console.error('Failed to save notes:', error);
    } finally {
      setIsSavingNotes(false);
    }
  };

  const handleSaveDates = async () => {
    if (!lead?.id) return;
    
    setIsSavingDates(true);
    try {
      // Try to save to API first, fallback to localStorage
      try {
        await apiService.updateLead(lead.id, {
          interviewScheduled: interviewScheduled?.toISOString(),
          followUpDate: followUpDate?.toISOString()
        });
        console.log(`Dates saved to database for lead ${lead.id}`);
      } catch (apiError) {
        console.log('API call failed, saving to localStorage:', apiError);
        // Fallback: Save to localStorage
        localStorage.setItem(`lead_interview_${lead.id}`, interviewScheduled?.toISOString() || '');
        localStorage.setItem(`lead_followup_${lead.id}`, followUpDate?.toISOString() || '');
        console.log(`Dates saved to localStorage for lead ${lead.id}`);
      }
    } catch (error) {
      console.error('Failed to save dates:', error);
    } finally {
      setIsSavingDates(false);
    }
  };

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
    setCurrentStatus(newStatus);
  };

  const handleSave = async () => {
    if (!lead || currentStatus === lead.status) return;
    
    setIsSaving(true);
    try {
      await onStatusChange?.(lead.id, currentStatus);
      console.log(`Lead ${lead.fullName} status saved as ${currentStatus}`);
    } catch (error) {
      console.error('Failed to save lead status:', error);
      // Revert to original status on error
      setCurrentStatus(lead.status);
    } finally {
      setIsSaving(false);
    }
  };

  const handleCloseLead = async () => {
    if (!lead) return;
    
    setIsClosing(true);
    try {
      await onStatusChange?.(lead.id, 'closed');
      console.log(`Lead ${lead.fullName} closed`);
      onClose(); // Close the modal after successfully closing the lead
    } catch (error) {
      console.error('Failed to close lead:', error);
    } finally {
      setIsClosing(false);
    }
  };

  const handleCallLead = () => {
    if (lead?.phone) {
      setIsCallModalOpen(true);
    }
  };

  const handleCallModalClose = () => {
    setIsCallModalOpen(false);
  };

  const handleHangup = () => {
    console.log('Call ended');
    handleCallModalClose();
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
    { value: 'enrolled', label: 'Enrolled', group: 'Enrolled' },
    { value: 'closed', label: 'Closed', group: 'Closed' }
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
      <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto" aria-describedby="lead-details-description">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-3">
            <User className="h-6 w-6" />
            {lead.fullName}
            <Badge variant={getStatusBadgeVariant(currentStatus)} className="ml-auto">
              {getStatusLabel(currentStatus)}
            </Badge>
          </DialogTitle>
          <p id="lead-details-description" className="sr-only">
            Detailed information about lead {lead.fullName} including contact details, status, notes, and scheduling information.
          </p>
        </DialogHeader>

        <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="details" className="flex items-center gap-2">
              <Info className="h-4 w-4" />
              Details
            </TabsTrigger>
            <TabsTrigger value="notes" className="flex items-center gap-2">
              <StickyNote className="h-4 w-4" />
              Notes
            </TabsTrigger>
            <TabsTrigger value="emails" className="flex items-center gap-2">
              <Mail className="h-4 w-4" />
              Emails
            </TabsTrigger>
            <TabsTrigger value="documents" className="flex items-center gap-2">
              <FileText className="h-4 w-4" />
              Documents
            </TabsTrigger>
          </TabsList>

          {/* Details Tab */}
          <TabsContent value="details" className="space-y-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Contact Information */}
              <Card>
                <CardHeader>
                  <CardTitle className="text-lg">Contact Information</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {lead.phone && (
                    <div className="flex items-center gap-3">
                      <Phone className="h-4 w-4 text-gray-500" />
                      <div>
                        <p className="text-sm font-medium">Phone</p>
                        <p 
                          className="text-sm font-mono text-blue-600 hover:text-blue-800 cursor-pointer transition-colors"
                          onClick={handleCallLead}
                        >
                          {lead.phone}
                        </p>
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
                    <CalendarDays className="h-4 w-4 text-gray-500" />
                    <div>
                      <p className="text-sm font-medium">Created</p>
                      <p className="text-sm text-gray-700">
                        {lead.createdAt ? formatDate(lead.createdAt) : 'Unknown'}
                      </p>
                    </div>
                  </div>
                  
                  {lead.updatedAt && lead.updatedAt !== lead.createdAt && (
                    <div className="flex items-center gap-3">
                      <CalendarDays className="h-4 w-4 text-gray-500" />
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
                      variant={currentStatus === status.value ? "default" : "outline"}
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
                  {getNextStatus(currentStatus) && (
                    <Button 
                      onClick={() => handleStatusChange(getNextStatus(currentStatus)!)} 
                      className="bg-green-600 hover:bg-green-700"
                    >
                      <ArrowRight className="h-4 w-4 mr-2" />
                      Move to {statusOptions.find(s => s.value === getNextStatus(currentStatus))?.label}
                    </Button>
                  )}
                  {getPreviousStatus(currentStatus) && (
                    <Button 
                      variant="outline" 
                      onClick={() => handleStatusChange(getPreviousStatus(currentStatus)!)} 
                    >
                      <ArrowLeft className="h-4 w-4 mr-2" />
                      Move to {statusOptions.find(s => s.value === getPreviousStatus(currentStatus))?.label}
                    </Button>
                  )}
                </div>
              </CardContent>
            </Card>

            {/* Scheduling */}
            <Card>
              <CardHeader>
                <CardTitle className="text-lg flex items-center gap-2">
                  <CalendarDays className="h-5 w-5" />
                  Scheduling
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="space-y-2">
                  <label className="text-sm font-medium text-gray-700">
                    Interview Scheduled
                  </label>
                  <DateTimePicker
                    date={interviewScheduled}
                    onDateChange={setInterviewScheduled}
                    placeholder="Select interview date and time"
                  />
                </div>
                
                <div className="space-y-2">
                  <label className="text-sm font-medium text-gray-700">
                    Follow Up Date
                  </label>
                  <DateTimePicker
                    date={followUpDate}
                    onDateChange={setFollowUpDate}
                    placeholder="Select follow up date and time"
                  />
                </div>
                
                <div className="flex justify-end">
                  <Button 
                    onClick={handleSaveDates}
                    disabled={isSavingDates}
                    className="bg-blue-600 hover:bg-blue-700"
                  >
                    <Save className="h-4 w-4 mr-2" />
                    {isSavingDates ? 'Saving...' : 'Save Dates'}
                  </Button>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          {/* Notes Tab */}
          <TabsContent value="notes" className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Lead Notes</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <label htmlFor="notes" className="text-sm font-medium text-gray-700 mb-2 block">
                    Add notes about this lead
                  </label>
                  <textarea
                    id="notes"
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                    placeholder="Enter your notes about this lead..."
                    className="w-full h-32 p-3 border border-gray-300 rounded-md resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                
                <div className="flex justify-between items-center">
                  <div className="text-sm text-gray-500">
                    {notes.length} characters
                  </div>
                  <Button 
                    onClick={handleSaveNotes}
                    disabled={isSavingNotes}
                    className="bg-blue-600 hover:bg-blue-700"
                  >
                    <Save className="h-4 w-4 mr-2" />
                    {isSavingNotes ? 'Saving...' : 'Save Notes'}
                  </Button>
                </div>
              </CardContent>
            </Card>

            {/* Notes History */}
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Notes History</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center py-4">
                  <StickyNote className="h-8 w-8 text-gray-400 mx-auto mb-2" />
                  <p className="text-sm text-gray-600">
                    Notes history will appear here when you start adding notes to this lead.
                  </p>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          {/* Emails Tab */}
          <TabsContent value="emails" className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Email Communication</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center py-8">
                  <Mail className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">No emails yet</h3>
                  <p className="text-gray-600 mb-4">
                    Email communication history will appear here when you start corresponding with this lead.
                  </p>
                  <Button className="bg-blue-600 hover:bg-blue-700">
                    <Mail className="h-4 w-4 mr-2" />
                    Send Email
                  </Button>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          {/* Documents Tab */}
          <TabsContent value="documents" className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Documents & Files</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center py-8">
                  <FileText className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">No documents yet</h3>
                  <p className="text-gray-600 mb-4">
                    Upload and manage documents related to this lead. Contracts, proposals, and other files will appear here.
                  </p>
                  <Button className="bg-green-600 hover:bg-green-700">
                    <FileText className="h-4 w-4 mr-2" />
                    Upload Document
                  </Button>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>

        {/* Actions */}
        <div className="flex justify-between items-center gap-2 pt-4 border-t">
          <div className="flex gap-2">
            {lead.phone && (
              <Button onClick={handleCallLead} className="bg-green-600 hover:bg-green-700">
                <PhoneCall className="h-4 w-4 mr-2" />
                Call Lead
              </Button>
            )}
          </div>
          <div className="flex gap-2">
            {currentStatus !== lead.status && (
              <Button 
                onClick={handleSave}
                disabled={isSaving}
                className="bg-blue-600 hover:bg-blue-700"
              >
                <Save className="h-4 w-4 mr-2" />
                {isSaving ? 'Saving...' : 'Save Status'}
              </Button>
            )}
            <Button 
              onClick={handleCloseLead}
              disabled={isClosing}
              variant="destructive"
            >
              <X className="h-4 w-4 mr-2" />
              {isClosing ? 'Closing...' : 'Close Lead'}
            </Button>
            <Button variant="outline" onClick={onClose}>
              Cancel
            </Button>
          </div>
        </div>

        {/* Call Modal */}
        <CallModal
          lead={lead}
          isOpen={isCallModalOpen}
          onClose={handleCallModalClose}
          onHangup={handleHangup}
        />
      </DialogContent>
    </Dialog>
  );
}
