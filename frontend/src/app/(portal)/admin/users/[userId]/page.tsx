import PageHeader from '@/components/portal/PageHeader'

export default function AdminUserDetailPage({ params }: { params: { userId: string } }) {
  return (
    <div>
      <PageHeader title={`User ${params.userId}`} />
      <div className="card">User details form (to be implemented)</div>
    </div>
  )
}
