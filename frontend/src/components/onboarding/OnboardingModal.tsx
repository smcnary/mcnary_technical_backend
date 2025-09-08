'use client';

import React, { useState } from 'react';
import { useOnboarding } from '@/contexts/OnboardingContext';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { 
  X, 
  ChevronLeft, 
  ChevronRight, 
  CheckCircle, 
  Circle, 
  Building2, 
  Globe, 
  Target, 
  Eye,
  Loader2,
  AlertCircle
} from 'lucide-react';

interface OnboardingModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function OnboardingModal({ isOpen, onClose }: OnboardingModalProps) {
  const { onboardingData, updateStep, nextStep, previousStep, skipOnboarding, completeOnboarding } = useOnboarding();
  const { user, updateClient } = useAuth();
  const { updateClient: updateClientData } = useData();
  
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [formData, setFormData] = useState({
    companyName: user?.clientId ? '' : '',
    website: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zipCode: '',
    description: '',
    goals: [] as string[],
    googleBusinessProfile: '',
    googleAnalytics: ''
  });

  const availableGoals = [
    'Increase website traffic',
    'Generate more leads',
    'Improve local search rankings',
    'Build brand awareness',
    'Increase phone calls',
    'Get more online reviews'
  ];

  const handleGoalToggle = (goal: string) => {
    setFormData(prev => ({
      ...prev,
      goals: prev.goals.includes(goal)
        ? prev.goals.filter(g => g !== goal)
        : [...prev.goals, goal]
    }));
  };

  const handleSave = async () => {
    if (!user?.clientId) return;

    setIsLoading(true);
    setError(null);

    try {
      await updateClientData(user.clientId, {
        name: formData.companyName,
        website: formData.website,
        phone: formData.phone,
        address: formData.address,
        city: formData.city,
        state: formData.state,
        zipCode: formData.zipCode,
        description: formData.description,
        metadata: {
          goals: formData.goals,
          onboarding_completed: true
        }
      });

      updateStep(onboardingData.steps[onboardingData.currentStep].id, true);
      nextStep();
    } catch (err: any) {
      setError(err?.message || 'Failed to save information');
    } finally {
      setIsLoading(false);
    }
  };

  const handleSkip = () => {
    updateStep(onboardingData.steps[onboardingData.currentStep].id, true);
    nextStep();
  };

  const handleComplete = () => {
    completeOnboarding();
    onClose();
  };

  const renderStepContent = () => {
    const currentStep = onboardingData.steps[onboardingData.currentStep];
    
    switch (currentStep.id) {
      case 'welcome':
        return (
          <div className="text-center space-y-6">
            <div className="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
              <Building2 className="w-8 h-8 text-blue-600" />
            </div>
            <div>
              <h3 className="text-2xl font-bold text-gray-900">Welcome to CounselRank!</h3>
              <p className="text-gray-600 mt-2">
                We're excited to help you grow your law firm's online presence. 
                Let's get you set up in just a few minutes.
              </p>
            </div>
            <div className="bg-blue-50 rounded-lg p-4">
              <h4 className="font-semibold text-blue-900">What we'll cover:</h4>
              <ul className="text-sm text-blue-800 mt-2 space-y-1">
                <li>• Complete your company profile</li>
                <li>• Connect your Google tools</li>
                <li>• Set your SEO goals</li>
                <li>• Tour your dashboard</li>
              </ul>
            </div>
          </div>
        );

      case 'profile':
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h3 className="text-xl font-bold text-gray-900">Company Information</h3>
              <p className="text-gray-600 mt-1">Tell us about your law firm</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="companyName">Company Name *</Label>
                <Input
                  id="companyName"
                  value={formData.companyName}
                  onChange={(e) => setFormData(prev => ({ ...prev, companyName: e.target.value }))}
                  placeholder="Your Law Firm Name"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="website">Website</Label>
                <Input
                  id="website"
                  value={formData.website}
                  onChange={(e) => setFormData(prev => ({ ...prev, website: e.target.value }))}
                  placeholder="https://yourlawfirm.com"
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="phone">Phone Number</Label>
              <Input
                id="phone"
                value={formData.phone}
                onChange={(e) => setFormData(prev => ({ ...prev, phone: e.target.value }))}
                placeholder="(555) 123-4567"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">Company Description</Label>
              <Textarea
                id="description"
                value={formData.description}
                onChange={(e) => setFormData(prev => ({ ...prev, description: e.target.value }))}
                placeholder="Brief description of your law firm and practice areas..."
                rows={3}
              />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="space-y-2">
                <Label htmlFor="city">City</Label>
                <Input
                  id="city"
                  value={formData.city}
                  onChange={(e) => setFormData(prev => ({ ...prev, city: e.target.value }))}
                  placeholder="City"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="state">State</Label>
                <Input
                  id="state"
                  value={formData.state}
                  onChange={(e) => setFormData(prev => ({ ...prev, state: e.target.value }))}
                  placeholder="State"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="zipCode">ZIP Code</Label>
                <Input
                  id="zipCode"
                  value={formData.zipCode}
                  onChange={(e) => setFormData(prev => ({ ...prev, zipCode: e.target.value }))}
                  placeholder="12345"
                />
              </div>
            </div>
          </div>
        );

      case 'integrations':
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h3 className="text-xl font-bold text-gray-900">Connect Your Tools</h3>
              <p className="text-gray-600 mt-1">Link your Google services for better insights</p>
            </div>

            <div className="space-y-4">
              <Card>
                <CardHeader className="pb-3">
                  <CardTitle className="text-lg flex items-center gap-2">
                    <Globe className="w-5 h-5 text-green-600" />
                    Google Business Profile
                  </CardTitle>
                  <CardDescription>
                    Connect your Google Business Profile to track local search performance
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    <Label htmlFor="gbp">Business Profile ID</Label>
                    <Input
                      id="gbp"
                      value={formData.googleBusinessProfile}
                      onChange={(e) => setFormData(prev => ({ ...prev, googleBusinessProfile: e.target.value }))}
                      placeholder="gcid:123456789"
                    />
                    <p className="text-xs text-gray-500">
                      Find this in your Google Business Profile settings
                    </p>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader className="pb-3">
                  <CardTitle className="text-lg flex items-center gap-2">
                    <Target className="w-5 h-5 text-blue-600" />
                    Google Analytics
                  </CardTitle>
                  <CardDescription>
                    Connect Analytics to track website traffic and conversions
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    <Label htmlFor="ga">Analytics Property ID</Label>
                    <Input
                      id="ga"
                      value={formData.googleAnalytics}
                      onChange={(e) => setFormData(prev => ({ ...prev, googleAnalytics: e.target.value }))}
                      placeholder="GA4-123456789"
                    />
                    <p className="text-xs text-gray-500">
                      You can add this later in your settings
                    </p>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        );

