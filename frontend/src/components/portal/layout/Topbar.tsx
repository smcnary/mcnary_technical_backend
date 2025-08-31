"use client";

import Link from "next/link";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { 
  Bell, 
  Search, 
  Settings, 
  User, 
  LogOut, 
  ChevronDown,
  Menu,
  X,
  Home,
  BarChart3,
  FileText,
  Users,
  Target,
  ClipboardCheck
} from "lucide-react";
import { useState } from "react";
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuLabel, 
  DropdownMenuSeparator, 
  DropdownMenuTrigger 
} from "@/components/ui/dropdown-menu";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";

export default function Topbar() {
  const { user, isAuthenticated, logout, isAdmin, isClientAdmin } = useAuth();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [notifications] = useState(3); // Mock notification count

  const handleLogout = async () => {
    try {
      await logout();
      // Redirect to login page
      window.location.href = '/login';
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  const getUserInitials = (name?: string) => {
    if (!name) return 'U';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
  };

  const getUserRole = () => {
    if (isAdmin()) return 'Administrator';
    if (isClientAdmin()) return 'Client Admin';
    return 'User';
  };

  return (
    <>
      {/* Desktop Topbar */}
      <div className="h-16 bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div className="h-full px-4 lg:px-6 flex items-center justify-between">
          {/* Left Section - Logo and Brand */}
          <div className="flex items-center gap-4">
            <Link href="/" className="flex items-center gap-3 group">
              <div className="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-200">
                <span className="text-white font-bold text-lg">TS</span>
              </div>
              <div className="hidden sm:block">
                <span className="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                  Tulsa SEO
                </span>
                <p className="text-xs text-gray-500 -mt-1">Digital Marketing Platform</p>
              </div>
            </Link>
          </div>

          {/* Center Section - Navigation (Desktop) */}
          <div className="hidden md:flex items-center gap-1">
            <Link 
              href="/dashboard" 
              className="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
            >
              <Home className="w-4 h-4" />
              Dashboard
            </Link>
            <Link 
              href="/analytics" 
              className="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
            >
              <BarChart3 className="w-4 h-4" />
              Analytics
            </Link>
            <Link 
              href="/campaigns" 
              className="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
            >
              <Target className="w-4 h-4" />
              Campaigns
            </Link>
            <Link 
              href="/reports" 
              className="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
            >
              <FileText className="w-4 h-4" />
              Reports
            </Link>
            <Link 
              href="/services/audit" 
              className="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
            >
              <ClipboardCheck className="w-4 h-4" />
              Get an Audit
            </Link>
            {isAdmin() && (
              <Link 
                href="/admin" 
                className="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
              >
                <Users className="w-4 h-4" />
                Admin
              </Link>
            )}
          </div>

          {/* Right Section - Search, Notifications, User Menu */}
          <div className="flex items-center gap-3">
            {/* Search */}
            <Button
              variant="ghost"
              size="sm"
              className="hidden sm:flex items-center gap-2 px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50"
            >
              <Search className="w-4 h-4" />
              <span className="text-sm">Search...</span>
            </Button>

            {/* Notifications */}
            <Button
              variant="ghost"
              size="sm"
              className="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50"
            >
              <Bell className="w-5 h-5" />
              {notifications > 0 && (
                <Badge 
                  variant="destructive" 
                  className="absolute -top-1 -right-1 h-5 w-5 rounded-full p-0 flex items-center justify-center text-xs"
                >
                  {notifications}
                </Badge>
              )}
            </Button>

            {/* User Menu */}
            {isAuthenticated && user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button
                    variant="ghost"
                    className="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg"
                  >
                    <Avatar className="w-8 h-8">
                      <AvatarImage src={user.avatar} alt={user.name || user.email} />
                      <AvatarFallback className="bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-medium">
                        {getUserInitials(user.name)}
                      </AvatarFallback>
                    </Avatar>
                    <div className="hidden sm:block text-left">
                      <p className="text-sm font-medium text-gray-900">
                        {user.name || user.email}
                      </p>
                      <p className="text-xs text-gray-500">{getUserRole()}</p>
                    </div>
                    <ChevronDown className="w-4 h-4 text-gray-400" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-56">
                  <DropdownMenuLabel>
                    <div className="flex flex-col space-y-1">
                      <p className="text-sm font-medium leading-none">
                        {user.name || user.email}
                      </p>
                      <p className="text-xs leading-none text-muted-foreground">
                        {user.email}
                      </p>
                    </div>
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem asChild>
                    <Link href="/profile" className="flex items-center gap-2">
                      <User className="w-4 h-4" />
                      Profile
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem asChild>
                    <Link href="/settings" className="flex items-center gap-2">
                      <Settings className="w-4 h-4" />
                      Settings
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem 
                    onClick={handleLogout}
                    className="flex items-center gap-2 text-red-600 focus:text-red-600"
                  >
                    <LogOut className="w-4 h-4" />
                    Sign out
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <div className="flex items-center gap-2">
                <Button variant="ghost" asChild>
                  <Link href="/login">Sign in</Link>
                </Button>
                <Button asChild>
                  <Link href="/register">Get Started</Link>
                </Button>
              </div>
            )}

            {/* Mobile Menu Button */}
            <Button
              variant="ghost"
              size="sm"
              className="md:hidden p-2"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? (
                <X className="w-5 h-5" />
              ) : (
                <Menu className="w-5 h-5" />
              )}
            </Button>
          </div>
        </div>
      </div>

      {/* Mobile Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden bg-white border-b border-gray-200 shadow-lg">
          <div className="px-4 py-3 space-y-2">
            <Link 
              href="/dashboard" 
              className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
              onClick={() => setMobileMenuOpen(false)}
            >
              <Home className="w-4 h-4" />
              Dashboard
            </Link>
            <Link 
              href="/analytics" 
              className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
              onClick={() => setMobileMenuOpen(false)}
            >
              <BarChart3 className="w-4 h-4" />
              Analytics
            </Link>
            <Link 
              href="/campaigns" 
              className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
              onClick={() => setMobileMenuOpen(false)}
            >
              <Target className="w-4 h-4" />
              Campaigns
            </Link>
            <Link 
              href="/reports" 
              className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
              onClick={() => setMobileMenuOpen(false)}
            >
              <FileText className="w-4 h-4" />
              Reports
            </Link>
            <Link 
              href="/services/audit" 
              className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
              onClick={() => setMobileMenuOpen(false)}
            >
              <ClipboardCheck className="w-4 h-4" />
              Get an Audit
            </Link>
            {isAdmin() && (
              <Link 
                href="/admin" 
                className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
                onClick={() => setMobileMenuOpen(false)}
              >
                <Users className="w-4 h-4" />
                Admin Panel
              </Link>
            )}
            
            {/* Mobile Search */}
            <div className="pt-2 border-t border-gray-200">
              <Button
                variant="ghost"
                className="w-full justify-start text-gray-500 hover:text-gray-700 hover:bg-gray-50"
              >
                <Search className="w-4 h-4 mr-2" />
                Search...
              </Button>
            </div>

            {/* Mobile User Actions */}
            {isAuthenticated && user && (
              <div className="pt-2 border-t border-gray-200 space-y-2">
                <div className="flex items-center gap-3 px-3 py-2">
                  <Avatar className="w-8 h-8">
                    <AvatarImage src={user.avatar} alt={user.name || user.email} />
                    <AvatarFallback className="bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-medium">
                      {getUserInitials(user.name)}
                    </AvatarFallback>
                  </Avatar>
                  <div>
                    <p className="text-sm font-medium text-gray-900">
                      {user.name || user.email}
                    </p>
                    <p className="text-xs text-gray-500">{getUserRole()}</p>
                  </div>
                </div>
                <Link 
                  href="/profile" 
                  className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  <User className="w-4 h-4" />
                  Profile
                </Link>
                <Link 
                  href="/settings" 
                  className="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  <Settings className="w-4 h-4" />
                  Settings
                </Link>
                <button 
                  onClick={() => {
                    handleLogout();
                    setMobileMenuOpen(false);
                  }}
                  className="flex items-center gap-3 px-3 py-2 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition-all duration-200 w-full text-left"
                >
                  <LogOut className="w-4 h-4" />
                  Sign out
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </>
  );
}


