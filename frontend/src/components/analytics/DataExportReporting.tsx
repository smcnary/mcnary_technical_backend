'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  Download, 
  FileText, 
  FileSpreadsheet, 
  FilePdf, 
  Calendar,
  Filter,
  RefreshCw,
  Mail,
  Share2,
  CheckCircle,
  Loader2,
  AlertCircle,
  BarChart3,
  TrendingUp,
  Users,
  Target
} from 'lucide-react';

interface ReportConfig {
  type: 'leads' | 'campaigns' | 'analytics' | 'custom';
  format: 'csv' | 'pdf' | 'excel';
  dateRange: {
    start: string;
    end: string;
  };
  filters: {
    status?: string;
    source?: string;
    campaign?: string;
  };
  includeCharts: boolean;
  emailTo?: string;
}

interface ReportTemplate {
  id: string;
  name: string;
  description: string;
  type: string;
  icon: React.ElementType;
  color: string;
}

export default function DataExportReporting() {
  const { user } = useAuth();
  const { 
    leads, 
    campaigns, 
    getLeads, 
    getCampaigns,
    getLoadingState, 
    getErrorState 
  } = useData();

  const [reportConfig, setReportConfig] = useState<ReportConfig>({
    type: 'leads',
    format: 'csv',
    dateRange: {
      start: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      end: new Date().toISOString().split('T')[0]
    },
    filters: {},
    includeCharts: false,
    emailTo: ''
  });

  const [isGenerating, setIsGenerating] = useState(false);
  const [generatedReports, setGeneratedReports] = useState<any[]>([]);
  const [error, setError] = useState<string | null>(null);

  const reportTemplates: ReportTemplate[] = [
    {
      id: 'leads',
      name: 'Lead Report',
      description: 'Comprehensive lead analysis and conversion metrics',
      type: 'leads',
      icon: Users,
      color: 'bg-blue-100 text-blue-800'
    },
    {
      id: 'campaigns',
      name: 'Campaign Performance',
      description: 'Campaign effectiveness and ROI analysis',
      type: 'campaigns',
      icon: Target,
      color: 'bg-green-100 text-green-800'
    },
    {
      id: 'analytics',
      name: 'Analytics Summary',
      description: 'Website traffic and SEO performance metrics',
      type: 'analytics',
      icon: BarChart3,
      color: 'bg-purple-100 text-purple-800'
    },
    {
      id: 'custom',
      name: 'Custom Report',
      description: 'Build your own report with specific metrics',
      type: 'custom',
      icon: FileText,
      color: 'bg-orange-100 text-orange-800'
    }
  ];

  const formatOptions = [
    { value: 'csv', label: 'CSV', icon: FileSpreadsheet, description: 'Spreadsheet format' },
    { value: 'pdf', label: 'PDF', icon: FilePdf, description: 'Professional document' },
    { value: 'excel', label: 'Excel', icon: FileSpreadsheet, description: 'Excel workbook' }
  ];

  useEffect(() => {
    loadRecentReports();
  }, []);

  const loadRecentReports = async () => {
    // Mock recent reports - would come from backend
    setGeneratedReports([
      {
        id: '1',
        name: 'Monthly Lead Report - December 2024',
        type: 'leads',
        format: 'pdf',
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000),
        size: '2.3 MB',
        status: 'completed'
      },
      {
        id: '2',
        name: 'Campaign Performance Q4 2024',
        type: 'campaigns',
        format: 'excel',
        createdAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000),
        size: '1.8 MB',
        status: 'completed'
      },
      {
        id: '3',
        name: 'Analytics Summary - November 2024',
        type: 'analytics',
        format: 'pdf',
        createdAt: new Date(Date.now() - 14 * 24 * 60 * 60 * 1000),
        size: '3.1 MB',
        status: 'completed'
      }
    ]);
  };

  const generateReport = async () => {
    setIsGenerating(true);
    setError(null);

    try {
      // Simulate report generation
      await new Promise(resolve => setTimeout(resolve, 3000));

      const newReport = {
        id: Date.now().toString(),
        name: `${reportTemplates.find(t => t.type === reportConfig.type)?.name} - ${new Date().toLocaleDateString()}`,
        type: reportConfig.type,
        format: reportConfig.format,
        createdAt: new Date(),
        size: `${(Math.random() * 3 + 1).toFixed(1)} MB`,
        status: 'completed'
      };

      setGeneratedReports(prev => [newReport, ...prev]);

      // If email is specified, simulate sending
      if (reportConfig.emailTo) {
        console.log(`Report sent to ${reportConfig.emailTo}`);
      }

    } catch (err: any) {
      setError(err?.message || 'Failed to generate report');
    } finally {
      setIsGenerating(false);
    }
  };

  const downloadReport = (report: any) => {
    // Simulate download
    console.log(`Downloading report: ${report.name}`);
  };

  const shareReport = (report: any) => {
    // Simulate sharing
    console.log(`Sharing report: ${report.name}`);
  };

  const getFormatIcon = (format: string) => {
    const formatOption = formatOptions.find(f => f.value === format);
    return formatOption?.icon || FileText;
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'completed':
        return <Badge variant="default" className="bg-green-100 text-green-800">Completed</Badge>;
      case 'generating':
        return <Badge variant="secondary">Generating...</Badge>;
      case 'failed':
        return <Badge variant="destructive">Failed</Badge>;
      default:
        return <Badge variant="secondary">{status}</Badge>;
    }
  };

  const formatFileSize = (size: string) => size;
  const formatDate = (date: Date) => date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Data Export & Reporting</h2>
          <p className="text-gray-600">Generate and download comprehensive reports</p>
        </div>
        <Button onClick={loadRecentReports} variant="outline">
          <RefreshCw className="h-4 w-4 mr-2" />
          Refresh
        </Button>
      </div>

      {/* Report Templates */}
      <Card>
        <CardHeader>
          <CardTitle>Report Templates</CardTitle>
          <CardDescription>Choose a report template to get started</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {reportTemplates.map((template) => {
              const Icon = template.icon;
              return (
                <button
                  key={template.id}
                  onClick={() => setReportConfig(prev => ({ ...prev, type: template.type as any }))}
                  className={`p-4 rounded-lg border-2 text-left transition-all ${
                    reportConfig.type === template.type
                      ? 'border-blue-500 bg-blue-50'
                      : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  <div className="flex items-center gap-3 mb-2">
                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${template.color}`}>
                      <Icon className="w-5 h-5" />
                    </div>
                    <div>
                      <h3 className="font-semibold">{template.name}</h3>
                    </div>
                  </div>
                  <p className="text-sm text-gray-600">{template.description}</p>
                </button>
              );
            })}
          </div>
        </CardContent>
      </Card>

      {/* Report Configuration */}
      <Card>
        <CardHeader>
          <CardTitle>Report Configuration</CardTitle>
          <CardDescription>Customize your report settings</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {/* Format Selection */}
          <div className="space-y-2">
            <Label>Export Format</Label>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
              {formatOptions.map((format) => {
                const Icon = format.icon;
                return (
                  <button
                    key={format.value}
                    onClick={() => setReportConfig(prev => ({ ...prev, format: format.value as any }))}
                    className={`p-3 rounded-lg border-2 text-left transition-all ${
                      reportConfig.format === format.value
                        ? 'border-blue-500 bg-blue-50'
                        : 'border-gray-200 hover:border-gray-300'
                    }`}
                  >
                    <div className="flex items-center gap-2">
                      <Icon className="w-4 h-4" />
                      <span className="font-medium">{format.label}</span>
                    </div>
                    <p className="text-xs text-gray-600 mt-1">{format.description}</p>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Date Range */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="startDate">Start Date</Label>
              <Input
                id="startDate"
                type="date"
                value={reportConfig.dateRange.start}
                onChange={(e) => setReportConfig(prev => ({
                  ...prev,
                  dateRange: { ...prev.dateRange, start: e.target.value }
                }))}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="endDate">End Date</Label>
              <Input
                id="endDate"
                type="date"
                value={reportConfig.dateRange.end}
                onChange={(e) => setReportConfig(prev => ({
                  ...prev,
                  dateRange: { ...prev.dateRange, end: e.target.value }
                }))}
              />
            </div>
          </div>

          {/* Filters */}
          {reportConfig.type === 'leads' && (
            <div className="space-y-4">
              <Label>Filters</Label>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="status">Lead Status</Label>
                  <Select 
                    value={reportConfig.filters.status || ''} 
                    onValueChange={(value) => setReportConfig(prev => ({
                      ...prev,
                      filters: { ...prev.filters, status: value }
                    }))}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="All statuses" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="">All statuses</SelectItem>
                      <SelectItem value="pending">Pending</SelectItem>
                      <SelectItem value="contacted">Contacted</SelectItem>
                      <SelectItem value="qualified">Qualified</SelectItem>
                      <SelectItem value="disqualified">Disqualified</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="source">Source</Label>
                  <Select 
                    value={reportConfig.filters.source || ''} 
                    onValueChange={(value) => setReportConfig(prev => ({
                      ...prev,
                      filters: { ...prev.filters, source: value }
                    }))}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="All sources" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="">All sources</SelectItem>
                      <SelectItem value="website">Website</SelectItem>
                      <SelectItem value="google">Google</SelectItem>
                      <SelectItem value="referral">Referral</SelectItem>
                      <SelectItem value="social">Social Media</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
            </div>
          )}

          {/* Email Delivery */}
          <div className="space-y-2">
            <Label htmlFor="emailTo">Email Report To (Optional)</Label>
            <Input
              id="emailTo"
              type="email"
              placeholder="recipient@example.com"
              value={reportConfig.emailTo}
              onChange={(e) => setReportConfig(prev => ({ ...prev, emailTo: e.target.value }))}
            />
            <p className="text-xs text-gray-600">
              Leave empty to download directly. Enter email to receive report via email.
            </p>
          </div>

          {/* Generate Button */}
          <div className="flex items-center gap-2">
            <Button 
              onClick={generateReport} 
              disabled={isGenerating}
              className="flex-1"
            >
              {isGenerating ? (
                <>
                  <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                  Generating Report...
                </>
              ) : (
                <>
                  <Download className="h-4 w-4 mr-2" />
                  Generate Report
                </>
              )}
            </Button>
            {reportConfig.emailTo && (
              <Button variant="outline">
                <Mail className="h-4 w-4 mr-2" />
                Email Report
              </Button>
            )}
          </div>

          {/* Error Message */}
          {error && (
            <div className="flex items-center gap-2 text-red-600 bg-red-50 p-3 rounded-lg">
              <AlertCircle className="w-4 h-4" />
              {error}
            </div>
          )}
        </CardContent>
      </Card>

      {/* Recent Reports */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Reports</CardTitle>
          <CardDescription>Your recently generated reports</CardDescription>
        </CardHeader>
        <CardContent>
          {generatedReports.length === 0 ? (
            <div className="text-center py-8">
              <FileText className="h-12 w-12 mx-auto mb-4 text-gray-400" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">No Reports Yet</h3>
              <p className="text-gray-600">Generate your first report to get started.</p>
            </div>
          ) : (
            <div className="space-y-3">
              {generatedReports.map((report) => {
                const FormatIcon = getFormatIcon(report.format);
                return (
                  <div key={report.id} className="flex items-center gap-4 p-4 border rounded-lg">
                    <div className="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                      <FormatIcon className="w-5 h-5 text-gray-600" />
                    </div>
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <h3 className="font-semibold">{report.name}</h3>
                        {getStatusBadge(report.status)}
                      </div>
                      <div className="flex items-center gap-4 text-sm text-gray-600">
                        <span>{formatDate(report.createdAt)}</span>
                        <span>{formatFileSize(report.size)}</span>
                        <span className="capitalize">{report.format}</span>
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <Button 
                        variant="outline" 
                        size="sm"
                        onClick={() => downloadReport(report)}
                      >
                        <Download className="h-4 w-4 mr-2" />
                        Download
                      </Button>
                      <Button 
                        variant="outline" 
                        size="sm"
                        onClick={() => shareReport(report)}
                      >
                        <Share2 className="h-4 w-4 mr-2" />
                        Share
                      </Button>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
