<?php

declare(strict_types=1);

namespace App\Service\Check;

use App\Entity\Page;
use App\ValueObject\FindingResult;

interface CheckInterface
{
    public function getCode(): string;
    public function getCategory(): string;
    public function getSeverity(): string;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getRecommendation(): ?string;
    public function getEffort(): string;
    public function getImpactScore(): float;
    public function run(Page $page): ?FindingResult;
    public function isApplicable(Page $page): bool;
}
