import { ThemeProvider } from '@/contexts/ThemeContext'
import { OnboardingProvider } from '@/contexts/OnboardingContext'
import PortalHeader from '@/components/portal/layout/PortalHeader'

export default function PortalLayout({ children }: { children: React.ReactNode }) {
  return (
    <ThemeProvider>
      <OnboardingProvider>
        <div className="min-h-screen bg-slate-50 dark:bg-slate-900">
          <PortalHeader />
          <main className="flex-1 p-6">{children}</main>
        </div>
      </OnboardingProvider>
    </ThemeProvider>
  )
}
