<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\AuditRun;

class CrawlAuditRunMessage
{
    public function __construct(
        private string $auditRunId
    ) {}

    public function getAuditRunId(): string
    {
        return $this->auditRunId;
    }
}
