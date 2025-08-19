import PageHeader from '@/components/portal/PageHeader'

export default function AdminDashboardPage() {
  return (
    <div>
      <PageHeader title="Admin Dashboard" subtitle="System overview" />
      <div className="grid md:grid-cols-3 gap-6">
        <div className="card">Users</div>
        <div className="card">Clients</div>
        <div className="card">Packages</div>
      </div>
    </div>
  )
}
