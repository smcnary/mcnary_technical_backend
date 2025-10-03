'use client';

import React, { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Textarea } from '../ui/textarea';
import { Badge } from '../ui/badge';
import { Progress } from '../ui/progress';
import { 
  Search, 
  Globe, 
  CheckCircle, 
  AlertCircle, 
  ArrowRight, 
  ArrowLeft,
  Loader2,
  FileText,
  Eye,
  BarChart3
} from 'lucide-react';

interface AuditStep {
  id: string;
  title: string;
  description: string;
  completed: boolean;
}

interface AuditResult {
  url: string;
  score: number;
  issues: string[];
  recommendations: string[];
  status: 'completed' | 'in-progress' | 'error';
}

export default function AuditWizard() {
  const [currentStep, setCurrentStep] = useState(0);
  const [websiteUrl, setWebsiteUrl] = useState('');
  const [isAuditing, setIsAuditing] = useState(false);
  const [auditResult, setAuditResult] = useState<AuditResult | null>(null);
  const [error, setError] = useState('');

  const steps: AuditStep[] = [
    {
      id: 'url',
      title: 'Enter Website URL',
      description: 'Provide the website URL you want to audit',
      completed: false
    },
    {
      id: 'analysis',
      title: 'SEO Analysis',
      description: 'Analyzing your website for SEO issues',
      completed: false
    },
    {
      id: 'results',
      title: 'View Results',
      description: 'Review your audit results and recommendations',
      completed: false
    }
  ];

  const progress = ((currentStep + 1) / steps.length) * 100;

  const handleNext = () => {
    if (currentStep < steps.length - 1) {
      setCurrentStep(currentStep + 1);
    }
  };

  const handlePrevious = () => {
    if (currentStep > 0) {
      setCurrentStep(currentStep - 1);
    }
  };

  const handleStartAudit = async () => {
    if (!websiteUrl.trim()) {
      setError('Please enter a valid website URL');
      return;
    }

    setIsAuditing(true);
    setError('');
    setAuditResult(null);

    try {
      // Simulate audit process
      await new Promise(resolve => setTimeout(resolve, 3000));
      
      // Mock audit results
      const mockResult: AuditResult = {
        url: websiteUrl,
        score: Math.floor(Math.random() * 40) + 60, // Random score between 60-100
        issues: [
          'Missing meta description',
          'Images without alt text',
          'Slow page load speed',
          'Missing structured data',
          'Poor mobile responsiveness'
        ],
        recommendations: [
          'Add compelling meta descriptions to all pages',
          'Include alt text for all images',
          'Optimize images and enable compression',
          'Implement structured data markup',
          'Improve mobile user experience'
        ],
        status: 'completed'
      };

      setAuditResult(mockResult);
      setCurrentStep(2);
    } catch (err) {
      setError('Failed to complete audit. Please try again.');
    } finally {
      setIsAuditing(false);
    }
  };

  const getScoreColor = (score: number) => {
    if (score >= 90) return 'text-green-600';
    if (score >= 70) return 'text-yellow-600';
    return 'text-red-600';
  };

  const getScoreBadgeVariant = (score: number) => {
    if (score >= 90) return 'default';
    if (score >= 70) return 'secondary';
    return 'destructive';
  };

  const renderStepContent = () => {
    switch (currentStep) {
      case 0:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <Globe className="h-16 w-16 mx-auto text-blue-600 mb-4" />
              <h2 className="text-2xl font-bold mb-2">Website SEO Audit</h2>
              <p className="text-gray-600 dark:text-gray-400">
                Enter your website URL to get a comprehensive SEO analysis
              </p>
            </div>

            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="websiteUrl">Website URL</Label>
                <Input
                  id="websiteUrl"
                  type="url"
                  value={websiteUrl}
                  onChange={(e) => setWebsiteUrl(e.target.value)}
                  placeholder="https://example.com"
                  className="text-lg"
                />
              </div>

              {error && (
                <div className="flex items-center gap-2 text-red-600">
                  <AlertCircle className="h-4 w-4" />
                  <span className="text-sm">{error}</span>
                </div>
              )}
            </div>

            <div className="flex justify-end">
              <Button 
                onClick={handleStartAudit}
                disabled={!websiteUrl.trim() || isAuditing}
                className="min-w-[120px]"
              >
                {isAuditing ? (
                  <>
                    <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                    Auditing...
                  </>
                ) : (
                  <>
                    Start Audit
                    <ArrowRight className="h-4 w-4 ml-2" />
                  </>
                )}
              </Button>
            </div>
          </div>
        );

      case 1:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <Search className="h-16 w-16 mx-auto text-blue-600 mb-4" />
              <h2 className="text-2xl font-bold mb-2">Analyzing Your Website</h2>
              <p className="text-gray-600 dark:text-gray-400">
                We're examining your website for SEO opportunities and issues
              </p>
            </div>

            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-sm font-medium">Progress</span>
                <span className="text-sm text-gray-500">Analyzing...</span>
              </div>
              <Progress value={75} className="h-2" />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="text-center p-4 border rounded-lg">
                <FileText className="h-8 w-8 mx-auto text-blue-600 mb-2" />
                <h3 className="font-semibold">Content Analysis</h3>
                <p className="text-sm text-gray-500">Checking page content</p>
              </div>
              <div className="text-center p-4 border rounded-lg">
                <Eye className="h-8 w-8 mx-auto text-green-600 mb-2" />
                <h3 className="font-semibold">Technical SEO</h3>
                <p className="text-sm text-gray-500">Reviewing technical aspects</p>
              </div>
              <div className="text-center p-4 border rounded-lg">
                <BarChart3 className="h-8 w-8 mx-auto text-purple-600 mb-2" />
                <h3 className="font-semibold">Performance</h3>
                <p className="text-sm text-gray-500">Measuring site speed</p>
              </div>
            </div>
          </div>
        );

      case 2:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <CheckCircle className="h-16 w-16 mx-auto text-green-600 mb-4" />
              <h2 className="text-2xl font-bold mb-2">Audit Complete</h2>
              <p className="text-gray-600 dark:text-gray-400">
                Here are your SEO audit results and recommendations
              </p>
            </div>

            {auditResult && (
              <div className="space-y-6">
                {/* Score Overview */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                      <BarChart3 className="h-5 w-5" />
                      Overall Score
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="text-center">
                      <div className={`text-4xl font-bold ${getScoreColor(auditResult.score)}`}>
                        {auditResult.score}
                      </div>
                      <Badge variant={getScoreBadgeVariant(auditResult.score)} className="mt-2">
                        {auditResult.score >= 90 ? 'Excellent' : 
                         auditResult.score >= 70 ? 'Good' : 'Needs Improvement'}
                      </Badge>
                      <p className="text-sm text-gray-500 mt-2">
                        {auditResult.url}
                      </p>
                    </div>
                  </CardContent>
                </Card>

                {/* Issues */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                      <AlertCircle className="h-5 w-5 text-red-600" />
                      Issues Found ({auditResult.issues.length})
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      {auditResult.issues.map((issue, index) => (
                        <div key={index} className="flex items-start gap-2 p-2 bg-red-50 dark:bg-red-950/20 rounded">
                          <AlertCircle className="h-4 w-4 text-red-600 mt-0.5 flex-shrink-0" />
                          <span className="text-sm">{issue}</span>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>

                {/* Recommendations */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                      <CheckCircle className="h-5 w-5 text-green-600" />
                      Recommendations ({auditResult.recommendations.length})
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      {auditResult.recommendations.map((recommendation, index) => (
                        <div key={index} className="flex items-start gap-2 p-2 bg-green-50 dark:bg-green-950/20 rounded">
                          <CheckCircle className="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" />
                          <span className="text-sm">{recommendation}</span>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              </div>
            )}

            <div className="flex justify-between">
              <Button variant="outline" onClick={handlePrevious}>
                <ArrowLeft className="h-4 w-4 mr-2" />
                Back
              </Button>
              <Button onClick={() => {
                setCurrentStep(0);
                setWebsiteUrl('');
                setAuditResult(null);
                setError('');
              }}>
                New Audit
              </Button>
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Card>
        <CardHeader>
          <div className="space-y-4">
            <CardTitle className="text-2xl">SEO Audit Wizard</CardTitle>
            
            {/* Progress Bar */}
            <div className="space-y-2">
              <div className="flex justify-between text-sm">
                <span>Step {currentStep + 1} of {steps.length}</span>
                <span>{Math.round(progress)}% Complete</span>
              </div>
              <Progress value={progress} className="h-2" />
            </div>

            {/* Step Indicators */}
            <div className="flex justify-between">
              {steps.map((step, index) => (
                <div
                  key={step.id}
                  className={`flex flex-col items-center space-y-2 ${
                    index <= currentStep ? 'text-blue-600' : 'text-gray-400'
                  }`}
                >
                  <div
                    className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium ${
                      index < currentStep
                        ? 'bg-green-600 text-white'
                        : index === currentStep
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 dark:bg-gray-700'
                    }`}
                  >
                    {index < currentStep ? (
                      <CheckCircle className="h-4 w-4" />
                    ) : (
                      index + 1
                    )}
                  </div>
                  <div className="text-center">
                    <div className="text-xs font-medium">{step.title}</div>
                    <div className="text-xs text-gray-500 max-w-20">
                      {step.description}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </CardHeader>

        <CardContent className="pt-6">
          {renderStepContent()}
        </CardContent>
      </Card>
    </div>
  );
}
