'use client';

import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { useAuth } from '@/hooks/useAuth';

export interface OnboardingStep {
  id: string;
  title: string;
  description: string;
  completed: boolean;
  required: boolean;
  component?: ReactNode;
}

export interface OnboardingData {
  steps: OnboardingStep[];
  currentStep: number;
  isCompleted: boolean;
  isSkipped: boolean;
  progress: number;
}

interface OnboardingContextType {
  onboardingData: OnboardingData;
  updateStep: (stepId: string, completed: boolean) => void;
  nextStep: () => void;
  previousStep: () => void;
  skipOnboarding: () => void;
  completeOnboarding: () => void;
  resetOnboarding: () => void;
  isOnboardingActive: boolean;
}

const OnboardingContext = createContext<OnboardingContextType | undefined>(undefined);

const defaultSteps: OnboardingStep[] = [
  {
    id: 'welcome',
    title: 'Welcome to CounselRank',
    description: 'Let\'s get you set up with your SEO management platform',
    completed: false,
    required: true
  },
  {
    id: 'profile',
    title: 'Complete Your Profile',
    description: 'Add your company information and contact details',
    completed: false,
    required: true
  },
  {
    id: 'integrations',
    title: 'Connect Your Tools',
    description: 'Link your Google Business Profile and Analytics',
    completed: false,
    required: false
  },
  {
    id: 'goals',
    title: 'Set Your Goals',
    description: 'Tell us what you want to achieve with SEO',
    completed: false,
    required: true
  },
  {
    id: 'dashboard',
    title: 'Explore Your Dashboard',
    description: 'Take a quick tour of your new dashboard',
    completed: false,
    required: false
  }
];

export function OnboardingProvider({ children }: { children: ReactNode }) {
  const { user, isAuthenticated } = useAuth();
  const [onboardingData, setOnboardingData] = useState<OnboardingData>({
    steps: defaultSteps,
    currentStep: 0,
    isCompleted: false,
    isSkipped: false,
    progress: 0
  });

  const [isOnboardingActive, setIsOnboardingActive] = useState(false);

  // Check if user needs onboarding
  useEffect(() => {
    if (isAuthenticated && user) {
      // Check if user has completed onboarding
      const hasCompletedOnboarding = localStorage.getItem('onboarding_completed');
      const hasSkippedOnboarding = localStorage.getItem('onboarding_skipped');
      
      if (!hasCompletedOnboarding && !hasSkippedOnboarding) {
        // Check if user is new (created within last 7 days)
        const userCreatedAt = new Date(user.createdAt || '');
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
        
        if (userCreatedAt > sevenDaysAgo) {
          setIsOnboardingActive(true);
        }
      }
    }
  }, [isAuthenticated, user]);

  const updateStep = (stepId: string, completed: boolean) => {
    setOnboardingData(prev => {
      const updatedSteps = prev.steps.map(step => 
        step.id === stepId ? { ...step, completed } : step
      );
      
      const completedSteps = updatedSteps.filter(step => step.completed).length;
      const progress = (completedSteps / updatedSteps.length) * 100;
      
      return {
        ...prev,
        steps: updatedSteps,
        progress
      };
    });
  };

  const nextStep = () => {
    setOnboardingData(prev => ({
      ...prev,
      currentStep: Math.min(prev.currentStep + 1, prev.steps.length - 1)
    }));
  };

  const previousStep = () => {
    setOnboardingData(prev => ({
      ...prev,
      currentStep: Math.max(prev.currentStep - 1, 0)
    }));
  };

  const skipOnboarding = () => {
    setOnboardingData(prev => ({
      ...prev,
      isSkipped: true,
      isCompleted: false
    }));
    localStorage.setItem('onboarding_skipped', 'true');
    setIsOnboardingActive(false);
  };

  const completeOnboarding = () => {
    setOnboardingData(prev => ({
      ...prev,
      isCompleted: true,
      isSkipped: false
    }));
    localStorage.setItem('onboarding_completed', 'true');
    setIsOnboardingActive(false);
  };

  const resetOnboarding = () => {
    setOnboardingData({
      steps: defaultSteps,
      currentStep: 0,
      isCompleted: false,
      isSkipped: false,
      progress: 0
    });
    localStorage.removeItem('onboarding_completed');
    localStorage.removeItem('onboarding_skipped');
    setIsOnboardingActive(true);
  };

  const value: OnboardingContextType = {
    onboardingData,
    updateStep,
    nextStep,
    previousStep,
    skipOnboarding,
    completeOnboarding,
    resetOnboarding,
    isOnboardingActive
  };

  return (
    <OnboardingContext.Provider value={value}>
      {children}
    </OnboardingContext.Provider>
  );
}

export function useOnboarding() {
  const context = useContext(OnboardingContext);
  if (context === undefined) {
    throw new Error('useOnboarding must be used within an OnboardingProvider');
  }
  return context;
}
