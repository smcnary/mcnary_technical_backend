import React, { useState, useRef, useEffect } from 'react';
import { Card, CardContent } from '../ui/card';
import { Button } from '../ui/button';
import { Upload, X, CheckCircle2, AlertCircle } from 'lucide-react';
import LeadsKanbanBoard from '../leads/LeadsKanbanBoard';
import { useData } from '../../hooks/useData';
import { useAuth } from '../../hooks/useAuth';


export default function SeoClientsTab() {
  const [uploadedFile, setUploadedFile] = useState<File | null>(null);
  const [uploadStatus, setUploadStatus] = useState<'idle' | 'uploading' | 'success' | 'error'>('idle');
  const [uploadMessage, setUploadMessage] = useState('');
  const [showUpload, setShowUpload] = useState(false);
  const [isClient, setIsClient] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);
  
  // Get the importLeads function from useData hook
  const { importLeads, leads: realLeads, getLeads } = useData();
  const { isAuthenticated, isAdmin, isSalesConsultant } = useAuth();

  // Prevent hydration mismatch
  useEffect(() => {
    setIsClient(true);
  }, []);

  // Load real leads from database
  useEffect(() => {
    if (isClient) {
      console.log('Loading leads...', { isAuthenticated, isAdmin: isAdmin(), isSalesConsultant: isSalesConsultant() });
      // Force load leads immediately
      getLeads().then((leads) => {
        console.log('Leads loaded successfully:', leads.length, 'leads');
      }).catch((error) => {
        console.error('Error loading leads:', error);
      });
    }
  }, [isClient, getLeads]);

  // Use real leads count from database
  const realLeadsCount = realLeads.length;
  
  // TEMPORARY: Force some test leads for debugging
  const testLeads = [
    {
      id: 'test-001',
      fullName: 'Test Law Firm',
      email: 'test@lawfirm.com',
      phone: '+1 918-123-4567',
      firm: 'Test Law Firm',
      website: 'http://testlawfirm.com/',
      practiceAreas: ['attorney', 'lawyer', 'legal services'],
      city: 'Tulsa',
      state: 'OK',
      status: 'new_lead',
      statusLabel: 'New Lead',
      source: 'Test Data',
      createdAt: '2025-09-23T12:40:11Z',
      updatedAt: '2025-09-23T12:40:11Z'
    }
  ];
  
  // Use test leads if real leads are empty
  const displayLeads = realLeads.length > 0 ? realLeads : testLeads;
  
  // Debug: Log leads data
  useEffect(() => {
    console.log('Real leads data:', realLeads);
    console.log('Real leads count:', realLeadsCount);
  }, [realLeads, realLeadsCount]);

  // File upload handler
  const handleFileUpload = async (file: File) => {
    if (!file.name.toLowerCase().endsWith('.csv')) {
      setUploadStatus('error');
      setUploadMessage('Please upload a CSV file');
      return;
    }

    setUploadStatus('uploading');
    setUploadMessage('Uploading and processing CSV...');

    try {
      const text = await file.text();
      const result = await importLeads(text, {});

      if (result.imported_count > 0) {
        setUploadStatus('success');
        setUploadMessage(`Successfully imported ${result.imported_count} leads`);
        getLeads(); // Refresh the leads data
        setTimeout(() => {
          setShowUpload(false);
          setUploadedFile(null);
          setUploadStatus('idle');
          setUploadMessage('');
        }, 3000);
      } else {
        throw new Error(result.message || 'Failed to import leads');
      }
    } catch (error) {
      setUploadStatus('error');
      setUploadMessage(error instanceof Error ? error.message : 'Failed to upload file');
    }
  };

  // Drag and drop handlers
  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    const files = Array.from(e.dataTransfer.files);
    if (files.length > 0) {
      const file = files[0];
      setUploadedFile(file);
      handleFileUpload(file);
    }
  };

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (files && files.length > 0) {
      const file = files[0];
      setUploadedFile(file);
      handleFileUpload(file);
    }
  };

  // Always show content (removed loading state for debugging)

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <h2 className="text-xl font-semibold">SEO Client Leads</h2>
        </div>
        <div className="flex items-center gap-3">
          <div className="text-sm text-muted-foreground">
            {displayLeads.length} leads from database
          </div>
          {/* Admin-only upload button */}
          {(isAdmin() || isSalesConsultant()) && (
            <Button
              onClick={() => setShowUpload(!showUpload)}
              variant="outline"
              size="sm"
            >
              <Upload className="h-4 w-4 mr-2" />
              Upload CSV
            </Button>
          )}
        </div>
      </div>

      {/* Admin-only Upload Section */}
      {(isAdmin() || isSalesConsultant()) && showUpload && (
        <Card className="border-dashed border-2 border-gray-300 dark:border-gray-600">
          <CardContent className="p-6">
            <div className="text-center">
              <Upload className="mx-auto h-12 w-12 text-gray-400" />
              <h3 className="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Upload CSV File</h3>
              <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Upload a CSV file to import leads into the system. New leads will appear in the Kanban board below.
              </p>
              <div className="mt-6">
                <div
                  className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors"
                  onDragOver={handleDragOver}
                  onDrop={handleDrop}
                >
                  <Upload className="h-8 w-8 mx-auto mb-2 text-gray-400" />
                  <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    {uploadedFile ? uploadedFile.name : 'Drop your CSV file here or click to browse'}
                  </p>
                  <input
                    ref={fileInputRef}
                    type="file"
                    accept=".csv"
                    onChange={handleFileInputChange}
                    className="hidden"
                  />
                  <Button
                    variant="outline"
                    onClick={() => fileInputRef.current?.click()}
                    disabled={uploadStatus === 'uploading'}
                  >
                    Choose File
                  </Button>
                </div>

                {/* Upload Status */}
                {uploadStatus !== 'idle' && (
                  <div className={`mt-4 flex items-center gap-2 p-3 rounded-lg ${
                    uploadStatus === 'success' ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300' :
                    uploadStatus === 'error' ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300' :
                    'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                  }`}>
                    {uploadStatus === 'success' && <CheckCircle2 className="h-4 w-4" />}
                    {uploadStatus === 'error' && <AlertCircle className="h-4 w-4" />}
                    {uploadStatus === 'uploading' && <div className="h-4 w-4 border-2 border-current border-t-transparent rounded-full animate-spin" />}
                    <span className="text-sm">{uploadMessage}</span>
                  </div>
                )}

                {/* Action Buttons */}
                <div className="mt-4 flex items-center justify-center gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => {
                      setShowUpload(false);
                      setUploadedFile(null);
                      setUploadStatus('idle');
                      setUploadMessage('');
                    }}
                  >
                    <X className="h-4 w-4 mr-1" />
                    Cancel
                  </Button>
                  {uploadStatus === 'success' && (
                    <Button
                      size="sm"
                      onClick={() => {
                        setShowUpload(false);
                        setUploadedFile(null);
                        setUploadStatus('idle');
                        setUploadMessage('');
                      }}
                    >
                      Done
                    </Button>
                  )}
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Kanban Board */}
      <LeadsKanbanBoard 
        leads={displayLeads} 
        onLeadClick={(lead) => console.log('Lead clicked:', lead)}
      />
    </div>
  );
}