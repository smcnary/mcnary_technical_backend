<?php

declare(strict_types=1);

namespace App\ValueObject;

final class FetchedResource
{
    public function __construct(
        public readonly string $url,
        public readonly int $statusCode,
        public readonly string $contentType,
        public readonly string $body,
        public readonly array $headers,
        public readonly float $responseTime,
        public readonly int $contentLength,
        public readonly ?string $canonicalUrl = null,
        public readonly array $robotsDirectives = [],
        public readonly ?string $screenshotPath = null,
        public readonly ?string $htmlPath = null,
        public readonly ?string $error = null
    ) {}

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    public function isHtml(): bool
    {
        return str_contains($this->contentType, 'text/html');
    }

    public function isXml(): bool
    {
        return str_contains($this->contentType, 'xml');
    }

    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($headerName) === $name) {
                return $value;
            }
        }
        return null;
    }

    public function hasRobotsDirective(string $directive): bool
    {
        return in_array($directive, $this->robotsDirectives, true);
    }

    public function getBodyHash(): string
    {
        return hash('sha256', $this->body);
    }
}
