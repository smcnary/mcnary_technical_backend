'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { 
  Search, 
  Plus, 
  Eye, 
  Calendar,
  TrendingUp,
  Loader2,
  AlertCircle,
  FileText,
  Target
} from 'lucide-react';
import { CaseStudy } from '@/services/api';
import Link from 'next/link';

export default function ClientCasesPage() {
  const { user, isClientAdmin, isClientStaff } = useAuth();
  const { 
    caseStudies, 
    getCaseStudies, 
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [searchTerm, setSearchTerm] = useState('');
  const [practiceAreaFilter, setPracticeAreaFilter] = useState<string>('all');

  const isLoading = getLoadingState('caseStudies');
  const error = getErrorState('caseStudies');

  useEffect(() => {
    loadCaseStudies();
  }, []);

  const loadCaseStudies = async () => {
    try {
      clearError('caseStudies');
      await getCaseStudies();
    } catch (err) {
      console.error('Failed to load case studies:', err);
    }
  };

  const filteredCaseStudies = caseStudies.filter(caseStudy => {
    const matchesSearch = !searchTerm || 
      caseStudy.title?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      caseStudy.summary?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      caseStudy.practiceArea?.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesPracticeArea = practiceAreaFilter === 'all' || 
      caseStudy.practiceArea === practiceAreaFilter;
    
    return matchesSearch && matchesPracticeArea && caseStudy.isActive;
  });

  const practiceAreas = Array.from(
    new Set(caseStudies.map(cs => cs.practiceArea).filter(Boolean))
  );

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const getMetricsDisplay = (metricsJson: Record<string, unknown>) => {
    if (!metricsJson || typeof metricsJson !== 'object') return null;
    
    const metrics = Object.entries(metricsJson).slice(0, 3);
    return metrics.map(([key, value]) => (
      <div key={key} className="text-sm">
        <span className="font-medium">{key}:</span> {String(value)}
      </div>
    ));
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Cases" 
        subtitle="View successful case studies and track performance metrics" 
      />

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Cases</CardTitle>
            <FileText className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{caseStudies.length}</div>
            <p className="text-xs text-muted-foreground">
              Active case studies
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Practice Areas</CardTitle>
            <Target className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{practiceAreas.length}</div>
            <p className="text-xs text-muted-foreground">
              Different areas covered
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Avg. Performance</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">+127%</div>
            <p className="text-xs text-muted-foreground">
              Average improvement
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Success Rate</CardTitle>
            <Eye className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">94%</div>
            <p className="text-xs text-muted-foreground">
              Client satisfaction
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Filters and Search */}
      <Card>
        <CardHeader>
          <CardTitle>Case Studies</CardTitle>
          <CardDescription>
            Browse through successful case studies and performance metrics
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col sm:flex-row gap-4 mb-6">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                <Input
                  placeholder="Search case studies by title, summary, or practice area..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div className="flex gap-2">
              <select
                value={practiceAreaFilter}
                onChange={(e) => setPracticeAreaFilter(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="all">All Practice Areas</option>
                {practiceAreas.map(area => (
                  <option key={area} value={area}>{area}</option>
                ))}
              </select>
              {(isClientAdmin || isClientStaff) && (
                <Button>
                  <Plus className="h-4 w-4 mr-2" />
                  New Case
                </Button>
              )}
            </div>
          </div>

          {/* Case Studies Grid */}
          {isLoading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-6 w-6 animate-spin mr-2" />
              Loading case studies...
            </div>
          ) : error ? (
            <div className="flex items-center justify-center py-8 text-red-600">
              <AlertCircle className="h-6 w-6 mr-2" />
              {error}
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredCaseStudies.length === 0 ? (
                <div className="col-span-full text-center py-8 text-muted-foreground">
                  {searchTerm || practiceAreaFilter !== 'all' 
                    ? 'No case studies match your current filters' 
                    : 'No case studies available yet.'}
                </div>
              ) : (
                filteredCaseStudies.map((caseStudy) => (
                  <Card key={caseStudy.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                    <CardHeader className="pb-3">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <CardTitle className="text-lg line-clamp-2">
                            {caseStudy.title}
                          </CardTitle>
                          <CardDescription className="mt-1">
                            {caseStudy.practiceArea && (
                              <Badge variant="outline" className="text-xs">
                                {caseStudy.practiceArea}
                              </Badge>
                            )}
                          </CardDescription>
                        </div>
                        <div className="flex items-center gap-1 text-xs text-muted-foreground">
                          <Calendar className="h-3 w-3" />
                          {formatDate(caseStudy.createdAt)}
                        </div>
                      </div>
                    </CardHeader>
                    <CardContent className="pt-0">
                      {caseStudy.summary && (
                        <p className="text-sm text-muted-foreground mb-4 line-clamp-3">
                          {caseStudy.summary}
                        </p>
                      )}
                      
                      {caseStudy.metricsJson && Object.keys(caseStudy.metricsJson).length > 0 && (
                        <div className="space-y-2 mb-4">
                          <h4 className="text-sm font-medium">Key Metrics:</h4>
                          <div className="space-y-1">
                            {getMetricsDisplay(caseStudy.metricsJson)}
                          </div>
                        </div>
                      )}

                      <div className="flex items-center justify-between">
                        <Link href={`/client/cases/${caseStudy.id}`}>
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4 mr-2" />
                            View Details
                          </Button>
                        </Link>
                        {(isClientAdmin || isClientStaff) && (
                          <Button variant="ghost" size="sm">
                            Edit
                          </Button>
                        )}
                      </div>
                    </CardContent>
                  </Card>
                ))
              )}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}