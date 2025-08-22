<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class AggregateAuditMessage
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $runId
    ) {}

    public function getRunId(): string
    {
        return $this->runId;
    }
}
