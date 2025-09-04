<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class DomainOrUrl extends Constraint
{
    public $message = 'The value "{{ value }}" is not a valid domain or URL.';

    public function validatedBy(): string
    {
        return DomainOrUrlValidator::class;
    }
}
