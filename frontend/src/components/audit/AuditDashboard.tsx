'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Progress } from '../ui/progress';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '../ui/tabs';
import { 
  TrendingUp, 
  TrendingDown, 
  AlertTriangle, 
  CheckCircle, 
  Clock, 
  Target,
  BarChart3,
  FileText,
  Download,
  RefreshCw,
  ExternalLink,
  Zap,
  Shield,
  Search,
  Globe
} from 'lucide-react';
import { apiService } from '@/services/api';

interface AuditRun {
  id: string;
  status: 'DRAFT' | 'QUEUED' | 'RUNNING' | 'COMPLETED' | 'FAILED' | 'CANCELED';
  startedAt?: string;
  finishedAt?: string;
  healthScore?: number;
  pagesCrawled?: number;
  issuesFound?: number;
  quickWins?: QuickWin[];
  createdAt: string;
}

interface QuickWin {
  id: string;
  title: string;
  description: string;
  impact: 'HIGH' | 'MEDIUM' | 'LOW';
  effort: 'LOW' | 'MEDIUM' | 'HIGH';
  category: 'TECHNICAL' | 'ON_PAGE' | 'LOCAL' | 'CONTENT';
  affectedPages: number;
  estimatedTime: string;
}

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
}

interface AuditMetrics {
  healthScore: number;
  previousScore?: number;
  scoreChange?: number;
  totalIssues: number;
  criticalIssues: number;
  pagesAnalyzed: number;
  lastAuditDate: string;
}

