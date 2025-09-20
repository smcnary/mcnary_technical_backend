'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  Plus, 
  Search, 
  Filter, 
  FileText, 
  Clock, 
  CheckCircle, 
  AlertCircle,
  Archive,
  Eye,
  Send,
  Download
} from 'lucide-react';
import { format } from 'date-fns';
import Link from 'next/link';

interface Document {
  id: string;
  title: string;
  description?: string;
  status: 'draft' | 'ready_for_signature' | 'signed' | 'archived' | 'cancelled';
  type: 'contract' | 'agreement' | 'proposal' | 'invoice' | 'report' | 'other';
  requiresSignature: boolean;
  createdAt: string;
  updatedAt: string;
  sentForSignatureAt?: string;
  signedAt?: string;
  expiresAt?: string;
  client: {
    id: string;
    name: string;
  };
  createdBy: {
    id: string;
    email: string;
    firstName?: string;
    lastName?: string;
  };
  signatures?: DocumentSignature[];
}

interface DocumentSignature {
  id: string;
  status: 'pending' | 'signed' | 'rejected' | 'cancelled';
  signedAt?: string;
  signedBy: {
    id: string;
    email: string;
    firstName?: string;
    lastName?: string;
  };
}

interface DocumentListProps {
  clientId?: string;
  showClientColumn?: boolean;
  onDocumentSelect?: (document: Document) => void;
}

const statusColors = {
  draft: 'bg-gray-100 text-gray-800',
  ready_for_signature: 'bg-blue-100 text-blue-800',
  signed: 'bg-green-100 text-green-800',
  archived: 'bg-gray-100 text-gray-600',
  cancelled: 'bg-red-100 text-red-800',
};

const statusIcons = {
  draft: FileText,
  ready_for_signature: Clock,
  signed: CheckCircle,
  archived: Archive,
  cancelled: AlertCircle,
};

const typeLabels = {
  contract: 'Contract',
  agreement: 'Agreement',
  proposal: 'Proposal',
  invoice: 'Invoice',
  report: 'Report',
  other: 'Other',
};

