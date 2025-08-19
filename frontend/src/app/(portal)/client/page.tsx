import PageHeader from '@/components/portal/PageHeader'

export default function ClientDashboardPage() {
  return (
    <div>
      <PageHeader title="Client Dashboard" subtitle="Overview of your account" />
      <div className="grid md:grid-cols-2 gap-6">
        <div className="card">Recent Leads</div>
        <div className="card">Billing Summary</div>
      </div>
    </div>
  )
}
