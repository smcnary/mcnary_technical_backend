<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class CrawlPageMessage
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $runId,
        
        #[Assert\NotBlank]
        #[Assert\Url]
        public readonly string $url,
        
        #[Assert\Type('array')]
        public readonly array $options = []
    ) {}

    public function getRunId(): string
    {
        return $this->runId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
