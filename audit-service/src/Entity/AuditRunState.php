<?php

declare(strict_types=1);

namespace App\Entity;

enum AuditRunState: string
{
    case DRAFT = 'DRAFT';
    case QUEUED = 'QUEUED';
    case RUNNING = 'RUNNING';
    case FAILED = 'FAILED';
    case CANCELED = 'CANCELED';
    case COMPLETED = 'COMPLETED';

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::QUEUED => 'Queued',
            self::RUNNING => 'Running',
            self::FAILED => 'Failed',
            self::CANCELED => 'Canceled',
            self::COMPLETED => 'Completed',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::FAILED, self::CANCELED, self::COMPLETED]);
    }

    public function canTransitionTo(self $targetState): bool
    {
        return match($this) {
            self::DRAFT => in_array($targetState, [self::QUEUED]),
            self::QUEUED => in_array($targetState, [self::RUNNING, self::CANCELED]),
            self::RUNNING => in_array($targetState, [self::FAILED, self::COMPLETED, self::CANCELED]),
            self::FAILED, self::CANCELED, self::COMPLETED => false,
        };
    }
}
