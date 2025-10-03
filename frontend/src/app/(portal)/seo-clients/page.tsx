"use client";

import { useAuth } from "@/hooks/useAuth";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

export default function SeoClientsPage() {
  const { isAuthenticated, user, hasRole } = useAuth();

  // Check if user has access to SEO Clients
  const hasAccess = isAuthenticated && (
    hasRole('ROLE_SYSTEM_ADMIN') ||
    hasRole('ROLE_ADMIN') ||
    hasRole('ROLE_AGENCY_ADMIN') ||
    hasRole('ROLE_AGENCY_STAFF') ||
    hasRole('ROLE_CLIENT_ADMIN') ||
    hasRole('ROLE_SALES_CONSULTANT')
  );

  if (!isAuthenticated) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="w-full max-w-md">
          <CardHeader className="text-center">
            <CardTitle>Authentication Required</CardTitle>
            <CardDescription>
              Please sign in to access this page
            </CardDescription>
          </CardHeader>
          <CardContent className="text-center">
            <a href="/login" className="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Sign In
            </a>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!hasAccess) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="w-full max-w-md">
          <CardHeader className="text-center">
            <CardTitle>Access Denied</CardTitle>
            <CardDescription>
              You don't have permission to access this page
            </CardDescription>
          </CardHeader>
          <CardContent className="text-center">
            <p className="text-sm text-muted-foreground mb-4">
              Your roles: {user?.roles?.join(', ') || 'None'}
            </p>
            <button 
              onClick={() => window.history.back()}
              className="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
            >
              Go Back
            </button>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">SEO Clients CRM</h1>
          <p className="text-muted-foreground">
            Manage your SEO clients, leads, and campaigns
          </p>
        </div>
      </div>
      
      <div className="bg-white p-6 rounded-lg shadow">
        <h2 className="text-xl font-semibold mb-4">SEO Clients Dashboard</h2>
        <p>Welcome to the SEO Clients CRM! This page is now accessible to users with the appropriate roles.</p>
        <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded">
          <p className="text-green-800">
            ✅ Access granted for user: {user?.email} with roles: {user?.roles?.join(', ')}
          </p>
        </div>
      </div>
    </div>
  );
}
