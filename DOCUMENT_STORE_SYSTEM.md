# Document Store System

A comprehensive document management system that allows admins to create and modify documents, and clients to electronically sign them. This system includes version tracking, signature management, and template support.

## Features

### For Administrators
- ✅ Create and edit documents
- ✅ Upload document files
- ✅ Create reusable document templates
- ✅ Send documents for electronic signature
- ✅ Track signature status and progress
- ✅ Version control and change tracking
- ✅ Archive and manage document lifecycle
- ✅ View detailed analytics and reports

### For Clients
- ✅ View documents assigned to them
- ✅ Electronically sign documents
- ✅ Add comments to signatures
- ✅ Download signed documents
- ✅ Track signature history

## System Architecture

### Backend Entities

#### 1. Document Entity
The main document entity that stores document information and metadata.

**Key Fields:**
- `id`: UUID primary key
- `title`: Document title
- `description`: Optional document description
- `content`: Document content (HTML/text)
- `type`: Document type (contract, agreement, proposal, etc.)
- `status`: Document status (draft, ready_for_signature, signed, archived, cancelled)
- `client`: Associated client
- `createdBy`: User who created the document
- `file`: Optional uploaded file (MediaAsset)
- `template`: Optional source template
- `requiresSignature`: Boolean flag for signature requirement
- `signatureFields`: JSON array of signature field definitions
- `expiresAt`: Optional expiration date
- `sentForSignatureAt`: Timestamp when sent for signature
- `signedAt`: Timestamp when fully signed

#### 2. DocumentTemplate Entity
Reusable templates for creating documents with variable substitution.

**Key Fields:**
- `id`: UUID primary key
- `name`: Template name
- `description`: Template description
- `content`: Template content with variable placeholders
- `type`: Document type
- `variables`: JSON object defining template variables
- `signatureFields`: Default signature fields for documents created from template
- `isActive`: Whether template is available for use
- `requiresSignature`: Default signature requirement
- `usageCount`: Number of times template has been used

#### 3. DocumentSignature Entity
Tracks individual signatures on documents.

**Key Fields:**
- `id`: UUID primary key
- `document`: Associated document
- `signedBy`: User who signed
- `signatureData`: Base64 encoded signature data
- `signatureImage`: Signature image URL
- `status`: Signature status (pending, signed, rejected, cancelled)
- `signedAt`: Timestamp of signature
- `ipAddress`: IP address of signer
- `userAgent`: Browser user agent
- `comments`: Optional signature comments
- `isDigitalSignature`: Boolean flag for signature type

#### 4. DocumentVersion Entity
Tracks document versions and changes over time.

**Key Fields:**
- `id`: UUID primary key
- `document`: Associated document
- `versionNumber`: Sequential version number
- `title`: Version title
- `content`: Version content
- `changes`: JSON object describing what changed
- `createdBy`: User who created this version
- `isCurrent`: Whether this is the current version

### API Endpoints

#### Document Management
- `GET /api/v1/documents` - List documents (admin only)
- `GET /api/v1/documents/{id}` - Get document details
- `POST /api/v1/documents` - Create new document (admin only)
- `PUT /api/v1/documents/{id}` - Update document (admin only)
- `PATCH /api/v1/documents/{id}` - Partial update
- `DELETE /api/v1/documents/{id}` - Delete document (admin only)

#### Signature Management
- `POST /api/v1/documents/{id}/send-for-signature` - Send for signature (admin only)
- `POST /api/v1/documents/{id}/sign` - Sign document (client/admin)
- `GET /api/v1/documents/{id}/signature-status` - Get signature status

#### Template Management
- `GET /api/v1/document-templates` - List templates (admin only)
- `POST /api/v1/document-templates` - Create template (admin only)
- `POST /api/v1/documents/templates/{templateId}/create` - Create document from template

#### Client-Specific
- `GET /api/v1/documents/client/{clientId}` - Get documents for specific client
- `GET /api/v1/documents/ready-for-signature` - Get documents ready for signature

### Security & Access Control

#### Role-Based Access
- **ROLE_AGENCY_ADMIN**: Full access to all document operations
- **ROLE_CLIENT_ADMIN**: Access to client's documents and signature management
- **ROLE_CLIENT_USER**: Can view and sign documents assigned to their client

#### Document Access Rules
- Admins can access all documents
- Client admins can access their client's documents
- Client users can only access documents in "ready_for_signature" status for their client
- Document creators always have access to their documents

