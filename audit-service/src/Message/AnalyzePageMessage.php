<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class AnalyzePageMessage
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $runId,
        
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $pageId,
        
        #[Assert\Type('array')]
        public readonly array $options = []
    ) {}

    public function getRunId(): string
    {
        return $this->runId;
    }

    public function getPageId(): string
    {
        return $this->pageId;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
