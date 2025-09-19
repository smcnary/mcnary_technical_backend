<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

class DateTimeImmutableNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public function normalize($object, string $format = null, array $context = []): string
    {
        return $object->format('Y-m-d\TH:i:sP');
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof \DateTimeImmutable;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): \DateTimeImmutable
    {
        return new \DateTimeImmutable($data);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === \DateTimeImmutable::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
