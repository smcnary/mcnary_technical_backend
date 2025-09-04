<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AuditIntakeClient extends Constraint
{
    public string $emailMessage = 'The email "{{ email }}" is already associated with an existing client "{{ client_name }}". Please use a different email or contact the existing client.';
    
    public string $websiteMessage = 'The website "{{ website }}" appears to be associated with an existing client "{{ client_name }}". Please verify this is the correct client or use a different website.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
