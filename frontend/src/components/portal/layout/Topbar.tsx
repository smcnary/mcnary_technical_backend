"use client";

import { useState, useRef, useEffect } from "react";
import { ChevronDown, User, Settings, LogOut, Bell } from "lucide-react";
import { logoutUser, fetchUserProfile, UserProfileData, testApiConnection } from "../../../services/userProfile";
import { useRouter } from "next/navigation";

export default function Topbar() {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const [userProfile, setUserProfile] = useState<UserProfileData | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const dropdownRef = useRef<HTMLDivElement>(null);
  const router = useRouter();

  // Fetch user profile data
  useEffect(() => {
    const loadUserProfile = async () => {
      try {
        console.log('🔄 Layout Topbar: Loading user profile...');
        
        // First test API connection
        console.log('🧪 Layout Topbar: Testing API connection...');
        const apiWorking = await testApiConnection();
        console.log('🧪 Layout Topbar: API connection test result:', apiWorking);
        
        if (!apiWorking) {
          console.error('❌ Layout Topbar: API connection test failed');
          setIsLoading(false);
          return;
        }
        
        const profile = await fetchUserProfile();
        console.log('✅ Layout Topbar: User profile loaded:', profile);
        console.log('👤 Layout Topbar: User data:', profile.user);
        console.log('📧 Layout Topbar: Email:', profile.user.email);
        console.log('👤 Layout Topbar: First Name:', profile.user.firstName);
        console.log('👤 Layout Topbar: Last Name:', profile.user.lastName);
        setUserProfile(profile);
      } catch (error) {
        console.error('❌ Layout Topbar: Failed to load user profile:', error);
        // Fallback to default values if profile loading fails
      } finally {
        setIsLoading(false);
      }
    };

    loadUserProfile();
  }, []);

  // Close dropdown when clicking outside
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsDropdownOpen(false);
      }
    }

    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const toggleDropdown = () => setIsDropdownOpen(!isDropdownOpen);

  // Get display name (preferred name or fallback to email prefix)
  const getDisplayName = () => {
    if (!userProfile) return 'User';
    
    if (userProfile.user.name && userProfile.user.name.trim()) {
      return userProfile.user.name;
    }
    
    if (userProfile.user.firstName && userProfile.user.lastName) {
      return `${userProfile.user.firstName} ${userProfile.user.lastName}`;
    }
    
    if (userProfile.user.firstName) {
      return userProfile.user.firstName;
    }
    
    // Fallback to email prefix
    const emailParts = userProfile.user.email.split('@');
    return emailParts[0] ? emailParts[0].charAt(0).toUpperCase() + emailParts[0].slice(1) : 'User';
  };

  // Get user initials for avatar
  const getUserInitials = () => {
    if (!userProfile) return 'U';
    
    if (userProfile.user.firstName && userProfile.user.lastName) {
      return `${userProfile.user.firstName.charAt(0)}${userProfile.user.lastName.charAt(0)}`.toUpperCase();
    }
    
    if (userProfile.user.firstName) {
      return userProfile.user.firstName.charAt(0).toUpperCase();
    }
    
    if (userProfile.user.name) {
      const nameParts = userProfile.user.name.trim().split(' ');
      if (nameParts.length >= 2) {
        return `${nameParts[0].charAt(0)}${nameParts[1].charAt(0)}`.toUpperCase();
      }
      return nameParts[0].charAt(0).toUpperCase();
    }
    
    // Fallback to email prefix
    const emailParts = userProfile.user.email.split('@');
    return emailParts[0] ? emailParts[0].charAt(0).toUpperCase() : 'U';
  };

  return (
    <div className="h-14 border-b border-gray-200 px-4 flex items-center justify-between bg-white">
      <div className="font-semibold text-gray-900">SEO Portal</div>
      
      {/* Account Preferences Dropdown */}
      <div className="relative" ref={dropdownRef}>
        <button
          onClick={toggleDropdown}
          className="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          {/* User Avatar/Initials */}
          <div className="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
            <span className="text-sm font-semibold text-white">
              {isLoading ? '...' : getUserInitials()}
            </span>
          </div>
          
          {/* User Name */}
          <span className="hidden sm:block">
            {isLoading ? 'Loading...' : getDisplayName()}
          </span>
          
          {/* Dropdown Arrow */}
          <ChevronDown 
            className={`h-4 w-4 text-gray-400 transition-transform ${
              isDropdownOpen ? 'rotate-180' : ''
            }`} 
          />
        </button>

        {/* Dropdown Menu */}
        {isDropdownOpen && (
          <div className="absolute right-0 mt-2 w-56 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
            <div className="py-1">
              {/* User Info Section */}
              <div className="px-4 py-3 border-b border-gray-100">
                <p className="text-sm font-medium text-gray-900">
                  {isLoading ? 'Loading...' : getDisplayName()}
                </p>
                <p className="text-sm text-gray-500">
                  {isLoading ? 'Loading...' : userProfile?.user.email || 'No email available'}
                </p>
              </div>

              {/* Menu Items */}
              <a
                href="#profile"
                className="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                <User className="h-4 w-4" />
                Profile
              </a>
              
              <a
                href="#notifications"
                className="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                <Bell className="h-4 w-4" />
                Notifications
              </a>
              
              <a
                href="#settings"
                className="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                <Settings className="h-4 w-4" />
                Settings
              </a>

              {/* Divider */}
              <div className="border-t border-gray-100 my-1"></div>

              {/* Sign Out */}
              <button
                onClick={async () => {
                  try {
                    const result = await logoutUser();
                    if (result.success) {
                      router.push(result.redirectUrl || '/login');
                    }
                  } catch (error) {
                    console.error('Logout failed:', error);
                    router.push('/login');
                  }
                  setIsDropdownOpen(false);
                }}
                className="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
              >
                <LogOut className="h-4 w-4" />
                Sign out
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}