export default function AuditDashboard() {
  const [auditRun, setAuditRun] = useState<AuditRun | null>(null);
  const [issues, setIssues] = useState<AuditIssue[]>([]);
  const [quickWins, setQuickWins] = useState<QuickWin[]>([]);
  const [metrics, setMetrics] = useState<AuditMetrics | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadAuditData();
  }, []);

  const loadAuditData = async () => {
    try {
      setIsLoading(true);
      setError(null);

      // Mock data for now - replace with actual API calls
      const mockAuditRun: AuditRun = {
        id: 'audit-001',
        status: 'COMPLETED',
        startedAt: '2024-01-15T10:00:00Z',
        finishedAt: '2024-01-15T10:45:00Z',
        healthScore: 73,
        pagesCrawled: 156,
        issuesFound: 23,
        createdAt: '2024-01-15T10:00:00Z',
        quickWins: [
          {
            id: 'qw-001',
            title: 'Fix Missing Meta Descriptions',
            description: 'Add meta descriptions to 12 pages to improve click-through rates',
            impact: 'HIGH',
            effort: 'LOW',
            category: 'ON_PAGE',
            affectedPages: 12,
            estimatedTime: '2-3 hours'
          },
          {
            id: 'qw-002',
            title: 'Optimize Page Titles',
            description: 'Update page titles to include target keywords and stay under 60 characters',
            impact: 'HIGH',
            effort: 'LOW',
            category: 'ON_PAGE',
            affectedPages: 8,
            estimatedTime: '1-2 hours'
          },
          {
            id: 'qw-003',
            title: 'Add Missing Alt Text',
            description: 'Add descriptive alt text to 15 images for better accessibility and SEO',
            impact: 'MEDIUM',
            effort: 'LOW',
            category: 'ON_PAGE',
            affectedPages: 15,
            estimatedTime: '1 hour'
          }
        ]
      };

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
          createdAt: '2024-01-15T10:00:00Z'
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
          createdAt: '2024-01-15T10:00:00Z'
        },
        {
          id: 'issue-003',
          title: 'Slow Page Load Speed',
          description: 'Pages are loading slower than recommended, affecting user experience',
          severity: 'P2',
          category: 'TECHNICAL',
          affectedPages: 8,
          status: 'OPEN',
          fixHint: 'Optimize images, enable compression, and minimize CSS/JS',
          createdAt: '2024-01-15T10:00:00Z'
        }
      ];

      const mockMetrics: AuditMetrics = {
        healthScore: 73,
        previousScore: 68,
        scoreChange: 5,
        totalIssues: 23,
        criticalIssues: 8,
        pagesAnalyzed: 156,
        lastAuditDate: '2024-01-15T10:45:00Z'
      };

      setAuditRun(mockAuditRun);
      setIssues(mockIssues);
      setQuickWins(mockAuditRun.quickWins || []);
      setMetrics(mockMetrics);

    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load audit data');
    } finally {
      setIsLoading(false);
    }
  };

  const getScoreColor = (score: number) => {
    if (score >= 80) return 'text-green-500';
    if (score >= 60) return 'text-yellow-500';
    return 'text-red-500';
  };

  const getScoreBgColor = (score: number) => {
    if (score >= 80) return 'bg-green-500';
    if (score >= 60) return 'bg-yellow-500';
    return 'bg-red-500';
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'P1': return 'bg-red-500';
      case 'P2': return 'bg-yellow-500';
      case 'P3': return 'bg-blue-500';
      default: return 'bg-gray-500';
    }
  };

  const getImpactColor = (impact: string) => {
    switch (impact) {
      case 'HIGH': return 'text-red-400';
      case 'MEDIUM': return 'text-yellow-400';
      case 'LOW': return 'text-green-400';
      default: return 'text-gray-400';
    }
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <RefreshCw className="w-8 h-8 animate-spin text-indigo-500" />
        <span className="ml-2 text-gray-600">Loading audit data...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-8">
        <AlertTriangle className="w-12 h-12 text-red-500 mx-auto mb-4" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">Error Loading Audit Data</h3>
        <p className="text-gray-600 mb-4">{error}</p>
        <Button onClick={loadAuditData} variant="outline">
          <RefreshCw className="w-4 h-4 mr-2" />
          Try Again
        </Button>
      </div>
    );
  }

  if (!auditRun || !metrics) {
    return (
      <div className="text-center py-8">
        <Target className="w-12 h-12 text-gray-400 mx-auto mb-4" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">No Audit Data Available</h3>
        <p className="text-gray-600 mb-4">Run your first audit to see detailed insights about your website.</p>
        <Button>
          <Zap className="w-4 h-4 mr-2" />
          Start New Audit
        </Button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">SEO Audit Dashboard</h1>
          <p className="text-gray-600">Monitor your website's SEO health and track improvements</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={loadAuditData}>
            <RefreshCw className="w-4 h-4 mr-2" />
            Refresh
          </Button>
          <Button>
            <Download className="w-4 h-4 mr-2" />
            Export Report
          </Button>
        </div>
      </div>

      {/* Health Score Card */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <BarChart3 className="w-5 h-5" />
            Overall Health Score
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-4">
              <div className="relative">
                <div className="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center">
                  <div className={`w-20 h-20 rounded-full ${getScoreBgColor(metrics.healthScore)} flex items-center justify-center`}>
                    <span className="text-2xl font-bold text-white">{metrics.healthScore}</span>
                  </div>
                </div>
                {metrics.scoreChange && (
                  <div className={`absolute -top-2 -right-2 flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${
                    metrics.scoreChange > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                  }`}>
                    {metrics.scoreChange > 0 ? <TrendingUp className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
                    {Math.abs(metrics.scoreChange)}
                  </div>
                )}
              </div>
              <div>
                <h3 className="text-lg font-semibold text-gray-900">Website Health</h3>
                <p className="text-gray-600">
                  {metrics.healthScore >= 80 ? 'Excellent' : 
                   metrics.healthScore >= 60 ? 'Good' : 
                   metrics.healthScore >= 40 ? 'Needs Improvement' : 'Poor'}
                </p>
                <p className="text-sm text-gray-500">
                  Last updated: {new Date(metrics.lastAuditDate).toLocaleDateString()}
                </p>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-4 text-center">
              <div>
                <div className="text-2xl font-bold text-gray-900">{metrics.totalIssues}</div>
                <div className="text-sm text-gray-600">Total Issues</div>
              </div>
              <div>
                <div className="text-2xl font-bold text-red-500">{metrics.criticalIssues}</div>
                <div className="text-sm text-gray-600">Critical Issues</div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Tabs */}
      <Tabs value={activeTab} onValueChange={setActiveTab}>
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="issues">Issues</TabsTrigger>
          <TabsTrigger value="quick-wins">Quick Wins</TabsTrigger>
          <TabsTrigger value="performance">Performance</TabsTrigger>
        </TabsList>

        <TabsContent value="overview" className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {/* Audit Status */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Clock className="w-4 h-4" />
                  Audit Status
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-gray-600">Status</span>
                    <Badge variant={auditRun.status === 'COMPLETED' ? 'default' : 'secondary'}>
                      {auditRun.status}
                    </Badge>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-gray-600">Pages Crawled</span>
                    <span className="font-medium">{auditRun.pagesCrawled}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-gray-600">Duration</span>
                    <span className="font-medium">
                      {auditRun.startedAt && auditRun.finishedAt ? 
                        `${Math.round((new Date(auditRun.finishedAt).getTime() - new Date(auditRun.startedAt).getTime()) / 60000)} min` : 
                        'N/A'
                      }
                    </span>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Issues Summary */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <AlertTriangle className="w-4 h-4" />
                  Issues Summary
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-3">
                  {['P1', 'P2', 'P3'].map(severity => {
                    const count = issues.filter(issue => issue.severity === severity).length;
                    return (
                      <div key={severity} className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                          <div className={`w-3 h-3 rounded-full ${getSeverityColor(severity)}`}></div>
                          <span className="text-sm text-gray-600">Priority {severity}</span>
                        </div>
                        <span className="font-medium">{count}</span>
                      </div>
                    );
                  })}
                </div>
              </CardContent>
            </Card>

            {/* Quick Wins */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Zap className="w-4 h-4" />
                  Quick Wins Available
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center">
                  <div className="text-3xl font-bold text-indigo-600">{quickWins.length}</div>
                  <p className="text-sm text-gray-600">High-impact improvements</p>
                  <Button size="sm" className="mt-2" onClick={() => setActiveTab('quick-wins')}>
                    View All
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="issues" className="space-y-4">
          <div className="space-y-4">
            {issues.map((issue) => (
              <Card key={issue.id}>
                <CardContent className="pt-6">
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-2">
                        <div className={`w-2 h-2 rounded-full ${getSeverityColor(issue.severity)}`}></div>
                        <h3 className="font-semibold text-gray-900">{issue.title}</h3>
                        <Badge variant="outline">{issue.category}</Badge>
                        <Badge variant="secondary">{issue.severity}</Badge>
                      </div>
                      <p className="text-gray-600 mb-3">{issue.description}</p>
                      <div className="flex items-center gap-4 text-sm text-gray-500">
                        <span>{issue.affectedPages} pages affected</span>
                        <span>•</span>
                        <span>{new Date(issue.createdAt).toLocaleDateString()}</span>
                      </div>
                      <div className="mt-3 p-3 bg-blue-50 rounded-lg">
                        <p className="text-sm text-blue-800">
                          <strong>Fix Hint:</strong> {issue.fixHint}
                        </p>
                      </div>
                    </div>
                    <div className="flex gap-2 ml-4">
                      <Button size="sm" variant="outline">
                        <ExternalLink className="w-4 h-4 mr-1" />
                        View Pages
                      </Button>
                      <Button size="sm">
                        Mark Resolved
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </TabsContent>

        <TabsContent value="quick-wins" className="space-y-4">
          <div className="space-y-4">
            {quickWins.map((win) => (
              <Card key={win.id}>
                <CardContent className="pt-6">
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-2">
                        <h3 className="font-semibold text-gray-900">{win.title}</h3>
                        <Badge variant="outline">{win.category}</Badge>
                        <Badge className={getImpactColor(win.impact)} variant="outline">
                          {win.impact} Impact
                        </Badge>
                        <Badge variant="secondary">{win.effort} Effort</Badge>
                      </div>
                      <p className="text-gray-600 mb-3">{win.description}</p>
                      <div className="flex items-center gap-4 text-sm text-gray-500">
                        <span>{win.affectedPages} pages affected</span>
                        <span>•</span>
                        <span>Est. {win.estimatedTime}</span>
                      </div>
                    </div>
                    <div className="flex gap-2 ml-4">
                      <Button size="sm" variant="outline">
                        <FileText className="w-4 h-4 mr-1" />
                        Details
                      </Button>
                      <Button size="sm">
                        <Zap className="w-4 h-4 mr-1" />
                        Start Fix
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </TabsContent>

        <TabsContent value="performance" className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Globe className="w-4 h-4" />
                  Page Performance
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div>
                    <div className="flex justify-between text-sm mb-1">
                      <span>Average Load Time</span>
                      <span>2.3s</span>
                    </div>
                    <Progress value={65} className="h-2" />
                  </div>
                  <div>
                    <div className="flex justify-between text-sm mb-1">
                      <span>Core Web Vitals</span>
                      <span>Good</span>
                    </div>
                    <Progress value={78} className="h-2" />
                  </div>
                  <div>
                    <div className="flex justify-between text-sm mb-1">
                      <span>Mobile Performance</span>
                      <span>Needs Work</span>
                    </div>
                    <Progress value={45} className="h-2" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Search className="w-4 h-4" />
                  SEO Metrics
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div>
                    <div className="flex justify-between text-sm mb-1">
                      <span>Indexability</span>
                      <span>92%</span>
                    </div>
                    <Progress value={92} className="h-2" />
                  </div>
                  <div>
                    <div className="flex justify-between text-sm mb-1">
                      <span>Internal Linking</span>
                      <span>78%</span>
                    </div>
                    <Progress value={78} className="h-2" />
                  </div>
                  <div>
                    <div className="flex justify-between text-sm mb-1">
                      <span>Content Quality</span>
                      <span>85%</span>
                    </div>
                    <Progress value={85} className="h-2" />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}
