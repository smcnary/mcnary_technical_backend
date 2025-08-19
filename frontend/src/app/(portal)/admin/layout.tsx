import AdminSidebar from '@/components/portal/AdminSidebar'
import Topbar from '@/components/portal/Topbar'

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen grid grid-cols-[240px_1fr]">
      <AdminSidebar />
      <div className="flex flex-col">
        <Topbar />
        <main className="p-6">{children}</main>
      </div>
    </div>
  )
}
