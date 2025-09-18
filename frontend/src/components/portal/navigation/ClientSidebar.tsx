"use client"

import Link from 'next/link'
import { Bell, User, Building2, CreditCard, LogOut } from 'lucide-react'
import { useTheme } from '@/contexts/ThemeContext'
import { useAuth } from '@/hooks/useAuth'

export default function ClientSidebar() {
  const { theme } = useTheme();
  const { logout } = useAuth();
  
  const handleLogout = async () => {
    try {
      await logout();
      window.location.href = '/login';
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };
  
  return (
    <aside className={`h-screen sticky top-0 border-r border-gray-200 dark:border-slate-700 p-4 w-[240px] bg-white dark:bg-slate-900 ${theme === 'dark' ? 'dark' : ''}`}>
      <div className="font-semibold mb-4 text-gray-900 dark:text-white">Client</div>
      <nav className="space-y-2 text-sm">
        <Link href="/client" className="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-gray-700 dark:text-gray-300">
          <div className="w-4 h-4 bg-gray-400 rounded"></div>
          Dashboard
        </Link>
        <Link href="/user-preferences" className="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-gray-700 dark:text-gray-300">
          <User className="w-4 h-4" />
          User Preferences
        </Link>
        <Link href="/admin" className="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-gray-700 dark:text-gray-300">
          <Building2 className="w-4 h-4" />
          Agency Admin
        </Link>
        <Link href="/client/billing" className="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-gray-700 dark:text-gray-300">
          <CreditCard className="w-4 h-4" />
          Billing
        </Link>
        <Link href="/notifications" className="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors text-gray-700 dark:text-gray-300">
          <Bell className="w-4 h-4" />
          Notifications
        </Link>
      </nav>
      
      {/* Logout Button */}
      <div className="mt-auto pt-4 border-t border-gray-200 dark:border-slate-700">
        <button 
          onClick={handleLogout}
          className="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600 dark:text-red-400 w-full text-left"
        >
          <LogOut className="w-4 h-4" />
          Logout
        </button>
      </div>
    </aside>
  )
}


