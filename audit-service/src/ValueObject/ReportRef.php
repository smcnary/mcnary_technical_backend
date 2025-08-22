<?php

declare(strict_types=1);

namespace App\ValueObject;

final class ReportRef
{
    public function __construct(
        public readonly string $runId,
        public readonly string $format,
        public readonly string $storageKey,
        public readonly int $bytes,
        public readonly string $checksum,
        public readonly string $downloadUrl,
        public readonly \DateTimeImmutable $generatedAt,
        public readonly ?string $error = null
    ) {}

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->bytes;
        $unit = 0;

        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }

        return round($bytes, 2) . ' ' . $units[$unit];
    }

    public function toArray(): array
    {
        return [
            'runId' => $this->runId,
            'format' => $this->format,
            'storageKey' => $this->storageKey,
            'bytes' => $this->bytes,
            'formattedSize' => $this->getFormattedSize(),
            'checksum' => $this->checksum,
            'downloadUrl' => $this->downloadUrl,
            'generatedAt' => $this->generatedAt->format('c'),
            'error' => $this->error,
            'success' => $this->isSuccess(),
        ];
    }
}
