"use client";

import React, { useState, useEffect } from "react";
import { useAuth } from "@/hooks/useAuth";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import SeoClientsTab from "@/components/dashboard/SeoClientsTab";
import UserAvatar from "@/components/ui/UserAvatar";
import ProtectedRoute from "@/components/auth/ProtectedRoute";

export default function SeoClientsPage() {
  const { isAdmin, isSalesConsultant, isAuthenticated } = useAuth();
  const [isClient, setIsClient] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  
  // Debug: Force show SEO Clients for testing
  const debugShowSeoClients = true;

  // Prevent hydration mismatch by only rendering after client-side mount
  useEffect(() => {
    setIsClient(true);
    setIsLoading(false);
  }, []);

  // Always show content (removed loading state for debugging)

  if (!debugShowSeoClients && !isAdmin() && !isSalesConsultant()) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="w-full max-w-md">
          <CardHeader className="text-center">
            <CardTitle>Access Denied</CardTitle>
            <CardDescription>You don't have permission to access this page</CardDescription>
          </CardHeader>
        </Card>
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="container mx-auto p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">SEO Clients CRM</h1>
            <p className="text-muted-foreground">
              Manage your SEO clients, leads, and campaigns
            </p>
          </div>
          <UserAvatar showNotifications={true} />
        </div>

        {/* Active Page Indicator */}
        <div className="flex items-center gap-2 mb-4 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
          <div className="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
          <span className="text-sm font-medium text-purple-700 dark:text-purple-300">Currently viewing: SEO Clients CRM</span>
        </div>

        {/* SEO Clients Content */}
        <SeoClientsTab />
      </div>
    </ProtectedRoute>
  );
}
