import Link from 'next/link'

export default function ClientSidebar() {
  return (
    <aside className="h-screen sticky top-0 border-r border-gray-200 p-4 w-[240px] bg-white">
      <div className="font-semibold mb-4">Client</div>
      <nav className="space-y-2 text-sm">
        <Link href="/client" className="block hover:underline">Dashboard</Link>
        <Link href="/client/leads" className="block hover:underline">Leads</Link>
        <Link href="/client/cases" className="block hover:underline">Cases</Link>
        <Link href="/client/billing" className="block hover:underline">Billing</Link>
        <Link href="/client/settings" className="block hover:underline">Settings</Link>
      </nav>
    </aside>
  )
}
