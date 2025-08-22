<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class GenerateReportMessage
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $runId,
        
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['json', 'html', 'pdf', 'csv'])]
        public readonly string $format
    ) {}

    public function getRunId(): string
    {
        return $this->runId;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
