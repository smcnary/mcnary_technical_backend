import PageHeader from '@/components/portal/PageHeader'

export default async function CaseDetailsPage({ params }: { params: Promise<{ caseId: string }> }) {
  const { caseId } = await params;
  
  return (
    <div>
      <PageHeader title={`Case ${caseId}`} />
      <div className="card">Case details (to be implemented)</div>
    </div>
  )
}
