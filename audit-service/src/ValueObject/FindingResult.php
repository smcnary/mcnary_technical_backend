<?php

declare(strict_types=1);

namespace App\ValueObject;

final class FindingResult
{
    public function __construct(
        public readonly string $checkCode,
        public readonly string $category,
        public readonly string $severity,
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $recommendation = null,
        public readonly array $evidence = [],
        public readonly float $impactScore = 0.0,
        public readonly string $effort = 'medium'
    ) {}

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isHigh(): bool
    {
        return $this->severity === 'high';
    }

    public function isMedium(): bool
    {
        return $this->severity === 'medium';
    }

    public function isLow(): bool
    {
        return $this->severity === 'low';
    }

    public function toArray(): array
    {
        return [
            'check_code' => $this->checkCode,
            'category' => $this->category,
            'severity' => $this->severity,
            'title' => $this->title,
            'description' => $this->description,
            'recommendation' => $this->recommendation,
            'evidence' => $this->evidence,
            'impact_score' => $this->impactScore,
            'effort' => $this->effort,
        ];
    }
}