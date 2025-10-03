import React, { useState, useRef, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Users, MessageSquare, FileText, Search, CheckCircle, Calendar, Phone, Clock, ChevronRight, Upload, X, CheckCircle2, AlertCircle, Grid3X3, List } from 'lucide-react';
import OpenPhoneIntegrationComponent from '../openphone/OpenPhoneIntegration';
import LeadsKanbanBoard from '../leads/LeadsKanbanBoard';
import { useData } from '../../hooks/useData';
import { useAuth } from '../../hooks/useAuth';

// Types for the data structures
interface Lead {
  id: number;
  name: string;
  email: string;
  company: string;
  status: string;
  createdAt: string;
  phone?: string;
}

interface Interview {
  id: number;
  name: string;
  company: string;
  scheduledDate: string;
  time: string;
  type: string;
}

interface Application {
  id: number;
  name: string;
  company: string;
  submittedDate: string;
  status: string;
  package: string;
}

interface Audit {
  id: number;
  name: string;
  company: string;
  startedDate: string;
  status: string;
  progress: number;
}

interface Enrolled {
  id: number;
  name: string;
  company: string;
  enrolledDate: string;
  package: string;
  status: string;
}

// Mock data for each category
const mockData = {
  leads: [
    { id: 1, name: 'John Smith', email: 'john@example.com', company: 'Smith & Associates', status: 'new', createdAt: '2024-01-15' },
    { id: 2, name: 'Sarah Johnson', email: 'sarah@techcorp.com', company: 'TechCorp Inc', status: 'contacted', createdAt: '2024-01-14' },
    { id: 3, name: 'Mike Davis', email: 'mike@lawfirm.com', company: 'Davis Law Firm', status: 'qualified', createdAt: '2024-01-13' },
  ],
  interviews: [
    { id: 1, name: 'John Smith', company: 'Smith & Associates', scheduledDate: '2024-01-20', time: '2:00 PM', type: 'Discovery Call' },
    { id: 2, name: 'Sarah Johnson', company: 'TechCorp Inc', scheduledDate: '2024-01-22', time: '10:00 AM', type: 'Strategy Session' },
  ],
  applications: [
    { id: 1, name: 'Mike Davis', company: 'Davis Law Firm', submittedDate: '2024-01-18', status: 'under_review', package: 'Premium SEO' },
    { id: 2, name: 'Lisa Brown', company: 'Brown Consulting', submittedDate: '2024-01-17', status: 'approved', package: 'Standard SEO' },
  ],
  audits: [
    { id: 1, name: 'Lisa Brown', company: 'Brown Consulting', startedDate: '2024-01-16', status: 'in_progress', progress: 65 },
    { id: 2, name: 'Tom Wilson', company: 'Wilson Enterprises', startedDate: '2024-01-12', status: 'completed', progress: 100 },
  ],
  enrolled: [
    { id: 1, name: 'Tom Wilson', company: 'Wilson Enterprises', enrolledDate: '2024-01-10', package: 'Premium SEO', status: 'active' },
    { id: 2, name: 'Jennifer Lee', company: 'Lee Marketing', enrolledDate: '2024-01-08', package: 'Standard SEO', status: 'active' },
  ]
};

