import PageHeader from '@/components/portal/PageHeader'

export default function AdminClientDetailPage({ params }: { params: { clientId: string } }) {
  return (
    <div>
      <PageHeader title={`Client ${params.clientId}`} />
      <div className="card">Client details form (to be implemented)</div>
    </div>
  )
}
