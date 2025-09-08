'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Input } from '../ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '../ui/select';
import { Checkbox } from '../ui/checkbox';
import { 
  AlertTriangle, 
  CheckCircle, 
  Clock, 
  Filter, 
  Search, 
  ExternalLink,
  FileText,
  Zap,
  Eye,
  EyeOff,
  MoreHorizontal
} from 'lucide-react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '../ui/dropdown-menu';

interface AuditIssue {
  id: string;
  title: string;
  description: string;
  severity: 'P1' | 'P2' | 'P3';
  category: 'TECHNICAL' | 'ON_PAGE' | 'LOCAL' | 'CONTENT';
  affectedPages: number;
  status: 'OPEN' | 'IN_PROGRESS' | 'RESOLVED' | 'IGNORED';
  fixHint: string;
  createdAt: string;
  updatedAt?: string;
  resolvedAt?: string;
  pages?: string[];
}

interface IssueFilters {
  severity: string;
  category: string;
  status: string;
  search: string;
}

export default function AuditIssuesManager() {
  const [issues, setIssues] = useState<AuditIssue[]>([]);
  const [filteredIssues, setFilteredIssues] = useState<AuditIssue[]>([]);
  const [selectedIssues, setSelectedIssues] = useState<string[]>([]);
  const [filters, setFilters] = useState<IssueFilters>({
    severity: 'all',
    category: 'all',
    status: 'all',
    search: ''
  });
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadIssues();
  }, []);

  useEffect(() => {
    applyFilters();
  }, [issues, filters]);

  const loadIssues = async () => {
    try {
      setIsLoading(true);
      setError(null);

      // Mock data - replace with actual API call
      const mockIssues: AuditIssue[] = [
        {
          id: 'issue-001',
          title: 'Missing Meta Descriptions',
          description: 'Pages without meta descriptions have lower click-through rates in search results',
          severity: 'P1',
          category: 'ON_PAGE',
          affectedPages: 12,
          status: 'OPEN',
          fixHint: 'Write unique, compelling meta descriptions (150-160 characters) for each page',
          createdAt: '2024-01-15T10:00:00Z',
          pages: ['/about', '/services', '/contact', '/blog/post-1', '/blog/post-2']
        },
        {
          id: 'issue-002',
          title: 'Duplicate Page Titles',
          description: 'Multiple pages share the same title, which confuses search engines',
          severity: 'P1',
          category: 'ON_PAGE',
          affectedPages: 5,
          status: 'OPEN',
          fixHint: 'Create unique, descriptive titles for each page',
          createdAt: '2024-01-15T10:00:00Z',
          pages: ['/services/seo', '/services/local-seo']
        },
        {
          id: 'issue-003',
          title: 'Slow Page Load Speed',
          description: 'Pages are loading slower than recommended, affecting user experience',
          severity: 'P2',
          category: 'TECHNICAL',
          affectedPages: 8,
          status: 'IN_PROGRESS',
          fixHint: 'Optimize images, enable compression, and minimize CSS/JS',
          createdAt: '2024-01-15T10:00:00Z',
          pages: ['/home', '/services', '/portfolio']
        },
        {
          id: 'issue-004',
          title: 'Missing Alt Text',
          description: 'Images without alt text are not accessible and miss SEO opportunities',
          severity: 'P2',
          category: 'ON_PAGE',
          affectedPages: 15,
          status: 'OPEN',
          fixHint: 'Add descriptive alt text to all images',
          createdAt: '2024-01-15T10:00:00Z'
        },
        {
          id: 'issue-005',
          title: 'No Google Business Profile',
          description: 'Missing Google Business Profile limits local search visibility',
          severity: 'P1',
          category: 'LOCAL',
          affectedPages: 1,
          status: 'RESOLVED',
          fixHint: 'Create and optimize Google Business Profile',
          createdAt: '2024-01-15T10:00:00Z',
          resolvedAt: '2024-01-16T14:30:00Z'
        },
        {
          id: 'issue-006',
          title: 'Thin Content Pages',
          description: 'Some pages have insufficient content for good SEO performance',
          severity: 'P3',
          category: 'CONTENT',
          affectedPages: 6,
          status: 'OPEN',
          fixHint: 'Expand content to at least 300 words per page',
          createdAt: '2024-01-15T10:00:00Z'
        }
      ];

      setIssues(mockIssues);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load issues');
    } finally {
      setIsLoading(false);
    }
  };

  const applyFilters = () => {
    let filtered = [...issues];

    if (filters.severity !== 'all') {
      filtered = filtered.filter(issue => issue.severity === filters.severity);
    }

    if (filters.category !== 'all') {
      filtered = filtered.filter(issue => issue.category === filters.category);
    }

    if (filters.status !== 'all') {
      filtered = filtered.filter(issue => issue.status === filters.status);
    }

    if (filters.search) {
      filtered = filtered.filter(issue => 
        issue.title.toLowerCase().includes(filters.search.toLowerCase()) ||
        issue.description.toLowerCase().includes(filters.search.toLowerCase())
      );
    }

    setFilteredIssues(filtered);
  };

  const updateIssueStatus = async (issueId: string, newStatus: AuditIssue['status']) => {
    try {
      // Mock API call - replace with actual implementation
      setIssues(prev => prev.map(issue => 
        issue.id === issueId 
          ? { 
              ...issue, 
              status: newStatus,
              updatedAt: new Date().toISOString(),
              resolvedAt: newStatus === 'RESOLVED' ? new Date().toISOString() : undefined
            }
          : issue
      ));
    } catch (err) {
      console.error('Failed to update issue status:', err);
    }
  };

  const bulkUpdateStatus = async (newStatus: AuditIssue['status']) => {
    try {
      // Mock API call - replace with actual implementation
      setIssues(prev => prev.map(issue => 
        selectedIssues.includes(issue.id)
          ? { 
              ...issue, 
              status: newStatus,
              updatedAt: new Date().toISOString(),
              resolvedAt: newStatus === 'RESOLVED' ? new Date().toISOString() : undefined
            }
          : issue
      ));
      setSelectedIssues([]);
    } catch (err) {
      console.error('Failed to bulk update issues:', err);
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'P1': return 'bg-red-500';
      case 'P2': return 'bg-yellow-500';
      case 'P3': return 'bg-blue-500';
      default: return 'bg-gray-500';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'RESOLVED': return 'text-green-600 bg-green-100';
      case 'IN_PROGRESS': return 'text-blue-600 bg-blue-100';
      case 'IGNORED': return 'text-gray-600 bg-gray-100';
      default: return 'text-orange-600 bg-orange-100';
    }
  };

  const getCategoryIcon = (category: string) => {
    switch (category) {
      case 'TECHNICAL': return '⚙️';
      case 'ON_PAGE': return '📄';
      case 'LOCAL': return '📍';
      case 'CONTENT': return '📝';
      default: return '❓';
    }
  };

  const handleSelectIssue = (issueId: string) => {
    setSelectedIssues(prev => 
      prev.includes(issueId) 
        ? prev.filter(id => id !== issueId)
        : [...prev, issueId]
    );
  };

  const handleSelectAll = () => {
    if (selectedIssues.length === filteredIssues.length) {
      setSelectedIssues([]);
    } else {
      setSelectedIssues(filteredIssues.map(issue => issue.id));
    }
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
        <span className="ml-2 text-gray-600">Loading issues...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-8">
        <AlertTriangle className="w-12 h-12 text-red-500 mx-auto mb-4" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">Error Loading Issues</h3>
        <p className="text-gray-600 mb-4">{error}</p>
        <Button onClick={loadIssues} variant="outline">
          Try Again
        </Button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">SEO Issues</h1>
          <p className="text-gray-600">Manage and track your website's SEO issues</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline">
            <FileText className="w-4 h-4 mr-2" />
            Export
          </Button>
          <Button>
            <Zap className="w-4 h-4 mr-2" />
            Quick Fixes
          </Button>
        </div>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="pt-6">
          <div className="flex flex-wrap gap-4">
            <div className="flex-1 min-w-64">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                <Input
                  placeholder="Search issues..."
                  value={filters.search}
                  onChange={(e) => setFilters(prev => ({ ...prev, search: e.target.value }))}
                  className="pl-10"
                />
              </div>
            </div>
            <Select value={filters.severity} onValueChange={(value) => setFilters(prev => ({ ...prev, severity: value }))}>
              <SelectTrigger className="w-40">
                <SelectValue placeholder="Severity" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Severity</SelectItem>
                <SelectItem value="P1">Priority 1</SelectItem>
                <SelectItem value="P2">Priority 2</SelectItem>
                <SelectItem value="P3">Priority 3</SelectItem>
              </SelectContent>
            </Select>
            <Select value={filters.category} onValueChange={(value) => setFilters(prev => ({ ...prev, category: value }))}>
              <SelectTrigger className="w-40">
                <SelectValue placeholder="Category" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Categories</SelectItem>
                <SelectItem value="TECHNICAL">Technical</SelectItem>
                <SelectItem value="ON_PAGE">On-Page</SelectItem>
                <SelectItem value="LOCAL">Local SEO</SelectItem>
                <SelectItem value="CONTENT">Content</SelectItem>
              </SelectContent>
            </Select>
            <Select value={filters.status} onValueChange={(value) => setFilters(prev => ({ ...prev, status: value }))}>
              <SelectTrigger className="w-40">
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="OPEN">Open</SelectItem>
                <SelectItem value="IN_PROGRESS">In Progress</SelectItem>
                <SelectItem value="RESOLVED">Resolved</SelectItem>
                <SelectItem value="IGNORED">Ignored</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Bulk Actions */}
      {selectedIssues.length > 0 && (
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center justify-between">
              <span className="text-sm text-gray-600">
                {selectedIssues.length} issue{selectedIssues.length !== 1 ? 's' : ''} selected
              </span>
              <div className="flex gap-2">
                <Button size="sm" variant="outline" onClick={() => bulkUpdateStatus('IN_PROGRESS')}>
                  Mark In Progress
                </Button>
                <Button size="sm" variant="outline" onClick={() => bulkUpdateStatus('RESOLVED')}>
                  Mark Resolved
                </Button>
                <Button size="sm" variant="outline" onClick={() => bulkUpdateStatus('IGNORED')}>
                  Ignore
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Issues List */}
      <div className="space-y-4">
        {filteredIssues.length === 0 ? (
          <Card>
            <CardContent className="pt-6 text-center">
              <AlertTriangle className="w-12 h-12 text-gray-400 mx-auto mb-4" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">No Issues Found</h3>
              <p className="text-gray-600">Try adjusting your filters or search terms.</p>
            </CardContent>
          </Card>
        ) : (
          filteredIssues.map((issue) => (
            <Card key={issue.id}>
              <CardContent className="pt-6">
                <div className="flex items-start gap-4">
                  <Checkbox
                    checked={selectedIssues.includes(issue.id)}
                    onCheckedChange={() => handleSelectIssue(issue.id)}
                    className="mt-1"
                  />
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between mb-2">
                      <div className="flex items-center gap-2">
                        <span className="text-lg">{getCategoryIcon(issue.category)}</span>
                        <h3 className="font-semibold text-gray-900">{issue.title}</h3>
                        <div className={`w-2 h-2 rounded-full ${getSeverityColor(issue.severity)}`}></div>
                        <Badge variant="outline">{issue.severity}</Badge>
                        <Badge variant="outline">{issue.category}</Badge>
                        <Badge className={getStatusColor(issue.status)} variant="secondary">
                          {issue.status.replace('_', ' ')}
                        </Badge>
                      </div>
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" size="sm">
                            <MoreHorizontal className="w-4 h-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem onClick={() => updateIssueStatus(issue.id, 'IN_PROGRESS')}>
                            Mark In Progress
                          </DropdownMenuItem>
                          <DropdownMenuItem onClick={() => updateIssueStatus(issue.id, 'RESOLVED')}>
                            Mark Resolved
                          </DropdownMenuItem>
                          <DropdownMenuItem onClick={() => updateIssueStatus(issue.id, 'IGNORED')}>
                            Ignore Issue
                          </DropdownMenuItem>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </div>
                    
                    <p className="text-gray-600 mb-3">{issue.description}</p>
                    
                    <div className="flex items-center gap-4 text-sm text-gray-500 mb-3">
                      <span>{issue.affectedPages} pages affected</span>
                      <span>•</span>
                      <span>Created: {new Date(issue.createdAt).toLocaleDateString()}</span>
                      {issue.resolvedAt && (
                        <>
                          <span>•</span>
                          <span>Resolved: {new Date(issue.resolvedAt).toLocaleDateString()}</span>
                        </>
                      )}
                    </div>
                    
                    <div className="p-3 bg-blue-50 rounded-lg mb-3">
                      <p className="text-sm text-blue-800">
                        <strong>Fix Hint:</strong> {issue.fixHint}
                      </p>
                    </div>
                    
                    {issue.pages && issue.pages.length > 0 && (
                      <div className="flex items-center gap-2">
                        <span className="text-sm text-gray-600">Affected pages:</span>
                        <div className="flex flex-wrap gap-1">
                          {issue.pages.slice(0, 3).map((page, index) => (
                            <Badge key={index} variant="outline" className="text-xs">
                              {page}
                            </Badge>
                          ))}
                          {issue.pages.length > 3 && (
                            <Badge variant="outline" className="text-xs">
                              +{issue.pages.length - 3} more
                            </Badge>
                          )}
                        </div>
                      </div>
                    )}
                  </div>
                  <div className="flex gap-2 ml-4">
                    <Button size="sm" variant="outline">
                      <ExternalLink className="w-4 h-4 mr-1" />
                      View Pages
                    </Button>
                    <Button size="sm">
                      <Zap className="w-4 h-4 mr-1" />
                      Fix Now
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))
        )}
      </div>

      {/* Summary Stats */}
      <Card>
        <CardHeader>
          <CardTitle>Issue Summary</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="text-center">
              <div className="text-2xl font-bold text-red-500">
                {issues.filter(i => i.severity === 'P1').length}
              </div>
              <div className="text-sm text-gray-600">Priority 1</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-yellow-500">
                {issues.filter(i => i.severity === 'P2').length}
              </div>
              <div className="text-sm text-gray-600">Priority 2</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-blue-500">
                {issues.filter(i => i.severity === 'P3').length}
              </div>
              <div className="text-sm text-gray-600">Priority 3</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-green-500">
                {issues.filter(i => i.status === 'RESOLVED').length}
              </div>
              <div className="text-sm text-gray-600">Resolved</div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