export default function SeoClientsTab() {
  const [activeTab, setActiveTab] = useState<string | null>(null);
  const [showUpload, setShowUpload] = useState(false);
  const [uploadedFile, setUploadedFile] = useState<File | null>(null);
  const [uploadStatus, setUploadStatus] = useState<'idle' | 'uploading' | 'success' | 'error'>('idle');
  const [uploadMessage, setUploadMessage] = useState('');
  const [leads] = useState<Lead[]>(mockData.leads);
  const [viewMode, setViewMode] = useState<'kanban' | 'tabs'>('kanban');
  const fileInputRef = useRef<HTMLInputElement>(null);
  
  // Get the importLeads function from useData hook
  const { importLeads, leads: realLeads, getLeads } = useData();
  const { isAuthenticated, isAdmin, isSalesConsultant, isClientAdmin } = useAuth();

  // Load real leads from database
  useEffect(() => {
    if (isAuthenticated && (isAdmin() || isSalesConsultant() || isClientAdmin())) {
      getLeads();
    }
  }, [isAuthenticated, isAdmin, isSalesConsultant, isClientAdmin, getLeads]);

  // Use real leads count from database
  const realLeadsCount = realLeads.length;
  
  const tabs = [
    { key: 'leads', label: 'Leads', icon: Users, color: 'text-blue-500', count: realLeadsCount },
    { key: 'interviews', label: 'Interviews', icon: MessageSquare, color: 'text-green-500', count: mockData.interviews.length },
    { key: 'applications', label: 'Applications', icon: FileText, color: 'text-orange-500', count: mockData.applications.length },
    { key: 'audits', label: 'Audits', icon: Search, color: 'text-purple-500', count: mockData.audits.length },
    { key: 'enrolled', label: 'Enrolled', icon: CheckCircle, color: 'text-emerald-500', count: mockData.enrolled.length },
    { key: 'openphone', label: 'OpenPhone', icon: Phone, color: 'text-indigo-500', count: 0 },
  ];

  // File upload handler
  const handleFileUpload = async (file: File) => {
    if (!file.name.toLowerCase().endsWith('.csv')) {
      setUploadStatus('error');
      setUploadMessage('Please upload a CSV file');
      return;
    }

    setUploadStatus('uploading');
    setUploadMessage('Processing CSV file...');

    try {
      const text = await file.text();
      
      // Use the actual importLeads function from useData hook
      const result = await importLeads(text, {
        // You can add options here if needed
      });
      
      if (result.imported_count > 0) {
        setUploadStatus('success');
        setUploadMessage(`Successfully imported ${result.imported_count} leads`);
        
        // Refresh the leads data
        getLeads();
        
        // Auto-close upload after success
        setTimeout(() => {
          setShowUpload(false);
          setUploadedFile(null);
          setUploadStatus('idle');
          setUploadMessage('');
        }, 2000);
      } else {
        throw new Error(result.message || 'Failed to import leads');
      }

    } catch (error) {
      setUploadStatus('error');
      setUploadMessage(error instanceof Error ? error.message : 'Failed to process CSV file');
    }
  };

  // Drag and drop handlers
  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      setUploadedFile(files[0]);
      handleFileUpload(files[0]);
    }
  };

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (files && files.length > 0) {
      setUploadedFile(files[0]);
      handleFileUpload(files[0]);
    }
  };

  const renderListContent = (tabKey: string) => {
    if (tabKey === 'openphone') {
      return (
        <div className="p-4">
          <OpenPhoneIntegrationComponent 
            clientId="mock-client-id" 
            clientName="Sample Client" 
          />
        </div>
      );
    }
    
    const data = tabKey === 'leads' ? leads : mockData[tabKey as keyof typeof mockData];
    
    if (!data || data.length === 0) {
      return (
        <div className="text-center py-8 text-muted-foreground text-sm">
          No {tabKey} yet
        </div>
      );
    }

    return (
      <div className="space-y-3 max-h-96 overflow-y-auto">
        {data.map((item: Lead | Interview | Application | Audit | Enrolled) => (
          <div key={item.id} className="p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
            <div className="flex items-center justify-between">
              <div className="flex-1">
                <h4 className="font-medium text-sm">{'fullName' in item ? item.fullName : item.name}</h4>
                <p className="text-xs text-muted-foreground">{'firm' in item ? item.firm : item.company}</p>
                {'email' in item && item.email && <p className="text-xs text-muted-foreground">{item.email}</p>}
                {'scheduledDate' in item && (
                  <div className="flex items-center gap-1 mt-1">
                    <Calendar className="h-3 w-3 text-muted-foreground" />
                    <span className="text-xs text-muted-foreground">{item.scheduledDate} at {item.time}</span>
                  </div>
                )}
                {'submittedDate' in item && (
                  <div className="flex items-center gap-1 mt-1">
                    <Clock className="h-3 w-3 text-muted-foreground" />
                    <span className="text-xs text-muted-foreground">Submitted {item.submittedDate}</span>
                  </div>
                )}
                {'enrolledDate' in item && (
                  <div className="flex items-center gap-1 mt-1">
                    <CheckCircle className="h-3 w-3 text-emerald-500" />
                    <span className="text-xs text-muted-foreground">Enrolled {item.enrolledDate}</span>
                  </div>
                )}
              </div>
              <div className="flex flex-col items-end gap-1">
                {'status' in item && item.status && (
                  <Badge variant={item.status === 'active' || item.status === 'qualified' ? 'default' : 'secondary'} className="text-xs">
                    {item.status.replace('_', ' ')}
                  </Badge>
                )}
                {'package' in item && item.package && (
                  <Badge variant="outline" className="text-xs">
                    {item.package}
                  </Badge>
                )}
                {'progress' in item && item.progress && (
                  <div className="text-xs text-muted-foreground">
                    {item.progress}% complete
                  </div>
                )}
                <ChevronRight className="h-4 w-4 text-muted-foreground" />
              </div>
            </div>
          </div>
        ))}
      </div>
    );
  };

  return (
    <div className="space-y-6">
      {/* View Mode Toggle */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <h2 className="text-xl font-semibold">SEO Client Leads</h2>
          <div className="flex border border-gray-300 dark:border-gray-600 rounded-md">
            <Button
              variant={viewMode === 'kanban' ? 'default' : 'ghost'}
              size="sm"
              onClick={() => setViewMode('kanban')}
              className="rounded-r-none border-r border-gray-300 dark:border-gray-600"
            >
              <Grid3X3 className="h-4 w-4" />
              Kanban
            </Button>
            <Button
              variant={viewMode === 'tabs' ? 'default' : 'ghost'}
              size="sm"
              onClick={() => setViewMode('tabs')}
              className="rounded-l-none"
            >
              <List className="h-4 w-4" />
              Tabs
            </Button>
          </div>
        </div>
        <div className="text-sm text-muted-foreground">
          {realLeadsCount} leads from database
        </div>
      </div>

      {/* Kanban Board View */}
      {viewMode === 'kanban' ? (
        <LeadsKanbanBoard 
          leads={realLeads} 
          onLeadClick={(lead) => console.log('Lead clicked:', lead)}
        />
      ) : (
        <>
          {/* Upload Section */}
          <Card className="border-dashed border-2 border-gray-300 dark:border-gray-600">
        <CardHeader>
          <CardTitle className="text-lg flex items-center gap-2">
            <Upload className="h-5 w-5" />
            Import Leads from CSV
          </CardTitle>
          <CardDescription>
            Upload a CSV file to import new leads. Required columns: name, email, company. Optional: phone, status.
          </CardDescription>
        </CardHeader>
        <CardContent>
          {!showUpload ? (
            <div className="flex items-center gap-4">
              <Button 
                onClick={() => setShowUpload(true)}
                className="flex items-center gap-2"
              >
                <Upload className="h-4 w-4" />
                Upload CSV
              </Button>
              <div className="text-sm text-muted-foreground">
                Drag and drop a CSV file or click to browse
              </div>
            </div>
          ) : (
            <div className="space-y-4">
              {/* Upload Area */}
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
                <div className={`flex items-center gap-2 p-3 rounded-lg ${
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
              <div className="flex items-center gap-2">
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
          )}
        </CardContent>
      </Card>

      {/* Tabs Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        {tabs.map((tab) => {
          const IconComponent = tab.icon;
          return (
            <Card 
              key={tab.key}
              className={`cursor-pointer transition-all duration-200 hover:shadow-md ${
                activeTab === tab.key ? 'ring-2 ring-blue-500 shadow-md' : ''
              }`}
              onClick={() => setActiveTab(activeTab === tab.key ? null : tab.key)}
            >
              <CardHeader className="pb-3">
                <CardTitle className="text-sm font-medium flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <IconComponent className={`h-4 w-4 ${tab.color}`} />
                    {tab.label}
                  </div>
                  <Badge variant="secondary" className="text-xs">
                    {tab.count}
                  </Badge>
                </CardTitle>
              </CardHeader>
              <CardContent>
                {activeTab === tab.key ? (
                  renderListContent(tab.key)
                ) : (
                  <div className="text-center py-8 text-muted-foreground text-sm">
                    Click to view {tab.label.toLowerCase()}
                  </div>
                )}
              </CardContent>
            </Card>
          );
        })}
      </div>
        </>
      )}
    </div>
  );
}
