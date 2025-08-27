import PageHeader from '@/components/portal/PageHeader'

export default async function AdminClientDetailPage({ params }: { params: Promise<{ clientId: string }> }) {
  const { clientId } = await params;
  
  return (
    <div>
      <PageHeader title={`Client ${clientId}`} />
      <div className="card">Client details form (to be implemented)</div>
    </div>
  )
}
