import Link from 'next/link'

export default function AdminSidebar() {
  return (
    <aside className="h-screen sticky top-0 border-r border-gray-200 p-4 w-[240px] bg-white">
      <div className="font-semibold mb-4">Admin</div>
      <nav className="space-y-2 text-sm">
        <Link href="/admin" className="block hover:underline">Dashboard</Link>
        <Link href="/admin/crm" className="block hover:underline">CRM</Link>
        <Link href="/admin/leadgen" className="block hover:underline">Leadgen</Link>
        <Link href="/admin/users" className="block hover:underline">Users</Link>
        <Link href="/admin/clients" className="block hover:underline">Clients</Link>
        <Link href="/admin/packages" className="block hover:underline">Packages</Link>
        <Link href="/admin/analytics" className="block hover:underline">Analytics</Link>
        <Link href="/admin/settings" className="block hover:underline">Settings</Link>
      </nav>
    </aside>
  )
}


