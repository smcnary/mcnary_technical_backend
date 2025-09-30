import ClientSidebar from '@/components/portal/navigation/ClientSidebar'
import Topbar from '@/components/portal/layout/Topbar'
import { ThemeProvider } from '@/contexts/ThemeContext'
import { OnboardingProvider } from '@/contexts/OnboardingContext'

export default function ClientLayout({ children }: { children: React.ReactNode }) {
  return (
    <ThemeProvider>
      <OnboardingProvider>
        <div className="min-h-screen grid grid-cols-[240px_1fr] bg-slate-50 dark:bg-slate-900">
          <ClientSidebar data-tour="sidebar-nav" />
          <div className="flex flex-col min-h-screen bg-slate-100/70 dark:bg-slate-900">
            <Topbar />
            <main className="flex-1 p-6">{children}</main>
          </div>
        </div>
      </OnboardingProvider>
    </ThemeProvider>
  )
}
