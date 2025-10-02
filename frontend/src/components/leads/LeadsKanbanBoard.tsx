'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Badge } from '../ui/badge';
import { Button } from '../ui/button';
import { 
  Users, 
  MessageSquare, 
  FileText, 
  Search, 
  CheckCircle, 
  Phone,
  Building,
  ArrowRight
} from 'lucide-react';
import {
  DndContext,
  DragEndEvent,
  DragOverEvent,
  DragOverlay,
  DragStartEvent,
  PointerSensor,
  useSensor,
  useSensors,
  closestCenter,
  useDroppable,
} from '@dnd-kit/core';
import {
  SortableContext,
  verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import {
  useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import LeadDetailsModal from './LeadDetailsModal';
import CallModal from './CallModal';
import LeadFormModal from './LeadFormModal';

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
  onLeadStatusChange?: (leadId: string, newStatus: string) => void;
  onLeadCreate?: (lead: Lead) => void;
  onLeadUpdate?: (lead: Lead) => void;
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
    title: 'New Leads',
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
  }
];

// Droppable Column Component
function DroppableColumn({ 
  column, 
  leads, 
  draggedLead, 
  dragOverColumn, 
  onLeadClick,
  onPhoneClick, 
  getStatusBadgeVariant, 
  getStatusLabel, 
  formatDate 
}: {
  column: StatusColumn;
  leads: Lead[];
  draggedLead: Lead | null;
  dragOverColumn: string | null;
  onLeadClick?: (lead: Lead) => void;
  onPhoneClick?: (e: React.MouseEvent, lead: Lead) => void;
  getStatusBadgeVariant: (status: string) => any;
  getStatusLabel: (status: string) => string;
  formatDate: (dateString: string) => string;
}) {
  const { isOver, setNodeRef } = useDroppable({
    id: column.id,
  });

  const columnLeads = leads.filter(lead => column.statuses.includes(lead.status));

  return (
    <div key={column.id} className="flex flex-col" ref={setNodeRef}>
      <Card className={`h-full transition-all duration-200 ${
        dragOverColumn === column.id && draggedLead 
          ? 'ring-2 ring-blue-500 bg-blue-50/50 dark:bg-blue-950/30' 
          : isOver 
          ? 'ring-2 ring-green-500 bg-green-50/50 dark:bg-green-950/30'
          : ''
      }`}>
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
          <div>
            <SortableContext
              items={columnLeads.map(lead => lead.id)}
              strategy={verticalListSortingStrategy}
            >
              <div className="space-y-3">
                {columnLeads.length === 0 ? (
                  <div className="text-center py-4">
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      {dragOverColumn === column.id && draggedLead ? 
                       `Drop to move to ${column.title}` : 
                       'No leads in this stage'}
                    </p>
                  </div>
                ) : (
                  columnLeads.map((lead) => (
                            <SortableLeadItem
                              key={lead.id}
                              lead={lead}
                              onLeadClick={onLeadClick}
                              onPhoneClick={onPhoneClick}
                              getStatusBadgeVariant={getStatusBadgeVariant}
                              getStatusLabel={getStatusLabel}
                              formatDate={formatDate}
                            />
                  ))
                )}
              </div>
            </SortableContext>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

// Sortable Lead Item Component
function SortableLeadItem({ lead, onLeadClick, onPhoneClick, getStatusBadgeVariant, getStatusLabel, formatDate }: {
  lead: Lead;
  onLeadClick?: (lead: Lead) => void;
  onPhoneClick?: (e: React.MouseEvent, lead: Lead) => void;
  getStatusBadgeVariant: (status: string) => any;
  getStatusLabel: (status: string) => string;
  formatDate: (dateString: string) => string;
}) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: lead.id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.5 : 1,
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      {...attributes}
      {...listeners}
      className="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-grab active:cursor-grabbing group"
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
        
        {lead.phone && (
          <div 
            className="flex items-center gap-1 cursor-pointer hover:text-blue-600 transition-colors"
            onClick={(e) => onPhoneClick?.(e, lead)}
          >
            <Phone className="h-3 w-3" />
            <span className="truncate font-mono">{lead.phone}</span>
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
  );
}

export default function LeadsKanbanBoard({ leads, onLeadClick, onLeadStatusChange, onLeadCreate, onLeadUpdate }: LeadsKanbanBoardProps) {
  const [selectedLead, setSelectedLead] = useState<Lead | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [draggedLead, setDraggedLead] = useState<Lead | null>(null);
  const [dragOverColumn, setDragOverColumn] = useState<string | null>(null);
  const [callLead, setCallLead] = useState<Lead | null>(null);
  const [isCallModalOpen, setIsCallModalOpen] = useState(false);
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
  const [editingLead, setEditingLead] = useState<Lead | null>(null);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);

  const sensors = useSensors(
    useSensor(PointerSensor, {
      activationConstraint: {
        distance: 8,
      },
    })
  );

  // Debug: Log when leads change
  useEffect(() => {
    console.log('LeadsKanbanBoard - leads updated:', leads.length, 'leads');
    leads.forEach((lead, index) => {
      console.log(`Lead ${index + 1}: ${lead.fullName} - Status: ${lead.status}`);
    });
  }, [leads]);


  // Debug: Log leads data
  useEffect(() => {
    console.log('LeadsKanbanBoard received leads:', leads);
    console.log('LeadsKanbanBoard leads count:', leads.length);
    if (leads.length > 0) {
      console.log('First lead:', leads[0]);
    }
  }, [leads]);

  const handleLeadClick = (lead: Lead) => {
    setSelectedLead(lead);
    setIsModalOpen(true);
    onLeadClick?.(lead);
  };

  const handleModalClose = () => {
    setIsModalOpen(false);
    setSelectedLead(null);
  };

  const handlePhoneClick = (e: React.MouseEvent, lead: Lead) => {
    e.stopPropagation(); // Prevent triggering the lead click
    setCallLead(lead);
    setIsCallModalOpen(true);
  };

  const handleCallModalClose = () => {
    setIsCallModalOpen(false);
    setCallLead(null);
  };

  const handleHangup = () => {
    console.log('Call ended');
    handleCallModalClose();
  };

  const handleCreateLead = () => {
    setIsCreateModalOpen(true);
  };

  const handleCreateModalClose = () => {
    setIsCreateModalOpen(false);
  };

  const handleCreateSave = (lead: Lead) => {
    onLeadCreate?.(lead);
    setIsCreateModalOpen(false);
  };

  const handleEditLead = (lead: Lead) => {
    setEditingLead(lead);
    setIsEditModalOpen(true);
  };

  const handleEditModalClose = () => {
    setIsEditModalOpen(false);
    setEditingLead(null);
  };

  const handleEditSave = (lead: Lead) => {
    onLeadUpdate?.(lead);
    setIsEditModalOpen(false);
    setEditingLead(null);
  };

  const handleStatusChange = async (leadId: string, newStatus: string) => {
    try {
      await onLeadStatusChange?.(leadId, newStatus);
      // Update the local state immediately for better UX
      setSelectedLead(prev => prev ? { ...prev, status: newStatus } : null);
    } catch (error) {
      console.error('Failed to update lead status:', error);
    }
  };

  const handleDragStart = (event: DragStartEvent) => {
    const leadId = event.active.id as string;
    const lead = leads.find(l => l.id === leadId);
    console.log('🎯 DRAG STARTED for lead:', leadId, lead?.fullName);
    setDraggedLead(lead || null);
  };

  const handleDragOver = (event: DragOverEvent) => {
    const { over } = event;
    if (over) {
      const columnId = over.id as string;
      setDragOverColumn(columnId);
    } else {
      setDragOverColumn(null);
    }
  };

  const handleDragEnd = (event: DragEndEvent) => {
    const { active, over } = event;
    console.log('🎯 DRAG ENDED:', { active: active.id, over: over?.id });
    setDraggedLead(null);
    setDragOverColumn(null);

    if (!over) {
      console.log('❌ No drop target found');
      return;
    }

    // Check if the drop target is a column (not another lead)
    const targetColumn = statusColumns.find(col => col.id === over.id);
    if (!targetColumn) {
      console.log('❌ Drop target is not a valid column:', over.id);
      return;
    }

    console.log('✅ Valid drop target found:', targetColumn.title);

    const leadId = active.id as string;
    const targetColumnId = over.id as string;

    // Find the current lead
    const currentLead = leads.find(lead => lead.id === leadId);
    
    if (!currentLead) {
      console.log('❌ Lead not found:', leadId);
      return;
    }

    // Determine the appropriate new status based on the target column
    let newStatus: string;
    
    switch (targetColumnId) {
      case 'leads':
        // If moving to leads column, set to new_lead if currently contacted, otherwise keep contacted
        newStatus = currentLead.status === 'contacted' ? 'new_lead' : 'contacted';
        break;
      case 'interviews':
        // If moving to interviews, set to interview_scheduled
        newStatus = 'interview_scheduled';
        break;
      case 'applications':
        // If moving to applications, set to application_received
        newStatus = 'application_received';
        break;
      case 'audits':
        // If moving to audits, set to audit_in_progress
        newStatus = 'audit_in_progress';
        break;
      case 'enrolled':
        // If moving to enrolled, set to enrolled
        newStatus = 'enrolled';
        break;
      default:
        // Fallback to first status in the column
        newStatus = targetColumn.statuses[0];
        break;
    }

    if (!newStatus) return;

    // Only update if the status actually changed
    if (currentLead.status !== newStatus) {
      console.log(`🔄 Moving lead ${currentLead.fullName} from ${currentLead.status} to ${newStatus}`);
      onLeadStatusChange?.(leadId, newStatus);
    } else {
      console.log(`⏸️ No status change needed - lead ${currentLead.fullName} already has status ${currentLead.status}`);
    }
  };

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


  // Always show content (removed loading state for debugging)

  return (
    <>
      {/* Header with Create Lead Button */}
      <div className="flex justify-between items-center p-6 pb-0">
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Leads Management</h2>
        <Button onClick={handleCreateLead} className="bg-blue-600 hover:bg-blue-700">
          <Users className="h-4 w-4 mr-2" />
          Create Lead
        </Button>
      </div>

      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragStart={handleDragStart}
        onDragOver={handleDragOver}
        onDragEnd={handleDragEnd}
      >
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 p-6 pt-4">
          {statusColumns.map((column) => (
              <DroppableColumn
                key={column.id}
                column={column}
                leads={leads}
                draggedLead={draggedLead}
                dragOverColumn={dragOverColumn}
                onLeadClick={handleLeadClick}
                onPhoneClick={handlePhoneClick}
                getStatusBadgeVariant={getStatusBadgeVariant}
                getStatusLabel={getStatusLabel}
                formatDate={formatDate}
              />
          ))}
        </div>

        <DragOverlay>
          {draggedLead ? (
            <div className="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-800 shadow-lg opacity-90">
              <div className="flex items-start justify-between mb-2">
                <h4 className="font-medium text-sm leading-tight">{draggedLead.fullName}</h4>
                <ArrowRight className="h-3 w-3 text-gray-400" />
              </div>
              
              <div className="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                {draggedLead.firm && (
                  <div className="flex items-center gap-1">
                    <Building className="h-3 w-3" />
                    <span className="truncate">{draggedLead.firm}</span>
                  </div>
                )}
                
                {draggedLead.phone && (
                  <div className="flex items-center gap-1">
                    <Phone className="h-3 w-3" />
                    <span className="truncate font-mono">{draggedLead.phone}</span>
                  </div>
                )}
              </div>
              
              <div className="flex items-center justify-between mt-2">
                <Badge variant={getStatusBadgeVariant(draggedLead.status)} className="text-xs">
                  {getStatusLabel(draggedLead.status)}
                </Badge>
                <span className="text-xs text-gray-500">
                  {draggedLead.createdAt ? formatDate(draggedLead.createdAt) : 'Unknown'}
                </span>
              </div>
            </div>
          ) : null}
        </DragOverlay>
      </DndContext>

      <LeadDetailsModal
        lead={selectedLead}
        isOpen={isModalOpen}
        onClose={handleModalClose}
        onStatusChange={handleStatusChange}
      />

      <CallModal
        lead={callLead}
        isOpen={isCallModalOpen}
        onClose={handleCallModalClose}
        onHangup={handleHangup}
      />

      <LeadFormModal
        isOpen={isCreateModalOpen}
        onClose={handleCreateModalClose}
        onSave={handleCreateSave}
        lead={null}
      />

      <LeadFormModal
        isOpen={isEditModalOpen}
        onClose={handleEditModalClose}
        onSave={handleEditSave}
        lead={editingLead}
      />
    </>
  );
}
