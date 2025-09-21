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

    public function getLabel(): string
    {
        return match($this) {
            self::NEW_LEAD => 'New Lead',
            self::CONTACTED => 'Contacted',
            self::INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::INTERVIEW_COMPLETED => 'Interview Completed',
            self::APPLICATION_RECEIVED => 'Application Received',
            self::AUDIT_IN_PROGRESS => 'Audit in Progress',
            self::AUDIT_COMPLETE => 'Audit Complete',
            self::ENROLLED => 'Enrolled',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::NEW_LEAD => 'New lead has been created',
            self::CONTACTED => 'Lead has been contacted',
            self::INTERVIEW_SCHEDULED => 'Interview has been scheduled',
            self::INTERVIEW_COMPLETED => 'Interview has been completed',
            self::APPLICATION_RECEIVED => 'Application has been received',
            self::AUDIT_IN_PROGRESS => 'Audit is currently in progress',
            self::AUDIT_COMPLETE => 'Audit has been completed',
            self::ENROLLED => 'Lead has been enrolled as a client',
        };
    }

    /**
     * Get all status values as array
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all status values with labels
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->getLabel();
        }
        return $options;
    }

    /**
     * Check if status is in early stage (before interview)
     */
    public function isEarlyStage(): bool
    {
        return in_array($this, [self::NEW_LEAD, self::CONTACTED]);
    }

    /**
     * Check if status is in interview stage
     */
    public function isInterviewStage(): bool
    {
        return in_array($this, [self::INTERVIEW_SCHEDULED, self::INTERVIEW_COMPLETED]);
    }

    /**
     * Check if status is in application stage
     */
    public function isApplicationStage(): bool
    {
        return $this === self::APPLICATION_RECEIVED;
    }

    /**
     * Check if status is in audit stage
     */
    public function isAuditStage(): bool
    {
        return in_array($this, [self::AUDIT_IN_PROGRESS, self::AUDIT_COMPLETE]);
    }

    /**
     * Check if status is final stage
     */
    public function isFinalStage(): bool
    {
        return $this === self::ENROLLED;
    }
}
