import { redirect } from 'next/navigation';

export default function AuditPage() {
  // Redirect to the AuditWizard
  redirect('/audit-wizard');
}
