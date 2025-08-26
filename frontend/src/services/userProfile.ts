export interface UserProfileData {
  user: {
    id: string;
    email: string;
    firstName: string | null;
    lastName: string | null;
    name: string | null;
    role: string;
    status: string;
    lastLoginAt: string | null;
  };
  agency: {
    id: string;
    name: string;
    domain: string | null;
    description: string | null;
  };
  client: {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    status: string;
  } | null;
  greeting: {
    displayName: string;
    organizationName: string;
    userRole: string;
    timeBasedGreeting: string;
  };
}

export interface UserGreetingData {
  displayName: string;
  organizationName: string;
  userRole: string;
  timeBasedGreeting: string;
}

/**
 * Test basic API connectivity
 */
export async function testApiConnection(): Promise<boolean> {
  try {
    console.log('🧪 Testing API connection...');
    
    const response = await fetch('/api/v1/test', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    console.log('🧪 Test response status:', response.status);
    console.log('🧪 Test response ok:', response.ok);

    if (response.ok) {
      const data = await response.json();
      console.log('🧪 Test response data:', data);
      return true;
    } else {
      console.error('🧪 Test failed with status:', response.status);
      return false;
    }
  } catch (error) {
    console.error('🧪 Test error:', error);
    return false;
  }
}

/**
 * Fetch user profile data from the backend API
 */
export async function fetchUserProfile(): Promise<UserProfileData> {
  try {
    // Get auth token from cookies (since login API sets it as HTTP-only cookie)
    const getCookie = (name: string) => {
      if (typeof document === 'undefined') return null;
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop()?.split(';').shift();
      return null;
    };
    
    const authToken = getCookie('auth_token');
    
    console.log('🔍 Fetching user profile...');
    console.log('🔑 Auth token available:', !!authToken);
    console.log('🔑 Auth token value:', authToken);
    console.log('🍪 All cookies:', document.cookie);
    
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
    };
    
    // Include auth token if available
    if (authToken) {
      headers['Authorization'] = `Bearer ${authToken}`;
    }

    console.log('📡 Making API call to /api/v1/me');
    console.log('📋 Headers:', headers);

    const response = await fetch('/api/v1/me', {
      method: 'GET',
      headers,
      credentials: 'include', // Include cookies for session-based auth
    });

    console.log('📥 Response status:', response.status);
    console.log('📥 Response ok:', response.ok);
    console.log('📥 Response headers:', Object.fromEntries(response.headers.entries()));

    if (!response.ok) {
      const errorText = await response.text();
      console.error('❌ API Error Response:', errorText);
      console.error('❌ Response status:', response.status);
      console.error('❌ Response status text:', response.statusText);
      
      // Try to parse error as JSON
      try {
        const errorJson = JSON.parse(errorText);
        console.error('❌ Error JSON:', errorJson);
      } catch (e) {
        console.error('❌ Error is not JSON');
      }
      
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const userData = await response.json();
    console.log('✅ User data received:', userData);
    console.log('👤 User data:', userData);
    console.log('📧 Email:', userData?.email);
    console.log('👤 First Name:', userData?.firstName);
    console.log('👤 Last Name:', userData?.lastName);
    console.log('🔑 User ID:', userData?.id);
    console.log('🎭 Roles:', userData?.roles);
    
    // Transform the data to match the expected UserProfileData interface
    const profileData: UserProfileData = {
      user: {
        id: userData.id,
        email: userData.email,
        firstName: userData.firstName,
        lastName: userData.lastName,
        name: userData.name,
        role: userData.roles?.[0] || 'ROLE_USER',
        status: userData.status,
        lastLoginAt: userData.last_login_at
      },
      agency: {
        id: userData.agency_id || '',
        name: 'Organization',
        domain: null,
        description: null
      },
      client: null,
      greeting: {
        displayName: userData.name || userData.email,
        organizationName: 'Organization',
        userRole: 'User',
        timeBasedGreeting: 'Hello'
      }
    };
    
    console.log('🔄 Transformed profile data:', profileData);
    
    return profileData;
  } catch (error) {
    console.error('❌ Error fetching user profile:', error);
    console.error('❌ Error details:', {
      name: error instanceof Error ? error.name : 'Unknown',
      message: error instanceof Error ? error.message : String(error),
      stack: error instanceof Error ? error.stack : 'No stack trace'
    });
    throw new Error('Failed to fetch user profile data');
  }
}

/**
 * Extract just the greeting data from the full profile response
 */
export async function fetchUserGreeting(): Promise<UserGreetingData> {
  const profile = await fetchUserProfile();
  return profile.greeting;
}

/**
 * Get cached user profile data or fetch from API
 */
let cachedProfile: UserProfileData | null = null;
let cacheTimestamp: number = 0;
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

export async function getCachedUserProfile(): Promise<UserProfileData> {
  const now = Date.now();
  
  // Return cached data if it's still valid
  if (cachedProfile && (now - cacheTimestamp) < CACHE_DURATION) {
    return cachedProfile;
  }

  // Fetch fresh data
  try {
    const profile = await fetchUserProfile();
    cachedProfile = profile;
    cacheTimestamp = now;
    return profile;
  } catch (error) {
    // Return cached data if available, even if expired
    if (cachedProfile) {
      console.warn('Using expired cached profile data due to API error:', error);
      return cachedProfile;
    }
    throw error;
  }
}

/**
 * Clear the cached profile data
 */
export function clearUserProfileCache(): void {
  cachedProfile = null;
  cacheTimestamp = 0;
}

/**
 * Logout user and clear all authentication data
 */
export async function logoutUser(): Promise<{ success: boolean; redirectUrl?: string }> {
  try {
    // Call backend logout endpoint
    const response = await fetch('/api/v1/auth/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${typeof window !== 'undefined' ? document.cookie.split('auth_token=')[1]?.split(';')[0] : null}`,
      },
    });

    if (response.ok) {
      const data = await response.json();
      // Clear auth token cookie
      if (typeof window !== 'undefined') {
        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
      }
      
      // Clear profile cache
      clearUserProfileCache();
      
      return {
        success: true,
        redirectUrl: data.data?.redirectUrl || '/login'
      };
    } else {
      throw new Error('Logout failed');
    }
  } catch (error) {
    console.error('Error during logout:', error);
    // Even if logout fails, clear local data
    if (typeof window !== 'undefined') {
      document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }
    clearUserProfileCache();
    
    return {
      success: true,
      redirectUrl: '/login'
    };
  }
}
