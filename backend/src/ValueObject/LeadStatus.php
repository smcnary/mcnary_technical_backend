<?php

namespace App\ValueObject;

enum LeadStatus: string
{
    case NEW_LEAD = 'new_lead';
    case CONTACTED = 'contacted';
    case INTERVIEW_SCHEDULED = 'interview_scheduled';
    case INTERVIEW_COMPLETED = 'interview_completed';
    case APPLICATION_RECEIVED = 'application_received';
    case AUDIT_IN_PROGRESS = 'audit_in_progress';
    case AUDIT_COMPLETE = 'audit_complete';
    case ENROLLED = 'enrolled';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match($this) {
            self::NEW_LEAD => 'New Lead',
            self::CONTACTED => 'Contacted',
            self::INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::INTERVIEW_COMPLETED => 'Interview Completed',
            self::APPLICATION_RECEIVED => 'Application Received',
            self::AUDIT_IN_PROGRESS => 'Audit In Progress',
            self::AUDIT_COMPLETE => 'Audit Complete',
            self::ENROLLED => 'Enrolled',
            self::CLOSED => 'Closed',
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
