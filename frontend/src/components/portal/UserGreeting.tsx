"use client";

import { useState, useEffect } from "react";
import { fetchUserProfile, UserProfileData } from "../../services/userProfile";

interface UserGreetingProps {
  className?: string;
}

export default function UserGreeting({ 
  className = ""
}: UserGreetingProps) {
  const [profileData, setProfileData] = useState<UserProfileData | null>(null);
  const [loading, setLoading] = useState(true);
  const [currentTime, setCurrentTime] = useState(new Date());

  useEffect(() => {
    // Update time every minute
    const interval = setInterval(() => {
      setCurrentTime(new Date());
    }, 60000);

    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    async function loadUserProfile() {
      try {
        setLoading(true);
        
        const data = await fetchUserProfile();
        setProfileData(data);
      } catch (err) {
        console.error('Failed to load user profile:', err);
        setProfileData(null);
      } finally {
        setLoading(false);
      }
    }

    loadUserProfile();
  }, []);

  // Get time-based greeting based on current time
  function getTimeBasedGreeting(time: Date): string {
    const hour = time.getHours();
    
    if (hour >= 5 && hour < 12) {
      return "Good morning";
    } else if (hour >= 12 && hour < 17) {
      return "Good afternoon";
    } else if (hour >= 17 && hour < 21) {
      return "Good evening";
    } else {
      return "Good night";
    }
  }

  // Show loading state
  if (loading && !profileData) {
    return (
      <div className={`flex flex-col ${className}`}>
        <div className="text-sm text-slate-600 dark:text-slate-400">
          <span className="animate-pulse">Loading...</span>
        </div>
        <div className="text-xs text-slate-500 dark:text-slate-500">
          <span className="animate-pulse">Loading...</span>
        </div>
      </div>
    );
  }

  // Use API data if available, otherwise use defaults
  const displayName = profileData?.greeting?.displayName || "User";
  const organizationName = profileData?.greeting?.organizationName || "Organization";
  const userRole = profileData?.greeting?.userRole || "User";
  const timeGreeting = profileData?.greeting?.timeBasedGreeting || getTimeBasedGreeting(currentTime);

  return (
    <div className={`flex flex-col ${className}`}>
      <div className="text-sm text-slate-600 dark:text-slate-400">
        {timeGreeting}, <span className="font-medium text-slate-900 dark:text-white">{displayName}</span>
      </div>
      <div className="text-xs text-slate-500 dark:text-slate-500">
        {organizationName} • {userRole}
      </div>
    </div>
  );
}
