'use client';

import React from 'react';
import { Badge } from '../ui/badge';
import { Button } from '../ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { 
  Monitor, 
  Code, 
  Database, 
  Globe, 
  Shield, 
  Zap,
  ExternalLink,
  RefreshCw,
  AlertCircle,
  CheckCircle
} from 'lucide-react';
import { Technology, TechStackResult } from '../../services/techStackService';

interface TechStackDisplayProps {
  techStack?: TechStackResult;
  isLoading?: boolean;
  onAnalyze?: () => void;
  website?: string;
}

const categoryIcons: Record<string, React.ReactNode> = {
  'Web Servers': <Monitor className="h-4 w-4" />,
  'Programming Languages': <Code className="h-4 w-4" />,
  'Databases': <Database className="h-4 w-4" />,
  'CDN': <Globe className="h-4 w-4" />,
  'Security': <Shield className="h-4 w-4" />,
  'Analytics': <Zap className="h-4 w-4" />,
  'CMS': <Monitor className="h-4 w-4" />,
  'E-commerce': <Globe className="h-4 w-4" />,
  'JavaScript Frameworks': <Code className="h-4 w-4" />,
  'Web Frameworks': <Code className="h-4 w-4" />,
  'Operating Systems': <Monitor className="h-4 w-4" />,
  'Web Hosting': <Globe className="h-4 w-4" />,
};

const getCategoryIcon = (category: string): React.ReactNode => {
  return categoryIcons[category] || <Code className="h-4 w-4" />;
};

const getConfidenceColor = (confidence: number): string => {
  if (confidence >= 80) return 'bg-green-100 text-green-800 border-green-200';
  if (confidence >= 60) return 'bg-yellow-100 text-yellow-800 border-yellow-200';
  return 'bg-red-100 text-red-800 border-red-200';
};

const TechnologyCard: React.FC<{ technology: Technology }> = ({ technology }) => {
  return (
    <div className="flex items-center justify-between p-3 border rounded-lg bg-white">
      <div className="flex items-center gap-3">
        <div className="flex items-center gap-2">
          {technology.categories.map((category, index) => (
            <div key={index} className="flex items-center gap-1 text-gray-500">
              {getCategoryIcon(category)}
            </div>
          ))}
        </div>
        <div>
          <div className="font-medium text-sm">{technology.name}</div>
          {technology.version && (
            <div className="text-xs text-gray-500">v{technology.version}</div>
          )}
          <div className="flex flex-wrap gap-1 mt-1">
            {technology.categories.map((category, index) => (
              <Badge key={index} variant="outline" className="text-xs">
                {category}
              </Badge>
            ))}
          </div>
        </div>
      </div>
      <div className="flex items-center gap-2">
        <Badge 
          variant="outline" 
          className={`text-xs ${getConfidenceColor(technology.confidence)}`}
        >
          {technology.confidence}%
        </Badge>
        {technology.website && (
          <Button
            variant="ghost"
            size="sm"
            asChild
            className="h-6 w-6 p-0"
          >
            <a 
              href={technology.website} 
              target="_blank" 
              rel="noopener noreferrer"
              title="Learn more about this technology"
            >
              <ExternalLink className="h-3 w-3" />
            </a>
          </Button>
        )}
      </div>
    </div>
  );
};

export default function TechStackDisplay({ 
  techStack, 
  isLoading, 
  onAnalyze, 
  website 
}: TechStackDisplayProps) {
  if (!website) {
    return null;
  }

  if (isLoading) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Monitor className="h-5 w-5" />
            Technology Stack
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-center py-8">
            <RefreshCw className="h-6 w-6 animate-spin text-gray-500" />
            <span className="ml-2 text-gray-500">Analyzing website...</span>
          </div>
        </CardContent>
      </Card>
    );
  }

  if (!techStack) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Monitor className="h-5 w-5" />
            Technology Stack
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-center py-6">
            <Monitor className="h-12 w-12 text-gray-300 mx-auto mb-4" />
            <p className="text-gray-500 mb-4">No technology stack data available</p>
            {onAnalyze && (
              <Button onClick={onAnalyze} variant="outline">
                <Zap className="h-4 w-4 mr-2" />
                Analyze Website
              </Button>
            )}
          </div>
        </CardContent>
      </Card>
    );
  }

  if (techStack.error) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Monitor className="h-5 w-5" />
            Technology Stack
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center gap-2 p-4 border border-red-200 rounded-lg bg-red-50">
            <AlertCircle className="h-5 w-5 text-red-500" />
            <div>
              <p className="text-red-800 font-medium">Analysis Failed</p>
              <p className="text-red-600 text-sm">{techStack.error}</p>
            </div>
          </div>
          {onAnalyze && (
            <div className="mt-4 text-center">
              <Button onClick={onAnalyze} variant="outline">
                <RefreshCw className="h-4 w-4 mr-2" />
                Try Again
              </Button>
            </div>
          )}
        </CardContent>
      </Card>
    );
  }

  const technologies = techStack.technologies || [];
  const categories = new Map<string, Technology[]>();
  
  // Group technologies by category
  technologies.forEach(tech => {
    tech.categories.forEach(category => {
      if (!categories.has(category)) {
        categories.set(category, []);
      }
      categories.get(category)!.push(tech);
    });
  });

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Monitor className="h-5 w-5" />
            Technology Stack
            <Badge variant="secondary" className="ml-2">
              {technologies.length} technologies
            </Badge>
          </div>
          <div className="flex items-center gap-2">
            {techStack.lastAnalyzed && (
              <span className="text-xs text-gray-500">
                {new Date(techStack.lastAnalyzed).toLocaleDateString()}
              </span>
            )}
            {onAnalyze && (
              <Button onClick={onAnalyze} variant="outline" size="sm">
                <RefreshCw className="h-4 w-4" />
              </Button>
            )}
          </div>
        </CardTitle>
      </CardHeader>
      <CardContent>
        {technologies.length === 0 ? (
          <div className="text-center py-6">
            <CheckCircle className="h-12 w-12 text-green-300 mx-auto mb-4" />
            <p className="text-gray-500">No technologies detected</p>
          </div>
        ) : (
          <div className="space-y-4">
            {Array.from(categories.entries()).map(([category, categoryTechs]) => (
              <div key={category}>
                <div className="flex items-center gap-2 mb-2">
                  {getCategoryIcon(category)}
                  <h4 className="font-medium text-sm text-gray-700">{category}</h4>
                  <Badge variant="outline" className="text-xs">
                    {categoryTechs.length}
                  </Badge>
                </div>
                <div className="space-y-2">
                  {categoryTechs.map((tech, index) => (
                    <TechnologyCard key={`${tech.name}-${index}`} technology={tech} />
                  ))}
                </div>
              </div>
            ))}
          </div>
        )}
      </CardContent>
    </Card>
  );
}
