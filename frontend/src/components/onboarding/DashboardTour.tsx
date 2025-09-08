'use client';

import React, { useState, useEffect, useRef } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
  X, 
  ChevronLeft, 
  ChevronRight, 
  Eye, 
  BarChart3, 
  Users, 
  Target, 
  Settings,
  CheckCircle,
  ArrowRight,
  ArrowDown,
  ArrowUp
} from 'lucide-react';

interface TourStep {
  id: string;
  title: string;
  description: string;
  target: string;
  position: 'top' | 'bottom' | 'left' | 'right';
  arrow?: 'up' | 'down' | 'left' | 'right';
}

interface DashboardTourProps {
  isOpen: boolean;
  onClose: () => void;
  onComplete: () => void;
}

const tourSteps: TourStep[] = [
  {
    id: 'welcome',
    title: 'Welcome to Your Dashboard!',
    description: 'This is your command center for managing your law firm\'s SEO performance.',
    target: 'dashboard-header',
    position: 'bottom',
    arrow: 'up'
  },
  {
    id: 'kpis',
    title: 'Key Performance Indicators',
    description: 'Track your most important metrics at a glance - visibility, views, calls, and leads.',
    target: 'kpi-cards',
    position: 'bottom',
    arrow: 'up'
  },
  {
    id: 'charts',
    title: 'Performance Trends',
    description: 'View your performance over time with interactive charts and data visualizations.',
    target: 'performance-chart',
    position: 'left',
    arrow: 'right'
  },
  {
    id: 'activity',
    title: 'Recent Activity',
    description: 'Stay updated with your latest performance updates and important notifications.',
    target: 'activity-feed',
    position: 'left',
    arrow: 'right'
  },
  {
    id: 'leads',
    title: 'Lead Management',
    description: 'View and manage your incoming leads from all your marketing channels.',
    target: 'leads-table',
    position: 'top',
    arrow: 'down'
  },
  {
    id: 'navigation',
    title: 'Navigation Menu',
    description: 'Access all your tools - campaigns, cases, billing, and settings.',
    target: 'sidebar-nav',
    position: 'right',
    arrow: 'left'
  }
];

export default function DashboardTour({ isOpen, onClose, onComplete }: DashboardTourProps) {
  const [currentStep, setCurrentStep] = useState(0);
  const [targetElement, setTargetElement] = useState<HTMLElement | null>(null);
  const [tooltipPosition, setTooltipPosition] = useState({ top: 0, left: 0 });
  const overlayRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (isOpen) {
      updateTargetElement();
    }
  }, [isOpen, currentStep]);

  useEffect(() => {
    if (targetElement) {
      updateTooltipPosition();
    }
  }, [targetElement]);

  const updateTargetElement = () => {
    const step = tourSteps[currentStep];
    const element = document.querySelector(`[data-tour="${step.target}"]`) as HTMLElement;
    setTargetElement(element);
  };

  const updateTooltipPosition = () => {
    if (!targetElement) return;

    const rect = targetElement.getBoundingClientRect();
    const step = tourSteps[currentStep];
    
    let top = 0;
    let left = 0;

    switch (step.position) {
      case 'top':
        top = rect.top - 20;
        left = rect.left + rect.width / 2;
        break;
      case 'bottom':
        top = rect.bottom + 20;
        left = rect.left + rect.width / 2;
        break;
      case 'left':
        top = rect.top + rect.height / 2;
        left = rect.left - 20;
        break;
      case 'right':
        top = rect.top + rect.height / 2;
        left = rect.right + 20;
        break;
    }

    setTooltipPosition({ top, left });
  };

  const nextStep = () => {
    if (currentStep < tourSteps.length - 1) {
      setCurrentStep(currentStep + 1);
    } else {
      onComplete();
    }
  };

  const previousStep = () => {
    if (currentStep > 0) {
      setCurrentStep(currentStep - 1);
    }
  };

  const skipTour = () => {
    onComplete();
  };

  const getArrowIcon = (direction?: string) => {
    switch (direction) {
      case 'up':
        return <ArrowUp className="w-4 h-4" />;
      case 'down':
        return <ArrowDown className="w-4 h-4" />;
      case 'left':
        return <ChevronLeft className="w-4 h-4" />;
      case 'right':
        return <ArrowRight className="w-4 h-4" />;
      default:
        return null;
    }
  };

  if (!isOpen) return null;

  const currentStepData = tourSteps[currentStep];

  return (
    <>
      {/* Overlay */}
      <div 
        ref={overlayRef}
        className="fixed inset-0 bg-black bg-opacity-50 z-40"
        onClick={skipTour}
      />
      
      {/* Highlighted Element */}
      {targetElement && (
        <div
          className="fixed z-45 border-2 border-blue-500 rounded-lg pointer-events-none"
          style={{
            top: targetElement.getBoundingClientRect().top - 4,
            left: targetElement.getBoundingClientRect().left - 4,
            width: targetElement.getBoundingClientRect().width + 8,
            height: targetElement.getBoundingClientRect().height + 8,
            boxShadow: '0 0 0 9999px rgba(0, 0, 0, 0.5)'
          }}
        />
      )}

      {/* Tooltip */}
      <div
        className="fixed z-50 max-w-sm"
        style={{
          top: tooltipPosition.top,
          left: tooltipPosition.left,
          transform: currentStepData.position === 'left' ? 'translateX(-100%)' : 
                    currentStepData.position === 'right' ? 'translateX(0)' :
                    currentStepData.position === 'top' ? 'translate(-50%, -100%)' : 
                    'translate(-50%, 0)'
        }}
      >
        <Card className="relative">
          <CardHeader className="pb-3">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <Badge variant="outline">
                  {currentStep + 1} of {tourSteps.length}
                </Badge>
                {getArrowIcon(currentStepData.arrow)}
              </div>
              <Button variant="ghost" size="sm" onClick={skipTour}>
                <X className="w-4 h-4" />
              </Button>
            </div>
            <CardTitle className="text-lg">{currentStepData.title}</CardTitle>
            <CardDescription>{currentStepData.description}</CardDescription>
          </CardHeader>

          <CardContent className="pt-0">
            <div className="flex items-center justify-between">
              <Button
                variant="outline"
                onClick={previousStep}
                disabled={currentStep === 0}
              >
                <ChevronLeft className="w-4 h-4 mr-2" />
                Previous
              </Button>

              <div className="flex items-center gap-2">
                {currentStep === tourSteps.length - 1 ? (
                  <Button onClick={onComplete}>
                    <CheckCircle className="w-4 h-4 mr-2" />
                    Complete Tour
                  </Button>
                ) : (
                  <Button onClick={nextStep}>
                    Next
                    <ChevronRight className="w-4 h-4 ml-2" />
                  </Button>
                )}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </>
  );
}
