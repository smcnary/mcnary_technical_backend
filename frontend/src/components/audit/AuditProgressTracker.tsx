'use client';

import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Badge } from '../ui/badge';
import { Progress } from '../ui/progress';
import { 
  Clock, 
  CheckCircle, 
  AlertCircle, 
  Play, 
  Pause, 
  RotateCcw,
  BarChart3,
  Globe,
  Search,
  FileText,
  Zap
} from 'lucide-react';

interface AuditProgress {
  id: string;
  status: 'QUEUED' | 'RUNNING' | 'COMPLETED' | 'FAILED' | 'CANCELED';
  progress: number;
  currentStep: string;
  startedAt?: string;
  estimatedCompletion?: string;
  pagesCrawled: number;
  totalPages: number;
  issuesFound: number;
  healthScore?: number;
  steps: AuditStep[];
}

interface AuditStep {
  id: string;
  name: string;
  status: 'PENDING' | 'RUNNING' | 'COMPLETED' | 'FAILED';
  progress: number;
  startedAt?: string;
  completedAt?: string;
  details?: string;
}

export default function AuditProgressTracker() {
  const [auditProgress, setAuditProgress] = useState<AuditProgress | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadAuditProgress();
    // Poll for updates every 5 seconds if audit is running
    const interval = setInterval(() => {
      if (auditProgress?.status === 'RUNNING') {
        loadAuditProgress();
      }
    }, 5000);

    return () => clearInterval(interval);
  }, [auditProgress?.status]);

  const loadAuditProgress = async () => {
    try {
      setIsLoading(true);
      setError(null);

      // Mock data - replace with actual API call
      const mockProgress: AuditProgress = {
        id: 'audit-001',
        status: 'RUNNING',
        progress: 65,
        currentStep: 'Analyzing Page Content',
        startedAt: '2024-01-15T10:00:00Z',
        estimatedCompletion: '2024-01-15T10:45:00Z',
        pagesCrawled: 102,
        totalPages: 156,
        issuesFound: 12,
        steps: [
          {
            id: 'step-1',
            name: 'Website Discovery',
            status: 'COMPLETED',
            progress: 100,
            startedAt: '2024-01-15T10:00:00Z',
            completedAt: '2024-01-15T10:05:00Z',
            details: 'Found 156 pages to analyze'
          },
          {
            id: 'step-2',
            name: 'Technical Analysis',
            status: 'COMPLETED',
            progress: 100,
            startedAt: '2024-01-15T10:05:00Z',
            completedAt: '2024-01-15T10:15:00Z',
            details: 'Analyzed page speed, mobile-friendliness, and technical SEO'
          },
          {
            id: 'step-3',
            name: 'Content Analysis',
            status: 'RUNNING',
            progress: 65,
            startedAt: '2024-01-15T10:15:00Z',
            details: 'Analyzing meta tags, headings, and content quality'
          },
          {
            id: 'step-4',
            name: 'Local SEO Check',
            status: 'PENDING',
            progress: 0,
            details: 'Checking Google Business Profile and local signals'
          },
          {
            id: 'step-5',
            name: 'Report Generation',
            status: 'PENDING',
            progress: 0,
            details: 'Compiling findings and generating recommendations'
          }
        ]
      };

      setAuditProgress(mockProgress);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load audit progress');
    } finally {
      setIsLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'COMPLETED': return 'text-green-500';
      case 'RUNNING': return 'text-blue-500';
      case 'FAILED': return 'text-red-500';
      case 'CANCELED': return 'text-gray-500';
      default: return 'text-gray-400';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'COMPLETED': return <CheckCircle className="w-5 h-5 text-green-500" />;
      case 'RUNNING': return <Clock className="w-5 h-5 text-blue-500 animate-pulse" />;
      case 'FAILED': return <AlertCircle className="w-5 h-5 text-red-500" />;
      case 'CANCELED': return <Pause className="w-5 h-5 text-gray-500" />;
      default: return <Clock className="w-5 h-5 text-gray-400" />;
    }
  };

  const formatDuration = (startedAt: string, completedAt?: string) => {
    const start = new Date(startedAt);
    const end = completedAt ? new Date(completedAt) : new Date();
    const duration = Math.round((end.getTime() - start.getTime()) / 1000);
    
    if (duration < 60) return `${duration}s`;
    if (duration < 3600) return `${Math.round(duration / 60)}m`;
    return `${Math.round(duration / 3600)}h`;
  };

  const getEstimatedTimeRemaining = () => {
    if (!auditProgress?.estimatedCompletion) return null;
    
    const now = new Date();
    const estimated = new Date(auditProgress.estimatedCompletion);
    const remaining = Math.max(0, Math.round((estimated.getTime() - now.getTime()) / 60000));
    
    return remaining > 0 ? `${remaining} minutes remaining` : 'Completing soon...';
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
        <span className="ml-2 text-gray-600">Loading audit progress...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-8">
        <AlertCircle className="w-12 h-12 text-red-500 mx-auto mb-4" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">Error Loading Progress</h3>
        <p className="text-gray-600 mb-4">{error}</p>
        <Button onClick={loadAuditProgress} variant="outline">
          <RotateCcw className="w-4 h-4 mr-2" />
          Try Again
        </Button>
      </div>
    );
  }

  if (!auditProgress) {
    return (
      <div className="text-center py-8">
        <BarChart3 className="w-12 h-12 text-gray-400 mx-auto mb-4" />
        <h3 className="text-lg font-semibold text-gray-900 mb-2">No Active Audit</h3>
        <p className="text-gray-600 mb-4">Start a new audit to track its progress in real-time.</p>
        <Button>
          <Play className="w-4 h-4 mr-2" />
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
          <h1 className="text-2xl font-bold text-gray-900">Audit Progress</h1>
          <p className="text-gray-600">Track your SEO audit in real-time</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={loadAuditProgress}>
            <RotateCcw className="w-4 h-4 mr-2" />
            Refresh
          </Button>
          {auditProgress.status === 'RUNNING' && (
            <Button variant="outline">
              <Pause className="w-4 h-4 mr-2" />
              Pause
            </Button>
          )}
        </div>
      </div>

      {/* Overall Progress */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            {getStatusIcon(auditProgress.status)}
            Audit Status: {auditProgress.status}
          </CardTitle>
          <CardDescription>
            {auditProgress.currentStep}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {/* Progress Bar */}
            <div>
              <div className="flex justify-between text-sm mb-2">
                <span>Overall Progress</span>
                <span>{auditProgress.progress}%</span>
              </div>
              <Progress value={auditProgress.progress} className="h-3" />
            </div>

            {/* Stats */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div className="text-center">
                <div className="text-2xl font-bold text-indigo-600">{auditProgress.pagesCrawled}</div>
                <div className="text-sm text-gray-600">Pages Crawled</div>
                <div className="text-xs text-gray-500">of {auditProgress.totalPages}</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-orange-600">{auditProgress.issuesFound}</div>
                <div className="text-sm text-gray-600">Issues Found</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-green-600">
                  {auditProgress.healthScore || '--'}
                </div>
                <div className="text-sm text-gray-600">Health Score</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-blue-600">
                  {auditProgress.startedAt ? formatDuration(auditProgress.startedAt) : '--'}
                </div>
                <div className="text-sm text-gray-600">Duration</div>
              </div>
            </div>

            {/* Time Remaining */}
            {auditProgress.status === 'RUNNING' && getEstimatedTimeRemaining() && (
              <div className="text-center p-3 bg-blue-50 rounded-lg">
                <Clock className="w-4 h-4 inline mr-2 text-blue-600" />
                <span className="text-blue-800 font-medium">{getEstimatedTimeRemaining()}</span>
              </div>
            )}
          </div>
        </CardContent>
      </Card>

      {/* Steps */}
      <Card>
        <CardHeader>
          <CardTitle>Audit Steps</CardTitle>
          <CardDescription>
            Detailed progress for each phase of the audit
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {auditProgress.steps.map((step, index) => (
              <div key={step.id} className="flex items-start gap-4">
                <div className="flex-shrink-0 mt-1">
                  {getStatusIcon(step.status)}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between mb-2">
                    <h3 className="font-medium text-gray-900">{step.name}</h3>
                    <div className="flex items-center gap-2">
                      <Badge variant={step.status === 'COMPLETED' ? 'default' : 'secondary'}>
                        {step.status}
                      </Badge>
                      {step.status === 'RUNNING' && (
                        <span className="text-sm text-gray-500">{step.progress}%</span>
                      )}
                    </div>
                  </div>
                  
                  {step.details && (
                    <p className="text-sm text-gray-600 mb-2">{step.details}</p>
                  )}
                  
                  {step.status === 'RUNNING' && (
                    <Progress value={step.progress} className="h-2 mb-2" />
                  )}
                  
                  <div className="flex items-center gap-4 text-xs text-gray-500">
                    {step.startedAt && (
                      <span>Started: {new Date(step.startedAt).toLocaleTimeString()}</span>
                    )}
                    {step.completedAt && (
                      <span>Completed: {new Date(step.completedAt).toLocaleTimeString()}</span>
                    )}
                    {step.startedAt && (
                      <span>
                        Duration: {formatDuration(step.startedAt, step.completedAt)}
                      </span>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Quick Actions */}
      {auditProgress.status === 'COMPLETED' && (
        <Card>
          <CardHeader>
            <CardTitle>Audit Complete!</CardTitle>
            <CardDescription>
              Your SEO audit has finished. View the results and start implementing improvements.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="flex gap-2">
              <Button>
                <BarChart3 className="w-4 h-4 mr-2" />
                View Results
              </Button>
              <Button variant="outline">
                <FileText className="w-4 h-4 mr-2" />
                Download Report
              </Button>
              <Button variant="outline">
                <Zap className="w-4 h-4 mr-2" />
                Start Quick Wins
              </Button>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
