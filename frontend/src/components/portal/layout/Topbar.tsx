"use client";

import { useState, useRef, useEffect } from "react";
import { ChevronDown, User, Settings, LogOut, Bell } from "lucide-react";
import UserGreeting from "../user/UserGreeting";
import { logoutUser } from "../../../services/userProfile";
import { useRouter } from "next/navigation";

export default function Topbar() {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);
  const router = useRouter();

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

  return (
    <div className="h-14 border-b border-gray-200 px-4 flex items-center justify-between bg-white">
      <div className="font-semibold text-gray-900">SEO Portal</div>
      
      {/* User Greeting */}
      <div className="flex-1 flex justify-center">
        <UserGreeting 
          fallbackData={{
            userName: "John Doe",
            organizationName: "McNary Legal Services",
            userRole: "Client Admin"
          }}
        />
      </div>
      
      {/* Account Preferences Dropdown */}
      <div className="relative" ref={dropdownRef}>
        <button
          onClick={toggleDropdown}
          className="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          {/* User Avatar/Initials */}
          <div className="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
            <span className="text-sm font-semibold text-white">JD</span>
          </div>
          
          {/* User Name */}
          <span className="hidden sm:block">John Doe</span>
          
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
                <p className="text-sm font-medium text-gray-900">John Doe</p>
                <p className="text-sm text-gray-500">john.doe@example.com</p>
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


