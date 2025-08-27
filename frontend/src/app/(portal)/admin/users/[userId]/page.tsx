import PageHeader from '@/components/portal/PageHeader'

export default async function AdminUserDetailPage({ params }: { params: Promise<{ userId: string }> }) {
  const { userId } = await params;
  
  return (
    <div>
      <PageHeader title={`User ${userId}`} />
      <div className="card">User details form (to be implemented)</div>
    </div>
  )
}
