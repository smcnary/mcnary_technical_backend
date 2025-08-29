import PageHeader from '@/components/portal/PageHeader'

// Required for static export - generate static params for dynamic route
export async function generateStaticParams() {
  // For now, generate a few example case IDs
  // In production, you might fetch these from an API
  return [
    { caseId: '1' },
    { caseId: '2' },
    { caseId: '3' },
  ]
}

export default async function CaseDetailsPage({ params }: { params: Promise<{ caseId: string }> }) {
  const { caseId } = await params;
  
  return (
    <div>
      <PageHeader title={`Case ${caseId}`} />
      <div className="card">Case details (to be implemented)</div>
    </div>
  )
}
