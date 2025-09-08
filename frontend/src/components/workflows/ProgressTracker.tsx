'use client';

import React, { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { 
  Target, 
  CheckCircle, 
  Clock, 
  TrendingUp, 
  Award, 
  Calendar,
  Users,
  BarChart3,
  Settings,
  Globe
} from 'lucide-react';

interface Milestone {
  id: string;
  title: string;
  description: string;
  completed: boolean;
  completedAt?: string;
  category: 'onboarding' | 'setup' | 'performance' | 'growth';
  points: number;
}

interface UserProgress {
  totalPoints: number;
  level: string;
  milestones: Milestone[];
  nextMilestone?: Milestone;
  completionRate: number;
}

export default function ProgressTracker() {
  const { user } = useAuth();
  const { clients, leads, campaigns, caseStudies } = useData();
  const [userProgress, setUserProgress] = useState<UserProgress>({
    totalPoints: 0,
    level: 'Beginner',
    milestones: [],
    completionRate: 0
  });

  const milestones: Milestone[] = [
    // Onboarding milestones
    {
      id: 'profile_complete',
      title: 'Complete Profile',
      description: 'Fill out your company information',
      completed: false,
      category: 'onboarding',
      points: 50
    },
    {
      id: 'first_login',
      title: 'First Login',
      description: 'Successfully log into your dashboard',
      completed: false,
      category: 'onboarding',
      points: 25
    },
    {
      id: 'dashboard_tour',
      title: 'Dashboard Tour',
      description: 'Complete the dashboard walkthrough',
      completed: false,
      category: 'onboarding',
      points: 30
    },

    // Setup milestones
    {
      id: 'google_integration',
      title: 'Google Integration',
      description: 'Connect Google Business Profile or Analytics',
      completed: false,
      category: 'setup',
      points: 75
    },
    {
      id: 'first_campaign',
      title: 'First Campaign',
      description: 'Create your first marketing campaign',
      completed: false,
      category: 'setup',
      points: 100
    },
    {
      id: 'goals_set',
      title: 'Set Goals',
      description: 'Define your SEO objectives',
      completed: false,
      category: 'setup',
      points: 50
    },

    // Performance milestones
    {
      id: 'first_lead',
      title: 'First Lead',
      description: 'Receive your first qualified lead',
      completed: false,
      category: 'performance',
      points: 150
    },
    {
      id: 'ten_leads',
      title: '10 Leads',
      description: 'Accumulate 10 qualified leads',
      completed: false,
      category: 'performance',
      points: 200
    },
    {
      id: 'first_conversion',
      title: 'First Conversion',
      description: 'Convert your first lead to a client',
      completed: false,
      category: 'performance',
      points: 300
    },

    // Growth milestones
    {
      id: 'monthly_report',
      title: 'Monthly Report',
      description: 'Generate your first monthly report',
      completed: false,
      category: 'growth',
      points: 100
    },
    {
      id: 'case_study',
      title: 'Case Study',
      description: 'Create your first case study',
      completed: false,
      category: 'growth',
      points: 150
    },
    {
      id: 'team_member',
      title: 'Team Member',
      description: 'Add a team member to your account',
      completed: false,
      category: 'growth',
      points: 75
    }
  ];

  useEffect(() => {
    calculateProgress();
  }, [user, clients, leads, campaigns, caseStudies]);

  const calculateProgress = () => {
    const completedMilestones: Milestone[] = [];
    let totalPoints = 0;

    // Check onboarding milestones
    const profileComplete = clients.length > 0 && clients[0]?.name;
    const firstLogin = user?.lastLoginAt;
    const dashboardTour = localStorage.getItem('dashboard_tour_completed');

    if (profileComplete) {
      completedMilestones.push({ ...milestones[0], completed: true, completedAt: new Date().toISOString() });
      totalPoints += milestones[0].points;
    }

    if (firstLogin) {
      completedMilestones.push({ ...milestones[1], completed: true, completedAt: user.lastLoginAt });
      totalPoints += milestones[1].points;
    }

    if (dashboardTour) {
      completedMilestones.push({ ...milestones[2], completed: true, completedAt: dashboardTour });
      totalPoints += milestones[2].points;
    }

    // Check setup milestones
    const hasGoogleIntegration = clients.length > 0 && (
      clients[0]?.googleBusinessProfile?.profileId || 
      clients[0]?.googleAnalytics?.propertyId
    );
    const hasFirstCampaign = campaigns.length > 0;
    const hasGoalsSet = clients.length > 0 && clients[0]?.metadata?.goals;

    if (hasGoogleIntegration) {
      completedMilestones.push({ ...milestones[3], completed: true, completedAt: new Date().toISOString() });
      totalPoints += milestones[3].points;
    }

    if (hasFirstCampaign) {
      completedMilestones.push({ ...milestones[4], completed: true, completedAt: campaigns[0]?.createdAt });
      totalPoints += milestones[4].points;
    }

    if (hasGoalsSet) {
      completedMilestones.push({ ...milestones[5], completed: true, completedAt: new Date().toISOString() });
      totalPoints += milestones[5].points;
    }

    // Check performance milestones
    const qualifiedLeads = leads.filter(lead => lead.status === 'qualified');
    const hasFirstLead = qualifiedLeads.length > 0;
    const hasTenLeads = qualifiedLeads.length >= 10;

    if (hasFirstLead) {
      completedMilestones.push({ ...milestones[6], completed: true, completedAt: qualifiedLeads[0]?.createdAt });
      totalPoints += milestones[6].points;
    }

    if (hasTenLeads) {
      completedMilestones.push({ ...milestones[7], completed: true, completedAt: qualifiedLeads[9]?.createdAt });
      totalPoints += milestones[7].points;
    }

    // Check growth milestones
    const hasCaseStudy = caseStudies.length > 0;

    if (hasCaseStudy) {
      completedMilestones.push({ ...milestones[10], completed: true, completedAt: caseStudies[0]?.createdAt });
      totalPoints += milestones[10].points;
    }

    // Calculate level
    const level = totalPoints >= 1000 ? 'Expert' : 
                  totalPoints >= 500 ? 'Advanced' : 
                  totalPoints >= 200 ? 'Intermediate' : 'Beginner';

    // Find next milestone
    const nextMilestone = milestones.find(milestone => 
      !completedMilestones.some(completed => completed.id === milestone.id)
    );

    const completionRate = (completedMilestones.length / milestones.length) * 100;

    setUserProgress({
      totalPoints,
      level,
      milestones: completedMilestones,
      nextMilestone,
      completionRate
    });
  };

  const getCategoryIcon = (category: string) => {
    switch (category) {
      case 'onboarding':
        return <Target className="w-4 h-4" />;
      case 'setup':
        return <Settings className="w-4 h-4" />;
      case 'performance':
        return <BarChart3 className="w-4 h-4" />;
      case 'growth':
        return <TrendingUp className="w-4 h-4" />;
      default:
        return <Target className="w-4 h-4" />;
    }
  };

  const getCategoryColor = (category: string) => {
    switch (category) {
      case 'onboarding':
        return 'bg-blue-100 text-blue-800';
      case 'setup':
        return 'bg-green-100 text-green-800';
      case 'performance':
        return 'bg-purple-100 text-purple-800';
      case 'growth':
        return 'bg-orange-100 text-orange-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  return (
    <div className="space-y-6">
      {/* Progress Overview */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Award className="w-5 h-5" />
            Your Progress
          </CardTitle>
          <CardDescription>
            Track your achievements and unlock new features
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="text-center">
              <div className="text-3xl font-bold text-blue-600">{userProgress.totalPoints}</div>
              <div className="text-sm text-muted-foreground">Total Points</div>
            </div>
            <div className="text-center">
              <div className="text-3xl font-bold text-green-600">{userProgress.level}</div>
              <div className="text-sm text-muted-foreground">Current Level</div>
            </div>
            <div className="text-center">
              <div className="text-3xl font-bold text-purple-600">{Math.round(userProgress.completionRate)}%</div>
              <div className="text-sm text-muted-foreground">Completion Rate</div>
            </div>
          </div>
          
          <div className="mt-6">
            <div className="flex items-center justify-between text-sm mb-2">
              <span>Overall Progress</span>
              <span>{Math.round(userProgress.completionRate)}%</span>
            </div>
            <Progress value={userProgress.completionRate} className="h-2" />
          </div>
        </CardContent>
      </Card>

      {/* Next Milestone */}
      {userProgress.nextMilestone && (
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Target className="w-5 h-5" />
              Next Milestone
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-4">
              <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                {getCategoryIcon(userProgress.nextMilestone.category)}
              </div>
              <div className="flex-1">
                <h3 className="font-semibold">{userProgress.nextMilestone.title}</h3>
                <p className="text-sm text-muted-foreground">{userProgress.nextMilestone.description}</p>
              </div>
              <Badge variant="outline">{userProgress.nextMilestone.points} pts</Badge>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Completed Milestones */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <CheckCircle className="w-5 h-5" />
            Completed Milestones
          </CardTitle>
          <CardDescription>
            {userProgress.milestones.length} of {milestones.length} milestones completed
          </CardDescription>
        </CardHeader>
        <CardContent>
          {userProgress.milestones.length === 0 ? (
            <div className="text-center py-8 text-muted-foreground">
              <Target className="w-12 h-12 mx-auto mb-4 opacity-50" />
              <p>No milestones completed yet. Start by completing your profile!</p>
            </div>
          ) : (
            <div className="space-y-4">
              {userProgress.milestones.map((milestone) => (
                <div key={milestone.id} className="flex items-center gap-4 p-4 border rounded-lg">
                  <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <CheckCircle className="w-5 h-5 text-green-600" />
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-1">
                      <h3 className="font-semibold">{milestone.title}</h3>
                      <Badge className={getCategoryColor(milestone.category)}>
                        {milestone.category}
                      </Badge>
                    </div>
                    <p className="text-sm text-muted-foreground">{milestone.description}</p>
                    {milestone.completedAt && (
                      <p className="text-xs text-muted-foreground mt-1">
                        Completed on {formatDate(milestone.completedAt)}
                      </p>
                    )}
                  </div>
                  <Badge variant="outline">{milestone.points} pts</Badge>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      {/* All Milestones */}
      <Card>
        <CardHeader>
          <CardTitle>All Milestones</CardTitle>
          <CardDescription>
            Complete milestones to earn points and unlock features
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {milestones.map((milestone) => {
              const isCompleted = userProgress.milestones.some(m => m.id === milestone.id);
              return (
                <div key={milestone.id} className={`flex items-center gap-4 p-3 rounded-lg ${
                  isCompleted ? 'bg-green-50 border border-green-200' : 'bg-gray-50'
                }`}>
                  <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
                    isCompleted ? 'bg-green-100' : 'bg-gray-100'
                  }`}>
                    {isCompleted ? (
                      <CheckCircle className="w-4 h-4 text-green-600" />
                    ) : (
                      <Clock className="w-4 h-4 text-gray-400" />
                    )}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center gap-2">
                      <h4 className={`font-medium ${isCompleted ? 'text-green-900' : 'text-gray-900'}`}>
                        {milestone.title}
                      </h4>
                      <Badge className={getCategoryColor(milestone.category)}>
                        {milestone.category}
                      </Badge>
                    </div>
                    <p className={`text-sm ${isCompleted ? 'text-green-700' : 'text-gray-600'}`}>
                      {milestone.description}
                    </p>
                  </div>
                  <Badge variant="outline">{milestone.points} pts</Badge>
                </div>
              );
            })}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
