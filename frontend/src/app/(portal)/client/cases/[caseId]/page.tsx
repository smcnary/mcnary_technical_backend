import PageHeader from '@/components/portal/PageHeader'

export default function CaseDetailsPage({ params }: { params: { caseId: string } }) {
  return (
    <div>
      <PageHeader title={`Case ${params.caseId}`} />
      <div className="card">Case details (to be implemented)</div>
    </div>
  )
}