### Frontend Components

#### 1. DocumentDashboard
Main dashboard component with tabs for different views:
- Overview with statistics and quick actions
- Pending signatures management
- Document details view
- Template management

#### 2. DocumentList
Displays a list of documents with filtering and search capabilities:
- Search by title/description
- Filter by status and type
- Sort by creation date
- Quick actions (view, edit, send for signature)

#### 3. DocumentForm
Form for creating and editing documents:
- Basic document information
- Template selection and variable substitution
- Signature field configuration
- File upload support
- Save as draft or send for signature

#### 4. DocumentSignature
Electronic signature interface:
- Canvas-based signature drawing
- Signature preview
- Comments field
- Legal notice and confirmation
- IP address and user agent tracking

## Usage Examples

### Creating a Document from Template

```typescript
const documentData = {
  title: "Service Agreement - Client Name",
  clientId: "client-uuid",
  templateId: "template-uuid",
  templateVariables: {
    clientName: "Acme Corp",
    serviceType: "SEO Services",
    contractValue: "$5,000",
    startDate: "2024-01-01",
    endDate: "2024-12-31"
  },
  signatureFields: [
    {
      name: "Client Signature",
      type: "signature",
      required: true,
      signerEmail: "client@acme.com"
    }
  ]
};

const response = await fetch('/api/v1/documents', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(documentData)
});
```

### Signing a Document

```typescript
const signatureData = {
  signature_data: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  signature_image: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  comments: "I agree to the terms and conditions."
};

const response = await fetch(`/api/v1/documents/${documentId}/sign`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(signatureData)
});
```

### Template Variable Substitution

Templates support variable substitution using the format `{{variableName}}`:

```html
<div class="contract">
  <h1>Service Agreement</h1>
  <p>This agreement is between {{agencyName}} and {{clientName}}.</p>
  <p>Service: {{serviceType}}</p>
  <p>Contract Value: {{contractValue}}</p>
  <p>Start Date: {{startDate}}</p>
  <p>End Date: {{endDate}}</p>
</div>
```

## Database Schema

### Migration
The system includes a comprehensive database migration (`Version20250920231847.php`) that creates:

1. **documents** table - Main document storage
2. **document_templates** table - Reusable templates
3. **document_signatures** table - Signature tracking
4. **document_versions** table - Version history

### Key Relationships
- Documents belong to Clients and Users (createdBy)
- Documents can have multiple Signatures
- Documents can have multiple Versions
- Documents can be created from Templates
- Documents can reference MediaAssets (files)

## Installation & Setup

### Backend Setup
1. Run the database migration:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

2. The system automatically creates the necessary repository classes and services

### Frontend Setup
1. Import the document components into your Next.js application
2. Set up the API routes to proxy requests to the backend
3. Configure authentication tokens in localStorage

### Environment Variables
Ensure these environment variables are set:
- `JWT_SECRET_KEY` - For token validation
- `DATABASE_URL` - Database connection string

## Security Considerations

### Signature Security
- All signatures are timestamped with IP address and user agent
- Signature data is stored as base64-encoded images
- Legal notices are displayed before signing
- Documents can be set to expire

### Access Control
- Role-based access control enforced at API level
- Client isolation ensures users only see their documents
- Admin actions are logged and auditable

### Data Protection
- Documents support metadata for additional security flags
- Version tracking provides complete audit trail
- Soft deletes preserve data integrity

## Future Enhancements

### Planned Features
- [ ] Bulk document operations
- [ ] Advanced template editor with WYSIWYG
- [ ] Email notifications for signature requests
- [ ] Document approval workflows
- [ ] Integration with external e-signature services
- [ ] Advanced reporting and analytics
- [ ] Document comparison and diff viewing
- [ ] Automated document generation from data

### API Improvements
- [ ] GraphQL endpoint for complex queries
- [ ] WebSocket support for real-time updates
- [ ] Batch operations for multiple documents
- [ ] Advanced search with full-text indexing

## Support & Maintenance

### Monitoring
- Track document creation and signature completion rates
- Monitor signature processing times
- Alert on failed signature attempts

### Backup Strategy
- Regular database backups including signature data
- Document file backup to cloud storage
- Version history preservation

This document store system provides a robust foundation for document management and electronic signatures, with room for future enhancements and scalability.
