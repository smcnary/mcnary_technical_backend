<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Reusable createdAt/updatedAt with lifecycle hooks.
 */
trait Timestamps
{
    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['lead:read', 'lead:admin:read', 'campaign:read', 'campaign:admin:read', 'case_study:read', 'case_study:admin:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['lead:read', 'lead:admin:read', 'campaign:read', 'campaign:admin:read', 'case_study:read', 'case_study:admin:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $now = new \DateTimeImmutable('now');
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function getCreatedAt(): \DateTimeImmutable 
    { 
        return $this->createdAt; 
    }
    
    public function getUpdatedAt(): \DateTimeImmutable 
    { 
        return $this->updatedAt; 
    }
}
