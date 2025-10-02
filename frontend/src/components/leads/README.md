# Lead Management Components

## Overview
This directory contains components for managing leads in the SEO Clients CRM system.

## Components

### LeadFormModal
A modal component for creating and editing leads with the following features:
- Form validation for required fields (name, email)
- Support for all lead fields (contact info, business info, practice areas, notes)
- Dynamic practice area management (add/remove)
- URL validation for website field
- Create and update modes

### LeadsKanbanBoard
A kanban board component for visualizing and managing leads with the following features:
- Drag and drop functionality to change lead status
- Create new leads via "Create Lead" button
- Edit existing leads via lead details modal
- Status-based column organization
- Real-time updates

### LeadDetailsModal
A detailed view modal for individual leads with the following features:
- Comprehensive lead information display
- Edit lead functionality via integrated form
- Status change capabilities
- Notes management
- Scheduling features
- Tech stack analysis

## Usage

### Creating a Lead
1. Click the "Create Lead" button in the kanban board header
2. Fill out the required fields (name, email)
3. Optionally add additional information
4. Click "Create Lead" to save

### Editing a Lead
1. Click on a lead card in the kanban board to open details
2. Click the "Edit Lead" button in the details modal
3. Modify the desired fields
4. Click "Update Lead" to save changes

### Updating Lead Status
1. Drag and drop leads between columns in the kanban board
2. Or use the status buttons in the lead details modal

## API Integration
The components integrate with the following API endpoints:
- `POST /api/v1/leads` - Create new lead
- `PATCH /api/v1/leads/{id}` - Update existing lead
- `GET /api/v1/leads/{id}` - Get lead details

## Props

### LeadFormModal Props
- `isOpen: boolean` - Controls modal visibility
- `onClose: () => void` - Called when modal is closed
- `onSave: (lead: Lead) => void` - Called when lead is saved
- `lead?: Lead | null` - Lead data for editing (null for create mode)

### LeadsKanbanBoard Props
- `leads: Lead[]` - Array of leads to display
- `onLeadClick?: (lead: Lead) => void` - Called when lead is clicked
- `onLeadStatusChange?: (leadId: string, newStatus: string) => void` - Called when status changes
- `onLeadCreate?: (lead: Lead) => void` - Called when new lead is created
- `onLeadUpdate?: (lead: Lead) => void` - Called when lead is updated

### LeadDetailsModal Props
- `lead: Lead | null` - Lead data to display
- `isOpen: boolean` - Controls modal visibility
- `onClose: () => void` - Called when modal is closed
- `onStatusChange?: (leadId: string, newStatus: string) => void` - Called when status changes
- `onLeadUpdate?: (lead: Lead) => void` - Called when lead is updated
