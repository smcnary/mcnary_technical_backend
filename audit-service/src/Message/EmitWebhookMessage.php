<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class EmitWebhookMessage
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $runId,
        
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['audit.run.completed', 'audit.run.failed', 'audit.run.started'])]
        public readonly string $event
    ) {}

    public function getRunId(): string
    {
        return $this->runId;
    }

    public function getEvent(): string
    {
        return $this->event;
    }
}
