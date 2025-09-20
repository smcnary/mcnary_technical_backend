'use client';

import React, { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
  FileText, 
  Clock, 
  CheckCircle, 
  AlertCircle,
  Users,
  TrendingUp
} from 'lucide-react';
import DocumentList from './DocumentList';
import DocumentForm from './DocumentForm';
import { format } from 'date-fns';

interface DocumentStats {
  total: number;
  draft: number;
  readyForSignature: number;
  signed: number;
  expired: number;
}

interface DocumentDashboardProps {
  clientId?: string;
}

export default function DocumentDashboard({ clientId }: DocumentDashboardProps) {
  const [activeTab, setActiveTab] = useState('overview');
  const [selectedDocument, setSelectedDocument] = useState<any>(null);
  const [showForm, setShowForm] = useState(false);
  const [stats, setStats] = useState<DocumentStats>({
    total: 0,
    draft: 0,
    readyForSignature: 0,
    signed: 0,
    expired: 0,
  });

  const handleDocumentSelect = (document: any) => {
    setSelectedDocument(document);
    setActiveTab('details');
  };

  const handleCreateDocument = () => {
    setSelectedDocument(null);
    setShowForm(true);
    setActiveTab('form');
  };

  const handleEditDocument = (document: any) => {
    setSelectedDocument(document);
    setShowForm(true);
    setActiveTab('form');
  };

  const handleFormSave = (document: any) => {
    setShowForm(false);
    setSelectedDocument(document);
    setActiveTab('details');
    // Refresh stats and list
  };

  const handleFormCancel = () => {
    setShowForm(false);
    setSelectedDocument(null);
    setActiveTab('overview');
  };

  const StatCard = ({ 
    title, 
    value, 
    icon: Icon, 
    color = 'text-gray-600',
    bgColor = 'bg-gray-50'
  }: {
    title: string;
    value: number;
    icon: React.ElementType;
    color?: string;
    bgColor?: string;
  }) => (
    <Card>
      <CardContent className="p-6">
        <div className="flex items-center">
          <div className={`p-2 rounded-lg ${bgColor}`}>
            <Icon className={`w-6 h-6 ${color}`} />
          </div>
          <div className="ml-4">
            <p className="text-sm font-medium text-gray-600">{title}</p>
            <p className="text-2xl font-bold">{value}</p>
          </div>
        </div>
      </CardContent>
    </Card>
  );

  const OverviewTab = () => (
    <div className="space-y-6">
      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <StatCard
          title="Total Documents"
          value={stats.total}
          icon={FileText}
          color="text-blue-600"
          bgColor="bg-blue-50"
        />
        <StatCard
          title="Draft"
          value={stats.draft}
          icon={FileText}
          color="text-gray-600"
          bgColor="bg-gray-50"
        />
        <StatCard
          title="Pending Signature"
          value={stats.readyForSignature}
          icon={Clock}
          color="text-yellow-600"
          bgColor="bg-yellow-50"
        />
        <StatCard
          title="Signed"
          value={stats.signed}
          icon={CheckCircle}
          color="text-green-600"
          bgColor="bg-green-50"
        />
        <StatCard
          title="Expired"
          value={stats.expired}
          icon={AlertCircle}
          color="text-red-600"
          bgColor="bg-red-50"
        />
      </div>

      {/* Quick Actions */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <TrendingUp className="w-5 h-5" />
            Quick Actions
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Button
              onClick={handleCreateDocument}
              className="h-20 flex flex-col items-center justify-center"
            >
              <FileText className="w-6 h-6 mb-2" />
              Create New Document
            </Button>
            <Button
              variant="outline"
              onClick={() => setActiveTab('pending')}
              className="h-20 flex flex-col items-center justify-center"
            >
              <Clock className="w-6 h-6 mb-2" />
              Review Pending Signatures
            </Button>
            <Button
              variant="outline"
              onClick={() => setActiveTab('templates')}
              className="h-20 flex flex-col items-center justify-center"
            >
              <Users className="w-6 h-6 mb-2" />
              Manage Templates
            </Button>
          </div>
        </CardContent>
      </Card>

      {/* Recent Documents */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Documents</CardTitle>
        </CardHeader>
        <CardContent>
          <DocumentList
            clientId={clientId}
            showClientColumn={!clientId}
            onDocumentSelect={handleDocumentSelect}
          />
        </CardContent>
      </Card>
    </div>
  );

  const PendingSignaturesTab = () => (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-lg font-semibold">Documents Pending Signature</h3>
          <p className="text-sm text-muted-foreground">
            Documents that are waiting for client signatures
          </p>
        </div>
      </div>
      
      <DocumentList
        clientId={clientId}
        showClientColumn={!clientId}
        onDocumentSelect={handleDocumentSelect}
        // You could add a filter here to only show ready_for_signature status
      />
    </div>
  );

  const DocumentDetailsTab = () => {
    if (!selectedDocument) {
      return (
        <Card>
          <CardContent className="p-12 text-center">
            <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <h3 className="text-lg font-semibold mb-2">No document selected</h3>
            <p className="text-muted-foreground">
              Select a document from the list to view details
            </p>
          </CardContent>
        </Card>
      );
    }

    return (
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <div>
                <CardTitle>{selectedDocument.title}</CardTitle>
                <p className="text-sm text-muted-foreground">
                  Created {format(new Date(selectedDocument.createdAt), 'MMM d, yyyy')}
                </p>
              </div>
              <div className="flex gap-2">
                <Button
                  variant="outline"
                  onClick={() => handleEditDocument(selectedDocument)}
                >
                  Edit
                </Button>
                <Button variant="outline">
                  Download
                </Button>
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-4">
            {selectedDocument.description && (
              <div>
                <h4 className="font-medium mb-2">Description</h4>
                <p className="text-muted-foreground">{selectedDocument.description}</p>
              </div>
            )}
            
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <h4 className="font-medium mb-1">Status</h4>
                <p className="text-sm text-muted-foreground capitalize">
                  {selectedDocument.status.replace('_', ' ')}
                </p>
              </div>
              <div>
                <h4 className="font-medium mb-1">Type</h4>
                <p className="text-sm text-muted-foreground capitalize">
                  {selectedDocument.type}
                </p>
              </div>
              <div>
                <h4 className="font-medium mb-1">Client</h4>
                <p className="text-sm text-muted-foreground">
                  {selectedDocument.client?.name}
                </p>
              </div>
              <div>
                <h4 className="font-medium mb-1">Requires Signature</h4>
                <p className="text-sm text-muted-foreground">
                  {selectedDocument.requiresSignature ? 'Yes' : 'No'}
                </p>
              </div>
            </div>

            {selectedDocument.signatures && selectedDocument.signatures.length > 0 && (
              <div>
                <h4 className="font-medium mb-2">Signatures</h4>
                <div className="space-y-2">
                  {selectedDocument.signatures.map((signature: any, index: number) => (
                    <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                      <div>
                        <p className="font-medium">
                          {signature.signedBy.firstName && signature.signedBy.lastName
                            ? `${signature.signedBy.firstName} ${signature.signedBy.lastName}`
                            : signature.signedBy.email}
                        </p>
                        <p className="text-sm text-muted-foreground">
                          Status: {signature.status}
                        </p>
                      </div>
                      {signature.signedAt && (
                        <p className="text-sm text-muted-foreground">
                          {format(new Date(signature.signedAt), 'MMM d, yyyy')}
                        </p>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    );
  };

  const TemplatesTab = () => (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-lg font-semibold">Document Templates</h3>
          <p className="text-sm text-muted-foreground">
            Manage reusable document templates
          </p>
        </div>
        <Button>
          Create Template
        </Button>
      </div>
      
      <Card>
        <CardContent className="p-12 text-center">
          <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
          <h3 className="text-lg font-semibold mb-2">Templates coming soon</h3>
          <p className="text-muted-foreground">
            Template management will be available in a future update
          </p>
        </CardContent>
      </Card>
    </div>
  );

  return (
    <div className="container mx-auto py-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Document Management</h1>
          <p className="text-muted-foreground">
            Create, manage, and track document signatures
          </p>
        </div>
      </div>

      <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-6">
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="pending">Pending Signatures</TabsTrigger>
          <TabsTrigger value="details">Document Details</TabsTrigger>
          <TabsTrigger value="templates">Templates</TabsTrigger>
        </TabsList>

        {showForm ? (
          <DocumentForm
            documentId={selectedDocument?.id}
            clientId={clientId}
            onSave={handleFormSave}
            onCancel={handleFormCancel}
          />
        ) : (
          <>
            <TabsContent value="overview">
              <OverviewTab />
            </TabsContent>
            
            <TabsContent value="pending">
              <PendingSignaturesTab />
            </TabsContent>
            
            <TabsContent value="details">
              <DocumentDetailsTab />
            </TabsContent>
            
            <TabsContent value="templates">
              <TemplatesTab />
            </TabsContent>
          </>
        )}
      </Tabs>
    </div>
  );
}
