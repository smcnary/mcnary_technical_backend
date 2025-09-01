<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DomainOrUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DomainOrUrl) {
            throw new UnexpectedTypeException($constraint, DomainOrUrl::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Check if it's a valid URL (with protocol)
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return;
        }

        // Check if it's a valid domain name
        if ($this->isValidDomain($value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->addViolation();
    }

    private function isValidDomain(string $domain): bool
    {
        // Remove any leading/trailing whitespace
        $domain = trim($domain);
        
        // Check if it contains a protocol (should be handled by URL validation above)
        if (preg_match('/^[a-zA-Z]+:\/\//', $domain)) {
            return false;
        }

        // Basic domain validation regex
        // Allows: example.com, sub.example.com, example.co.uk, etc.
        $pattern = '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/';
        
        return preg_match($pattern, $domain) === 1;
    }
}
