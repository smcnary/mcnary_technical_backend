<?php

namespace App\Validator;

use App\Entity\AuditIntake;
use App\Repository\ClientRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AuditIntakeClientValidator extends ConstraintValidator
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AuditIntakeClient) {
            throw new UnexpectedTypeException($constraint, AuditIntakeClient::class);
        }

        if (!$value instanceof AuditIntake) {
            return;
        }

        $contactEmail = $value->getContactEmail();
        $websiteUrl = $value->getWebsiteUrl();

        if (!$contactEmail && !$websiteUrl) {
            return;
        }

        // Check if contact email is associated with an existing client
        if ($contactEmail) {
            $existingClient = $this->clientRepository->findByEmail($contactEmail);
            if ($existingClient) {
                $this->context->buildViolation($constraint->emailMessage)
                    ->setParameter('{{ email }}', $contactEmail)
                    ->setParameter('{{ client_name }}', $existingClient->getName())
                    ->addViolation();
            }
        }

        // Check if website URL domain matches an existing client slug
        if ($websiteUrl) {
            $domain = $this->extractDomain($websiteUrl);
            if ($domain) {
                $existingClient = $this->clientRepository->findBySlug($domain);
                if ($existingClient) {
                    $this->context->buildViolation($constraint->websiteMessage)
                        ->setParameter('{{ website }}', $websiteUrl)
                        ->setParameter('{{ client_name }}', $existingClient->getName())
                        ->addViolation();
                }
            }
        }
    }

    private function extractDomain(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return null;
        }

        $host = $parsedUrl['host'];
        
        // Remove www. prefix if present
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        // Convert to slug format (lowercase, replace dots with hyphens)
        $slug = strtolower(str_replace('.', '-', $host));
        
        return $slug;
    }
}
