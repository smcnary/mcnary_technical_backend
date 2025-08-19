import ClientSidebar from '@/components/portal/ClientSidebar'
import Topbar from '@/components/portal/Topbar'

export default function ClientLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen grid grid-cols-[240px_1fr]">
      <ClientSidebar />
      <div className="flex flex-col">
        <Topbar />
        <main className="p-6">{children}</main>
      </div>
    </div>
  )
}
