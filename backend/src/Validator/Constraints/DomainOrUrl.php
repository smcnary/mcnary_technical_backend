<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DomainOrUrl extends Constraint
{
    public $message = 'The value "{{ value }}" is not a valid domain or URL.';

    public function validatedBy(): string
    {
        return DomainOrUrlValidator::class;
    }
}
