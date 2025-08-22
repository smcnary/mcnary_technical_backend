"use client";

import { useState, useEffect } from "react";
import { fetchUserGreeting, UserGreetingData } from "../../services/userProfile";

interface UserGreetingProps {
  className?: string;
  fallbackData?: {
    userName?: string;
    organizationName?: string;
    userRole?: string;
  };
}

export default function UserGreeting({ 
  className = "",
  fallbackData = {
    userName: "User",
    organizationName: "Organization",
    userRole: "User"
  }
}: UserGreetingProps) {
  const [greetingData, setGreetingData] = useState<UserGreetingData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentTime, setCurrentTime] = useState(new Date());

  useEffect(() => {
    // Update time every minute
    const interval = setInterval(() => {
      setCurrentTime(new Date());
    }, 60000);

    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    async function loadUserGreeting() {
      try {
        setLoading(true);
        setError(null);
        
        const data = await fetchUserGreeting();
        setGreetingData(data);
      } catch (err) {
        console.error('Failed to load user greeting:', err);
        setError(err instanceof Error ? err.message : 'Failed to load user data');
        // Use fallback data on error
        setGreetingData({
          displayName: fallbackData.userName || "User",
          organizationName: fallbackData.organizationName || "Organization",
          userRole: fallbackData.userRole || "User",
          timeBasedGreeting: getTimeBasedGreeting(currentTime)
        });
      } finally {
        setLoading(false);
      }
    }

    loadUserGreeting();
  }, [fallbackData]);

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
  if (loading && !greetingData) {
    return (
      <div className={`flex flex-col ${className}`}>
        <div className="text-sm text-slate-600">
          <span className="animate-pulse">Loading...</span>
        </div>
        <div className="text-xs text-slate-500">
          <span className="animate-pulse">Loading...</span>
        </div>
      </div>
    );
  }

  // Use API data if available, otherwise use fallback
  const displayName = greetingData?.displayName || fallbackData.userName || "User";
  const organizationName = greetingData?.organizationName || fallbackData.organizationName || "Organization";
  const userRole = greetingData?.userRole || fallbackData.userRole || "User";
  const timeGreeting = greetingData?.timeBasedGreeting || getTimeBasedGreeting(currentTime);

  return (
    <div className={`flex flex-col ${className}`}>
      <div className="text-sm text-slate-600">
        {timeGreeting}, <span className="font-medium text-slate-900">{displayName}</span>
      </div>
      <div className="text-xs text-slate-500">
        {organizationName} • {userRole}
      </div>
      {error && (
        <div className="text-xs text-amber-600 mt-1">
          Using fallback data
        </div>
      )}
    </div>
  );
}
