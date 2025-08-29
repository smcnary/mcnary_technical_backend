import PageHeader from '@/components/portal/PageHeader'

// Required for static export - generate static params for dynamic route
export async function generateStaticParams() {
  // For now, generate a few example user IDs
  // In production, you might fetch these from an API
  return [
    { userId: '1' },
    { userId: '2' },
    { userId: '3' },
  ]
}

export default async function AdminUserDetailPage({ params }: { params: Promise<{ userId: string }> }) {
  const { userId } = await params;
  
  return (
    <div>
      <PageHeader title={`User ${userId}`} />
      <div className="card">User details form (to be implemented)</div>
    </div>
  )
}
