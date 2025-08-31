import React from 'react';
import { useAuth } from '../../hooks/useAuth';
import { Loader2, Shield, AlertTriangle } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';

interface ProtectedRouteProps {
  children: React.ReactNode;
  requiredRoles?: string[];
  fallback?: React.ReactNode;
  redirectTo?: string;
  showUnauthorizedMessage?: boolean;
}

export default function ProtectedRoute({
  children,
  requiredRoles = [],
  fallback,
  redirectTo,
  showUnauthorizedMessage = true,
}: ProtectedRouteProps) {
  const { isAuthenticated, isLoading, user, hasAnyRole } = useAuth();

  // Show loading state while checking authentication
  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin mx-auto mb-4 text-primary" />
          <p className="text-muted-foreground">Checking authentication...</p>
        </div>
      </div>
    );
  }

  // Not authenticated
  if (!isAuthenticated) {
    if (fallback) return <>{fallback}</>;
    
    if (redirectTo) {
      // In a real app, you'd use Next.js router here
      window.location.href = redirectTo;
      return null;
    }

    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="w-full max-w-md">
          <CardHeader className="text-center">
            <Shield className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
            <CardTitle>Authentication Required</CardTitle>
            <CardDescription>
              Please sign in to access this page
            </CardDescription>
          </CardHeader>
          <CardContent className="text-center">
            <Button onClick={() => window.location.href = '/login'}>
              Sign In
            </Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  // Check role requirements
  if (requiredRoles.length > 0 && !hasAnyRole(requiredRoles)) {
    if (fallback) return <>{fallback}</>;
    
    if (redirectTo) {
      window.location.href = redirectTo;
      return null;
    }

    if (!showUnauthorizedMessage) {
      return null;
    }

    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="w-full max-w-md">
          <CardHeader className="text-center">
            <AlertTriangle className="h-12 w-12 mx-auto mb-4 text-destructive" />
            <CardTitle>Access Denied</CardTitle>
            <CardDescription>
              You don't have permission to access this page
            </CardDescription>
          </CardHeader>
          <CardContent className="text-center">
            <p className="text-sm text-muted-foreground mb-4">
              Required roles: {requiredRoles.join(', ')}
            </p>
            <p className="text-sm text-muted-foreground mb-4">
              Your roles: {user?.roles.join(', ') || 'None'}
            </p>
            <Button variant="outline" onClick={() => window.history.back()}>
              Go Back
            </Button>
          </CardContent>
        </Card>
      </div>
    );
  }

  // User is authenticated and has required roles
  return <>{children}</>;
}

// Convenience components for common role checks
export function AdminOnly({ children, ...props }: Omit<ProtectedRouteProps, 'requiredRoles'>) {
  return (
    <ProtectedRoute requiredRoles={['ROLE_ADMIN']} {...props}>
      {children}
    </ProtectedRoute>
  );
}

export function ClientAdminOnly({ children, ...props }: Omit<ProtectedRouteProps, 'requiredRoles'>) {
  return (
    <ProtectedRoute requiredRoles={['ROLE_CLIENT_ADMIN']} {...props}>
      {children}
    </ProtectedRoute>
  );
}

export function ClientStaffOnly({ children, ...props }: Omit<ProtectedRouteProps, 'requiredRoles'>) {
  return (
    <ProtectedRoute requiredRoles={['ROLE_CLIENT_ADMIN', 'ROLE_CLIENT_STAFF']} {...props}>
      {children}
    </ProtectedRoute>
  );
}