export default function DocumentList({ 
  clientId, 
  showClientColumn = true, 
  onDocumentSelect 
}: DocumentListProps) {
  const [documents, setDocuments] = useState<Document[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('');
  const [typeFilter, setTypeFilter] = useState<string>('');

  useEffect(() => {
    fetchDocuments();
  }, [clientId, statusFilter, typeFilter, searchTerm]);

  const fetchDocuments = async () => {
    try {
      setLoading(true);
      const params = new URLSearchParams();
      
      if (clientId) params.append('client_id', clientId);
      if (statusFilter) params.append('status', statusFilter);
      if (typeFilter) params.append('type', typeFilter);
      if (searchTerm) params.append('search', searchTerm);

      const response = await fetch(`/api/v1/documents?${params}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      });

      if (!response.ok) {
        throw new Error('Failed to fetch documents');
      }

      const data = await response.json();
      setDocuments(data.documents || []);
    } catch (error) {
      console.error('Error fetching documents:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSendForSignature = async (documentId: string) => {
    try {
      const response = await fetch(`/api/v1/documents/${documentId}/send-for-signature`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error('Failed to send document for signature');
      }

      await fetchDocuments(); // Refresh the list
    } catch (error) {
      console.error('Error sending document for signature:', error);
      alert('Failed to send document for signature');
    }
  };

  const getStatusIcon = (status: string) => {
    const IconComponent = statusIcons[status as keyof typeof statusIcons];
    return IconComponent ? <IconComponent className="w-4 h-4" /> : null;
  };

  const getSignatureCount = (document: Document) => {
    if (!document.signatures) return 0;
    return document.signatures.filter(sig => sig.status === 'signed').length;
  };

  const getPendingSignatureCount = (document: Document) => {
    if (!document.signatures) return 0;
    return document.signatures.filter(sig => sig.status === 'pending').length;
  };

  const isExpired = (expiresAt?: string) => {
    if (!expiresAt) return false;
    return new Date(expiresAt) < new Date();
  };

  if (loading) {
    return (
      <div className="space-y-4">
        {[...Array(3)].map((_, i) => (
          <Card key={i} className="animate-pulse">
            <CardContent className="p-6">
              <div className="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
              <div className="h-3 bg-gray-200 rounded w-1/2"></div>
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-2xl font-bold tracking-tight">Documents</h2>
          <p className="text-muted-foreground">
            Manage documents and track signatures
          </p>
        </div>
        <Link href="/documents/new">
          <Button>
            <Plus className="w-4 h-4 mr-2" />
            New Document
          </Button>
        </Link>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="p-6">
          <div className="flex gap-4 items-center">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
              <Input
                placeholder="Search documents..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10"
              />
            </div>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-48">
                <SelectValue placeholder="Filter by status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All Statuses</SelectItem>
                <SelectItem value="draft">Draft</SelectItem>
                <SelectItem value="ready_for_signature">Ready for Signature</SelectItem>
                <SelectItem value="signed">Signed</SelectItem>
                <SelectItem value="archived">Archived</SelectItem>
                <SelectItem value="cancelled">Cancelled</SelectItem>
              </SelectContent>
            </Select>
            <Select value={typeFilter} onValueChange={setTypeFilter}>
              <SelectTrigger className="w-48">
                <SelectValue placeholder="Filter by type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All Types</SelectItem>
                <SelectItem value="contract">Contract</SelectItem>
                <SelectItem value="agreement">Agreement</SelectItem>
                <SelectItem value="proposal">Proposal</SelectItem>
                <SelectItem value="invoice">Invoice</SelectItem>
                <SelectItem value="report">Report</SelectItem>
                <SelectItem value="other">Other</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Documents List */}
      <div className="space-y-4">
        {documents.length === 0 ? (
          <Card>
            <CardContent className="p-12 text-center">
              <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
              <h3 className="text-lg font-semibold mb-2">No documents found</h3>
              <p className="text-muted-foreground mb-4">
                {searchTerm || statusFilter || typeFilter
                  ? 'Try adjusting your search criteria'
                  : 'Get started by creating your first document'}
              </p>
              {!searchTerm && !statusFilter && !typeFilter && (
                <Link href="/documents/new">
                  <Button>
                    <Plus className="w-4 h-4 mr-2" />
                    Create Document
                  </Button>
                </Link>
              )}
            </CardContent>
          </Card>
        ) : (
          documents.map((document) => (
            <Card key={document.id} className="hover:shadow-md transition-shadow">
              <CardContent className="p-6">
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <div className="flex items-center gap-3 mb-2">
                      <h3 className="text-lg font-semibold">{document.title}</h3>
                      <Badge className={statusColors[document.status]}>
                        {getStatusIcon(document.status)}
                        <span className="ml-1 capitalize">
                          {document.status.replace('_', ' ')}
                        </span>
                      </Badge>
                      <Badge variant="outline">
                        {typeLabels[document.type as keyof typeof typeLabels]}
                      </Badge>
                      {document.requiresSignature && (
                        <Badge variant="secondary">Requires Signature</Badge>
                      )}
                      {isExpired(document.expiresAt) && (
                        <Badge variant="destructive">Expired</Badge>
                      )}
                    </div>
                    
                    {document.description && (
                      <p className="text-muted-foreground mb-3">{document.description}</p>
                    )}
                    
                    <div className="flex items-center gap-6 text-sm text-muted-foreground">
                      {showClientColumn && (
                        <span>Client: {document.client.name}</span>
                      )}
                      <span>
                        Created by {document.createdBy.firstName && document.createdBy.lastName
                          ? `${document.createdBy.firstName} ${document.createdBy.lastName}`
                          : document.createdBy.email}
                      </span>
                      <span>
                        Created {format(new Date(document.createdAt), 'MMM d, yyyy')}
                      </span>
                      {document.requiresSignature && (
                        <span>
                          {getSignatureCount(document)} signed, {getPendingSignatureCount(document)} pending
                        </span>
                      )}
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-2 ml-4">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => onDocumentSelect?.(document)}
                    >
                      <Eye className="w-4 h-4" />
                    </Button>
                    
                    {document.status === 'draft' && document.requiresSignature && (
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => handleSendForSignature(document.id)}
                      >
                        <Send className="w-4 h-4" />
                      </Button>
                    )}
                    
                    <Button variant="outline" size="sm">
                      <Download className="w-4 h-4" />
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))
        )}
      </div>
    </div>
  );
}
