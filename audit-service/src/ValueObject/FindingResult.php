<?php

declare(strict_types=1);

namespace App\ValueObject;

enum FindingStatus: string
{
    case PASS = 'pass';
    case FAIL = 'fail';
    case WARN = 'warn';
    case NA = 'na';
}

enum FindingSeverity: string
{
    case INFO = 'info';
    case LOW = 'low';
    case MED = 'med';
    case HIGH = 'high';
    case CRITICAL = 'critical';
}

final class FindingResult
{
    public function __construct(
        public readonly string $checkCode,
        public readonly FindingStatus $status,
        public readonly FindingSeverity $severity,
        public readonly array $evidence,
        public readonly ?float $scoreDelta = null,
        public readonly ?string $message = null
    ) {}

    public function isPass(): bool
    {
        return $this->status === FindingStatus::PASS;
    }

    public function isFail(): bool
    {
        return $this->status === FindingStatus::FAIL;
    }

    public function isWarning(): bool
    {
        return $this->status === FindingStatus::WARN;
    }

    public function isNotApplicable(): bool
    {
        return $this->status === FindingStatus::NA;
    }

    public function getSeverityWeight(): float
    {
        return match($this->severity) {
            FindingSeverity::INFO => 0.1,
            FindingSeverity::LOW => 0.3,
            FindingSeverity::MED => 0.6,
            FindingSeverity::HIGH => 0.8,
            FindingSeverity::CRITICAL => 1.0,
        };
    }

    public function toArray(): array
    {
        return [
            'checkCode' => $this->checkCode,
            'status' => $this->status->value,
            'severity' => $this->severity->value,
            'evidence' => $this->evidence,
            'scoreDelta' => $this->scoreDelta,
            'message' => $this->message,
        ];
    }
}
