"use client";

import { useEffect, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import ClientDashboard from '@/components/portal/ClientDashboard';
import { ApiService } from '@/services/api';
import { safeLocalStorage } from '@/lib/storage';

export default function ClientDashboardPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [isAuthenticating, setIsAuthenticating] = useState(false);
  const [authError, setAuthError] = useState<string | null>(null);

  useEffect(() => {
    const token = searchParams.get('token');
    const userId = searchParams.get('user_id');

    if (token && userId) {
      // User is arriving from Google OAuth with a token
      setIsAuthenticating(true);
      setAuthError(null);
      
      try {
        // Use the existing API service to set the auth token
        const apiService = new ApiService();
        apiService.setAuthToken(token);
        
        // Store user data
        safeLocalStorage.setItem('userData', JSON.stringify({ id: userId }));
        
        // Remove the token from URL for security
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
        
        console.log('Successfully authenticated via Google OAuth');
      } catch (error) {
        console.error('Authentication error:', error);
        setAuthError('Failed to authenticate. Please try logging in again.');
      } finally {
        setIsAuthenticating(false);
      }
    }
  }, [searchParams]);

  // Show loading state while authenticating
  if (isAuthenticating) {
    return (
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p className="text-gray-600 dark:text-gray-400">Setting up your dashboard...</p>
        </div>
      </div>
    );
  }

  // Show error if authentication failed
  if (authError) {
    return (
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
        <div className="text-center">
          <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p className="font-medium">Authentication Error</p>
            <p className="text-sm">{authError}</p>
          </div>
          <button
            onClick={() => router.push('/login')}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors"
          >
            Go to Login
          </button>
        </div>
      </div>
    );
  }

  return <ClientDashboard />;
}
