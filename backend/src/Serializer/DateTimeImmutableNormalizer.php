<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DateTimeImmutableNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return $object->format('Y-m-d\TH:i:sP');
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof \DateTimeImmutable;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): \DateTimeImmutable
    {
        return new \DateTimeImmutable($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === \DateTimeImmutable::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \DateTimeImmutable::class => true,
        ];
    }
}
