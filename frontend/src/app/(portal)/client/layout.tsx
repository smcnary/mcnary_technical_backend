import ClientSidebar from '@/components/portal/navigation/ClientSidebar'
import Topbar from '@/components/portal/layout/Topbar'
import { ThemeProvider } from '@/contexts/ThemeContext'
import { OnboardingProvider } from '@/contexts/OnboardingContext'

export default function ClientLayout({ children }: { children: React.ReactNode }) {
  return (
    <ThemeProvider>
      <OnboardingProvider>
        <div className="min-h-screen grid grid-cols-[240px_1fr]">
          <ClientSidebar data-tour="sidebar-nav" />
          <div className="flex flex-col">
            <Topbar />
            <main className="p-6">{children}</main>
          </div>
        </div>
      </OnboardingProvider>
    </ThemeProvider>
  )
}
