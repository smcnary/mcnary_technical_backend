import Link from 'next/link'

export default function ClientSidebar() {
  return (
    <aside className="h-screen sticky top-0 border-r border-gray-200 p-4 w-[240px] bg-white">
      <div className="font-semibold mb-4">Client</div>
      <nav className="space-y-2 text-sm">
        <Link href="/client/audit" className="block hover:underline">Audit</Link>
        <Link href="/client" className="block hover:underline">Dashboard</Link>
        <Link href="/client/billing" className="block hover:underline">Billing</Link>
      </nav>
    </aside>
  )
}
