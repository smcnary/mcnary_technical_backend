'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import PageHeader from '@/components/portal/PageHeader';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  ArrowLeft,
  Calendar,
  TrendingUp,
  Target,
  Eye,
  Edit,
  Share2,
  Download,
  Loader2,
  AlertCircle,
  FileText,
  BarChart3
} from 'lucide-react';
import { CaseStudy } from '@/services/api';
import Link from 'next/link';

interface CaseDetailsPageProps {
  params: Promise<{ caseId: string }>;
}

export default function CaseDetailsPage({ params }: CaseDetailsPageProps) {
  const { user, isClientAdmin, isClientStaff } = useAuth();
  const { 
    caseStudies, 
    getCaseStudy, 
    getLoadingState, 
    getErrorState, 
    clearError 
  } = useData();

  const [caseStudy, setCaseStudy] = useState<CaseStudy | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [caseId, setCaseId] = useState<string>('');

  const loadingState = getLoadingState('caseStudies');
  const errorState = getErrorState('caseStudies');

  useEffect(() => {
    const loadParams = async () => {
      const resolvedParams = await params;
      setCaseId(resolvedParams.caseId);
    };
    loadParams();
  }, [params]);

  useEffect(() => {
    if (caseId) {
      loadCaseStudy();
    }
  }, [caseId]);

  const loadCaseStudy = async () => {
    if (!caseId) return;
    
    setIsLoading(true);
    try {
      clearError('caseStudies');
      const caseData = await getCaseStudy(caseId);
      setCaseStudy(caseData);
    } catch (err) {
      console.error('Failed to load case study:', err);
    } finally {
      setIsLoading(false);
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const getMetricsDisplay = (metricsJson: Record<string, unknown>) => {
    if (!metricsJson || typeof metricsJson !== 'object') return [];
    
    return Object.entries(metricsJson).map(([key, value]) => ({
      label: key,
      value: String(value),
      formatted: typeof value === 'number' ? value.toLocaleString() : String(value)
    }));
  };

  if (isLoading) {
    return (
      <div className="space-y-6">
        <div className="flex items-center gap-4">
          <Link href="/client/cases">
            <Button variant="outline" size="sm">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Cases
            </Button>
          </Link>
        </div>
        <div className="flex items-center justify-center py-8">
          <Loader2 className="h-6 w-6 animate-spin mr-2" />
          Loading case study...
        </div>
      </div>
    );
  }

  if (errorState || !caseStudy) {
    return (
      <div className="space-y-6">
        <div className="flex items-center gap-4">
          <Link href="/client/cases">
            <Button variant="outline" size="sm">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Cases
            </Button>
          </Link>
        </div>
        <div className="flex items-center justify-center py-8 text-red-600">
          <AlertCircle className="h-6 w-6 mr-2" />
          {errorState || 'Case study not found'}
        </div>
      </div>
    );
  }

  const metrics = getMetricsDisplay(caseStudy.metricsJson);

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link href="/client/cases">
            <Button variant="outline" size="sm">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Cases
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold">{caseStudy.title}</h1>
            <div className="flex items-center gap-4 mt-2 text-sm text-muted-foreground">
              <div className="flex items-center gap-1">
                <Calendar className="h-4 w-4" />
                {formatDate(caseStudy.createdAt)}
              </div>
              {caseStudy.practiceArea && (
                <Badge variant="outline">{caseStudy.practiceArea}</Badge>
              )}
            </div>
          </div>
        </div>
        <div className="flex items-center gap-2">
          {(isClientAdmin || isClientStaff) && (
            <Button variant="outline" size="sm">
              <Edit className="h-4 w-4 mr-2" />
              Edit
            </Button>
          )}
          <Button variant="outline" size="sm">
            <Share2 className="h-4 w-4 mr-2" />
            Share
          </Button>
          <Button variant="outline" size="sm">
            <Download className="h-4 w-4 mr-2" />
            Export
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Content */}
        <div className="lg:col-span-2 space-y-6">
          {/* Summary */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5" />
                Case Study Summary
              </CardTitle>
            </CardHeader>
            <CardContent>
              {caseStudy.summary ? (
                <p className="text-muted-foreground leading-relaxed">
                  {caseStudy.summary}
                </p>
              ) : (
                <p className="text-muted-foreground italic">
                  No summary available for this case study.
                </p>
              )}
            </CardContent>
          </Card>

          {/* Performance Metrics */}
          {metrics.length > 0 && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <BarChart3 className="h-5 w-5" />
                  Performance Metrics
                </CardTitle>
                <CardDescription>
                  Key performance indicators and results achieved
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {metrics.map((metric, index) => (
                    <div key={index} className="p-4 border rounded-lg">
                      <div className="flex items-center justify-between">
                        <h4 className="font-medium text-sm text-muted-foreground">
                          {metric.label}
                        </h4>
                        <TrendingUp className="h-4 w-4 text-green-500" />
                      </div>
                      <div className="mt-2">
                        <div className="text-2xl font-bold">
                          {metric.formatted}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          )}

          {/* Detailed Results */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="h-5 w-5" />
                Detailed Results
              </CardTitle>
              <CardDescription>
                Comprehensive analysis and outcomes
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="prose max-w-none">
                <p className="text-muted-foreground">
                  This case study demonstrates the effectiveness of our SEO strategies 
                  and the measurable impact on client performance. The results speak to 
                  our commitment to delivering exceptional outcomes.
                </p>
                
                <h3 className="text-lg font-semibold mt-6 mb-3">Key Achievements</h3>
                <ul className="list-disc list-inside space-y-2 text-muted-foreground">
                  <li>Significant improvement in search engine rankings</li>
                  <li>Increased organic traffic and lead generation</li>
                  <li>Enhanced local visibility and Google Business Profile performance</li>
                  <li>Improved conversion rates and client satisfaction</li>
                </ul>

                <h3 className="text-lg font-semibold mt-6 mb-3">Strategy Overview</h3>
                <p className="text-muted-foreground">
                  Our comprehensive approach included technical SEO optimization, 
                  content strategy development, local SEO enhancement, and ongoing 
                  performance monitoring to ensure sustained results.
                </p>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Sidebar */}
        <div className="space-y-6">
          {/* Case Study Info */}
          <Card>
            <CardHeader>
              <CardTitle>Case Study Info</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <label className="text-sm font-medium text-muted-foreground">Status</label>
                <div className="mt-1">
                  <Badge variant={caseStudy.isActive ? "default" : "secondary"}>
                    {caseStudy.isActive ? "Active" : "Inactive"}
                  </Badge>
                </div>
              </div>
              
              <div>
                <label className="text-sm font-medium text-muted-foreground">Practice Area</label>
                <div className="mt-1">
                  {caseStudy.practiceArea ? (
                    <Badge variant="outline">{caseStudy.practiceArea}</Badge>
                  ) : (
                    <span className="text-sm text-muted-foreground">Not specified</span>
                  )}
                </div>
              </div>

              <div>
                <label className="text-sm font-medium text-muted-foreground">Created</label>
                <div className="mt-1 text-sm">
                  {formatDate(caseStudy.createdAt)}
                </div>
              </div>

              <div>
                <label className="text-sm font-medium text-muted-foreground">Last Updated</label>
                <div className="mt-1 text-sm">
                  {formatDate(caseStudy.updatedAt)}
                </div>
              </div>

              <div>
                <label className="text-sm font-medium text-muted-foreground">Sort Order</label>
                <div className="mt-1 text-sm">
                  {caseStudy.sort}
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Quick Actions */}
          <Card>
            <CardHeader>
              <CardTitle>Quick Actions</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <Button variant="outline" className="w-full justify-start">
                <Eye className="h-4 w-4 mr-2" />
                View Public Page
              </Button>
              <Button variant="outline" className="w-full justify-start">
                <Download className="h-4 w-4 mr-2" />
                Download PDF
              </Button>
              <Button variant="outline" className="w-full justify-start">
                <Share2 className="h-4 w-4 mr-2" />
                Share Case Study
              </Button>
              {(isClientAdmin || isClientStaff) && (
                <Button variant="outline" className="w-full justify-start">
                  <Edit className="h-4 w-4 mr-2" />
                  Edit Details
                </Button>
              )}
            </CardContent>
          </Card>

          {/* Related Cases */}
          <Card>
            <CardHeader>
              <CardTitle>Related Cases</CardTitle>
              <CardDescription>
                Other case studies in similar practice areas
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {caseStudies
                  .filter(cs => cs.id !== caseStudy.id && cs.practiceArea === caseStudy.practiceArea)
                  .slice(0, 3)
                  .map(relatedCase => (
                    <div key={relatedCase.id} className="p-3 border rounded-lg hover:bg-muted/50 transition-colors">
                      <Link href={`/client/cases/${relatedCase.id}`}>
                        <h4 className="font-medium text-sm line-clamp-2 hover:text-primary">
                          {relatedCase.title}
                        </h4>
                        <p className="text-xs text-muted-foreground mt-1">
                          {formatDate(relatedCase.createdAt)}
                        </p>
                      </Link>
                    </div>
                  ))}
                {caseStudies.filter(cs => cs.id !== caseStudy.id && cs.practiceArea === caseStudy.practiceArea).length === 0 && (
                  <p className="text-sm text-muted-foreground text-center py-4">
                    No related cases found
                  </p>
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}