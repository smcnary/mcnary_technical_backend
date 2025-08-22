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
 * Fetch user profile data from the backend API
 */
export async function fetchUserProfile(): Promise<UserProfileData> {
  try {
    const response = await fetch('/api/v1/user-profile/greeting', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        // Include any authentication headers if needed
        // 'Authorization': `Bearer ${token}`,
      },
      credentials: 'include', // Include cookies for session-based auth
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching user profile:', error);
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
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
      },
    });

    if (response.ok) {
      const data = await response.json();
      // Clear local storage/auth tokens
      localStorage.removeItem('auth_token');
      localStorage.removeItem('userData');
      
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
    localStorage.removeItem('auth_token');
    localStorage.removeItem('userData');
    clearUserProfileCache();
    
    return {
      success: true,
      redirectUrl: '/login'
    };
  }
}
