<?php

namespace App\Tests\Validator;

use App\Validator\Constraints\DomainOrUrl;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class DomainOrUrlTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): \App\Validator\Constraints\DomainOrUrlValidator
    {
        return new \App\Validator\Constraints\DomainOrUrlValidator();
    }

    public function testValidDomains(): void
    {
        $validDomains = [
            'example.com',
            'sub.example.com',
            'example.co.uk',
            'example.org',
            'test.example.com',
            'example123.com',
            'example-test.com',
            'smcnary.github.io',
            'www.example.com',
        ];

        foreach ($validDomains as $domain) {
            $this->validator->validate($domain, new DomainOrUrl());
            $this->assertNoViolation();
        }
    }

    public function testValidUrls(): void
    {
        $validUrls = [
            'https://example.com',
            'http://example.com',
            'https://sub.example.com',
            'https://example.co.uk',
            'https://smcnary.github.io',
            'http://www.example.com',
            'https://example.com/path',
            'https://example.com/path?param=value',
        ];

        foreach ($validUrls as $url) {
            $this->validator->validate($url, new DomainOrUrl());
            $this->assertNoViolation();
        }
    }

    public function testInvalidDomain(): void
    {
        $this->validator->validate('not-a-domain', new DomainOrUrl());
        $this->buildViolation('The value "{{ value }}" is not a valid domain or URL.')
            ->setParameter('{{ value }}', '"not-a-domain"')
            ->assertRaised();
    }

    public function testInvalidUrl(): void
    {
        $this->validator->validate('http://', new DomainOrUrl());
        $this->buildViolation('The value "{{ value }}" is not a valid domain or URL.')
            ->setParameter('{{ value }}', '"http://"')
            ->assertRaised();
    }

    public function testNullAndEmptyValues(): void
    {
        $this->validator->validate(null, new DomainOrUrl());
        $this->assertNoViolation();

        $this->validator->validate('', new DomainOrUrl());
        $this->assertNoViolation();
    }

    public function testNonStringValues(): void
    {
        $this->expectException(\Symfony\Component\Validator\Exception\UnexpectedValueException::class);
        $this->validator->validate(123, new DomainOrUrl());
    }
}
