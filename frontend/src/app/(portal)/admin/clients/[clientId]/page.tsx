import PageHeader from '@/components/portal/PageHeader'

// Required for static export - generate static params for dynamic route
export async function generateStaticParams() {
  // For now, generate a few example client IDs
  // In production, you might fetch these from an API
  return [
    { clientId: '1' },
    { clientId: '2' },
    { clientId: '3' },
  ]
}

export default async function AdminClientDetailPage({ params }: { params: Promise<{ clientId: string }> }) {
  const { clientId } = await params;
  
  return (
    <div>
      <PageHeader title={`Client ${clientId}`} />
      <div className="card">Client details form (to be implemented)</div>
    </div>
  )
}