      case 'goals':
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h3 className="text-xl font-bold text-gray-900">What are your goals?</h3>
              <p className="text-gray-600 mt-1">Select what you want to achieve with SEO</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              {availableGoals.map((goal) => (
                <button
                  key={goal}
                  onClick={() => handleGoalToggle(goal)}
                  className={`p-4 rounded-lg border-2 text-left transition-all ${
                    formData.goals.includes(goal)
                      ? 'border-blue-500 bg-blue-50 text-blue-900'
                      : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  <div className="flex items-center gap-3">
                    {formData.goals.includes(goal) ? (
                      <CheckCircle className="w-5 h-5 text-blue-600" />
                    ) : (
                      <Circle className="w-5 h-5 text-gray-400" />
                    )}
                    <span className="font-medium">{goal}</span>
                  </div>
                </button>
              ))}
            </div>

            {formData.goals.length > 0 && (
              <div className="bg-green-50 rounded-lg p-4">
                <h4 className="font-semibold text-green-900">Selected Goals:</h4>
                <div className="flex flex-wrap gap-2 mt-2">
                  {formData.goals.map((goal) => (
                    <Badge key={goal} variant="default" className="bg-green-600">
                      {goal}
                    </Badge>
                  ))}
                </div>
              </div>
            )}
          </div>
        );

      case 'dashboard':
        return (
          <div className="text-center space-y-6">
            <div className="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
              <Eye className="w-8 h-8 text-green-600" />
            </div>
            <div>
              <h3 className="text-2xl font-bold text-gray-900">Ready to explore!</h3>
              <p className="text-gray-600 mt-2">
                Your dashboard is ready. Let's take a quick tour to show you around.
              </p>
            </div>
            <div className="bg-green-50 rounded-lg p-4">
              <h4 className="font-semibold text-green-900">What's next:</h4>
              <ul className="text-sm text-green-800 mt-2 space-y-1">
                <li>• View your performance metrics</li>
                <li>• Manage your leads</li>
                <li>• Track your campaigns</li>
                <li>• Access your reports</li>
              </ul>
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <Card className="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
          <div>
            <CardTitle>Getting Started</CardTitle>
            <CardDescription>
              Step {onboardingData.currentStep + 1} of {onboardingData.steps.length}
            </CardDescription>
          </div>
          <Button variant="ghost" size="sm" onClick={skipOnboarding}>
            <X className="w-4 h-4" />
          </Button>
        </CardHeader>

        <CardContent className="space-y-6">
          {/* Progress Bar */}
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div 
              className="bg-blue-600 h-2 rounded-full transition-all duration-300"
              style={{ width: `${onboardingData.progress}%` }}
            />
          </div>

          {/* Step Content */}
          {renderStepContent()}

          {/* Error Message */}
          {error && (
            <div className="flex items-center gap-2 text-red-600 bg-red-50 p-3 rounded-lg">
              <AlertCircle className="w-4 h-4" />
              {error}
            </div>
          )}

          {/* Navigation */}
          <div className="flex items-center justify-between pt-4 border-t">
            <Button
              variant="outline"
              onClick={previousStep}
              disabled={onboardingData.currentStep === 0}
            >
              <ChevronLeft className="w-4 h-4 mr-2" />
              Previous
            </Button>

            <div className="flex items-center gap-2">
              {onboardingData.currentStep === onboardingData.steps.length - 1 ? (
                <Button onClick={handleComplete} disabled={isLoading}>
                  {isLoading ? (
                    <>
                      <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                      Completing...
                    </>
                  ) : (
                    'Complete Setup'
                  )}
                </Button>
              ) : (
                <>
                  <Button variant="outline" onClick={handleSkip}>
                    Skip
                  </Button>
                  <Button 
                    onClick={handleSave} 
                    disabled={isLoading || (onboardingData.steps[onboardingData.currentStep].id === 'profile' && !formData.companyName)}
                  >
                    {isLoading ? (
                      <>
                        <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                        Saving...
                      </>
                    ) : (
                      <>
                        Next
                        <ChevronRight className="w-4 h-4 ml-2" />
                      </>
                    )}
                  </Button>
                </>
              )}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
