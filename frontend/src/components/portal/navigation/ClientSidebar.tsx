"use client"

import Link from 'next/link'
import { Bell, User, Building2, CreditCard } from 'lucide-react'
import { useTheme } from '@/contexts/ThemeContext'

export default function ClientSidebar() {
  const { theme } = useTheme();
  
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
    </aside>
  )
}


