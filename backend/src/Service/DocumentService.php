<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\DocumentTemplate;
use App\Entity\DocumentSignature;
use App\Entity\DocumentVersion;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\MediaAsset;
use App\Repository\DocumentRepository;
use App\Repository\DocumentTemplateRepository;
use App\Repository\DocumentSignatureRepository;
use App\Repository\DocumentVersionRepository;
use App\Repository\ClientRepository;
use App\Repository\MediaAssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DocumentService
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    private ValidatorInterface $validator;
    private DocumentRepository $documentRepository;
    private DocumentTemplateRepository $templateRepository;
    private DocumentSignatureRepository $signatureRepository;
    private DocumentVersionRepository $versionRepository;
    private ClientRepository $clientRepository;
    private MediaAssetRepository $mediaAssetRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        ValidatorInterface $validator,
        DocumentRepository $documentRepository,
        DocumentTemplateRepository $templateRepository,
        DocumentSignatureRepository $signatureRepository,
        DocumentVersionRepository $versionRepository,
        ClientRepository $clientRepository,
        MediaAssetRepository $mediaAssetRepository
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->validator = $validator;
        $this->documentRepository = $documentRepository;
        $this->templateRepository = $templateRepository;
        $this->signatureRepository = $signatureRepository;
        $this->versionRepository = $versionRepository;
        $this->clientRepository = $clientRepository;
        $this->mediaAssetRepository = $mediaAssetRepository;
    }

    /**
     * Create a new document
     */
    public function createDocument(array $data, User $createdBy): Document
    {
        $document = new Document();
        $document->setCreatedBy($createdBy);
        
        // Set basic properties
        $document->setTitle($data['title'] ?? '');
        $document->setDescription($data['description'] ?? null);
        $document->setContent($data['content'] ?? null);
        $document->setType($data['type'] ?? 'contract');
        $document->setRequiresSignature($data['requires_signature'] ?? true);
        $document->setIsTemplate($data['is_template'] ?? false);
        
        // Set client
        if (isset($data['client_id'])) {
            $client = $this->clientRepository->find($data['client_id']);
            if (!$client) {
                throw new \InvalidArgumentException('Client not found');
            }
            $document->setClient($client);
        }
        
        // Set template if provided
        if (isset($data['template_id'])) {
            $template = $this->templateRepository->find($data['template_id']);
            if ($template) {
                $document->setTemplate($template);
                
                // Process template content with provided data
                if ($template->getContent()) {
                    $processedContent = $template->processContent($data['template_variables'] ?? []);
                    $document->setContent($processedContent);
                }
                
                // Copy signature fields from template
                if ($template->getSignatureFields()) {
                    $document->setSignatureFields($template->getSignatureFields());
                }
            }
        }
        
        // Set file if provided
        if (isset($data['file_id'])) {
            $file = $this->mediaAssetRepository->find($data['file_id']);
            if ($file) {
                $document->setFile($file);
            }
        }
        
        // Set metadata
        if (isset($data['metadata'])) {
            $document->setMetadata($data['metadata']);
        }
        
        // Set signature fields
        if (isset($data['signature_fields'])) {
            $document->setSignatureFields($data['signature_fields']);
        }
        
        // Set expiration date
        if (isset($data['expires_at'])) {
            $expiresAt = new \DateTimeImmutable($data['expires_at']);
            $document->setExpiresAt($expiresAt);
        }
        
        // Validate the document
        $violations = $this->validator->validate($document);
        if (count($violations) > 0) {
            throw new \InvalidArgumentException('Validation failed: ' . (string) $violations);
        }
        
        $this->entityManager->persist($document);
        
        // Create initial version
        $this->createVersion($document, $createdBy, 'Initial version', $data);
        
        $this->entityManager->flush();
        
        return $document;
    }

    /**
     * Update an existing document
     */
    public function updateDocument(Document $document, array $data, User $updatedBy): Document
    {
        $originalData = [
            'title' => $document->getTitle(),
            'content' => $document->getContent(),
            'description' => $document->getDescription(),
            'metadata' => $document->getMetadata(),
        ];
        
        $changes = [];
        
        // Update properties
        if (isset($data['title']) && $data['title'] !== $document->getTitle()) {
            $document->setTitle($data['title']);
            $changes['title'] = ['old' => $originalData['title'], 'new' => $data['title']];
        }
        
        if (isset($data['description']) && $data['description'] !== $document->getDescription()) {
            $document->setDescription($data['description']);
            $changes['description'] = ['old' => $originalData['description'], 'new' => $data['description']];
        }
        
        if (isset($data['content']) && $data['content'] !== $document->getContent()) {
            $document->setContent($data['content']);
            $changes['content'] = ['old' => $originalData['content'], 'new' => $data['content']];
        }
        
        if (isset($data['metadata']) && $data['metadata'] !== $document->getMetadata()) {
            $document->setMetadata($data['metadata']);
            $changes['metadata'] = ['old' => $originalData['metadata'], 'new' => $data['metadata']];
        }
        
        if (isset($data['signature_fields'])) {
            $document->setSignatureFields($data['signature_fields']);
        }
        
        if (isset($data['expires_at'])) {
            $expiresAt = new \DateTimeImmutable($data['expires_at']);
            $document->setExpiresAt($expiresAt);
        }
        
        // Create version if there are changes
        if (!empty($changes)) {
            $this->createVersion($document, $updatedBy, 'Document updated', $changes);
        }
        
        $this->entityManager->flush();
        
        return $document;
    }

    /**
     * Send document for signature
     */
    public function sendForSignature(Document $document, User $sentBy): Document
    {
        if ($document->getStatus() !== 'draft') {
            throw new \InvalidArgumentException('Document must be in draft status to send for signature');
        }
        
        if (!$document->isRequiresSignature()) {
            throw new \InvalidArgumentException('Document does not require signature');
        }
        
        $document->setStatus('ready_for_signature');
        $document->setSentForSignatureAt(new \DateTimeImmutable());
        
        // Create signature records for all required signers
        $signatureFields = $document->getSignatureFields() ?? [];
        foreach ($signatureFields as $field) {
            if (isset($field['required']) && $field['required']) {
                $signature = new DocumentSignature();
                $signature->setDocument($document);
                $signature->setStatus('pending');
                
                // Find the user who needs to sign
                if (isset($field['signer_email'])) {
                    // In a real implementation, you'd look up the user by email
                    // For now, we'll create a pending signature
                }
                
                $this->entityManager->persist($signature);
            }
        }
        
        $this->entityManager->flush();
        
        return $document;
    }

    /**
     * Sign a document
     */
    public function signDocument(Document $document, User $signer, array $signatureData): DocumentSignature
    {
        if ($document->getStatus() !== 'ready_for_signature') {
            throw new \InvalidArgumentException('Document is not ready for signature');
        }
        
        // Find or create signature record
        $signature = $this->signatureRepository->findOneBy([
            'document' => $document,
            'signedBy' => $signer,
            'status' => 'pending'
        ]);
        
        if (!$signature) {
            $signature = new DocumentSignature();
            $signature->setDocument($document);
            $signature->setSignedBy($signer);
        }
        
        // Set signature data
        $signature->setSignatureData($signatureData['signature_data'] ?? null);
        $signature->setSignatureImage($signatureData['signature_image'] ?? null);
        $signature->setStatus('signed');
        $signature->setSignedAt(new \DateTimeImmutable());
        $signature->setIpAddress($signatureData['ip_address'] ?? null);
        $signature->setUserAgent($signatureData['user_agent'] ?? null);
        $signature->setComments($signatureData['comments'] ?? null);
        
        if (isset($signatureData['metadata'])) {
            $signature->setMetadata($signatureData['metadata']);
        }
        
        $this->entityManager->persist($signature);
        
        // Check if all required signatures are complete
        $this->checkDocumentSignatureStatus($document);
        
        $this->entityManager->flush();
        
        return $signature;
    }

    /**
     * Create a document from template
     */
    public function createFromTemplate(DocumentTemplate $template, array $data, User $createdBy): Document
    {
        $documentData = [
            'title' => $data['title'] ?? $template->getName(),
            'description' => $data['description'] ?? $template->getDescription(),
            'type' => $template->getType(),
            'template_id' => $template->getId(),
            'template_variables' => $data['template_variables'] ?? [],
            'requires_signature' => $template->isRequiresSignature(),
            'signature_fields' => $template->getSignatureFields(),
            'metadata' => array_merge($template->getMetadata() ?? [], $data['metadata'] ?? []),
        ];
        
        if (isset($data['client_id'])) {
            $documentData['client_id'] = $data['client_id'];
        }
        
        return $this->createDocument($documentData, $createdBy);
    }

    /**
     * Get documents for a client
     */
    public function getDocumentsForClient(Client $client, array $filters = []): array
    {
        return $this->documentRepository->findByClient($client, $filters);
    }

    /**
     * Get documents ready for signature
     */
    public function getDocumentsReadyForSignature(Client $client = null): array
    {
        return $this->documentRepository->findReadyForSignature($client);
    }

    /**
     * Archive a document
     */
    public function archiveDocument(Document $document, User $archivedBy): Document
    {
        $document->setStatus('archived');
        $this->createVersion($document, $archivedBy, 'Document archived');
        $this->entityManager->flush();
        
        return $document;
    }

    /**
     * Create a new version of a document
     */
    private function createVersion(Document $document, User $createdBy, string $description, array $changes = []): DocumentVersion
    {
        // Mark all existing versions as not current
        foreach ($document->getVersions() as $version) {
            $version->setIsCurrent(false);
        }
        
        // Create new version
        $version = new DocumentVersion();
        $version->setDocument($document);
        $version->setCreatedBy($createdBy);
        $version->setVersionNumber($document->getVersions()->count() + 1);
        $version->setTitle($document->getTitle());
        $version->setDescription($description);
        $version->setContent($document->getContent());
        $version->setFile($document->getFile());
        $version->setMetadata($document->getMetadata());
        $version->setChanges($changes);
        $version->setIsCurrent(true);
        
        $document->addVersion($version);
        $this->entityManager->persist($version);
        
        return $version;
    }

    /**
     * Check if all required signatures are complete
     */
    private function checkDocumentSignatureStatus(Document $document): void
    {
        $signatureFields = $document->getSignatureFields() ?? [];
        $requiredSignatures = 0;
        $completedSignatures = 0;
        
        foreach ($signatureFields as $field) {
            if (isset($field['required']) && $field['required']) {
                $requiredSignatures++;
                
                // Check if signature exists and is signed
                $signature = $this->signatureRepository->findOneBy([
                    'document' => $document,
                    'status' => 'signed'
                ]);
                
                if ($signature) {
                    $completedSignatures++;
                }
            }
        }
        
        // If all required signatures are complete, mark document as signed
        if ($requiredSignatures > 0 && $completedSignatures >= $requiredSignatures) {
            $document->setStatus('signed');
            $document->setSignedAt(new \DateTimeImmutable());
        }
    }

    /**
     * Get signature status for a document
     */
    public function getSignatureStatus(Document $document): array
    {
        $signatures = $this->signatureRepository->findBy(['document' => $document]);
        $signatureFields = $document->getSignatureFields() ?? [];
        
        $status = [
            'total_required' => 0,
            'completed' => 0,
            'pending' => 0,
            'signatures' => []
        ];
        
        foreach ($signatureFields as $field) {
            if (isset($field['required']) && $field['required']) {
                $status['total_required']++;
            }
        }
        
        foreach ($signatures as $signature) {
            $status['signatures'][] = $signature->getSignatureSummary();
            
            if ($signature->isSigned()) {
                $status['completed']++;
            } else {
                $status['pending']++;
            }
        }
        
        return $status;
    }
}
