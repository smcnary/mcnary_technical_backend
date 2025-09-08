'use client';

import React from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AuditDashboard from '@/components/audit/AuditDashboard';
import AuditProgressTracker from '@/components/audit/AuditProgressTracker';
import AuditIssuesManager from '@/components/audit/AuditIssuesManager';

export default function AuditPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-gray-900">SEO Audit</h1>
        <p className="text-gray-600 mt-2">
          Comprehensive SEO analysis and recommendations for your website
        </p>
      </div>

      <Tabs defaultValue="dashboard" className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="dashboard">Dashboard</TabsTrigger>
          <TabsTrigger value="progress">Progress</TabsTrigger>
          <TabsTrigger value="issues">Issues</TabsTrigger>
        </TabsList>
        
        <TabsContent value="dashboard" className="mt-6">
          <AuditDashboard />
        </TabsContent>
        
        <TabsContent value="progress" className="mt-6">
          <AuditProgressTracker />
        </TabsContent>
        
        <TabsContent value="issues" className="mt-6">
          <AuditIssuesManager />
        </TabsContent>
      </Tabs>
    </div>
  );
}
