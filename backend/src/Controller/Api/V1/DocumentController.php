<?php

namespace App\Controller\Api\V1;

use App\Entity\Document;
use App\Entity\DocumentTemplate;
use App\Entity\Client;
use App\Service\DocumentService;
use App\Repository\DocumentRepository;
use App\Repository\DocumentTemplateRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/v1/documents')]
class DocumentController extends AbstractController
{
    private DocumentService $documentService;
    private DocumentRepository $documentRepository;
    private DocumentTemplateRepository $templateRepository;
    private ClientRepository $clientRepository;
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(
        DocumentService $documentService,
        DocumentRepository $documentRepository,
        DocumentTemplateRepository $templateRepository,
        ClientRepository $clientRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->documentService = $documentService;
        $this->documentRepository = $documentRepository;
        $this->templateRepository = $templateRepository;
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'api_v1_documents_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function list(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->query->get('status'),
                'type' => $request->query->get('type'),
                'client_id' => $request->query->get('client_id'),
                'search' => $request->query->get('search'),
            ];

            $filters = array_filter($filters, fn($value) => $value !== null);

            $documents = $this->documentRepository->findByFilters($filters);

            $data = $this->serializer->serialize($documents, 'json', [
                'groups' => ['document:read']
            ]);

            return new JsonResponse([
                'documents' => json_decode($data, true),
                'total' => count($documents)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to retrieve documents: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_documents_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function get(string $id): JsonResponse
    {
        try {
            $document = $this->documentRepository->find($id);
            
            if (!$document) {
                return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            $data = $this->serializer->serialize($document, 'json', [
                'groups' => ['document:read', 'document:detail']
            ]);

            return new JsonResponse(json_decode($data, true));

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to retrieve document: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'api_v1_documents_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $document = $this->documentService->createDocument($data, $user);

            $responseData = $this->serializer->serialize($document, 'json', [
                'groups' => ['document:read', 'document:detail']
            ]);

            return new JsonResponse([
                'message' => 'Document created successfully',
                'document' => json_decode($responseData, true)
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => 'Validation failed: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to create document: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_documents_update', methods: ['PUT'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function update(string $id, Request $request): JsonResponse
    {
        try {
            $document = $this->documentRepository->find($id);
            
            if (!$document) {
                return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $document = $this->documentService->updateDocument($document, $data, $user);

            $responseData = $this->serializer->serialize($document, 'json', [
                'groups' => ['document:read', 'document:detail']
            ]);

            return new JsonResponse([
                'message' => 'Document updated successfully',
                'document' => json_decode($responseData, true)
            ]);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => 'Validation failed: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to update document: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/send-for-signature', name: 'api_v1_documents_send_for_signature', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function sendForSignature(string $id): JsonResponse
    {
        try {
            $document = $this->documentRepository->find($id);
            
            if (!$document) {
                return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            $user = $this->getUser();
            $document = $this->documentService->sendForSignature($document, $user);

            return new JsonResponse([
                'message' => 'Document sent for signature successfully',
                'document_id' => $document->getId(),
                'status' => $document->getStatus()
            ]);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to send document for signature: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/sign', name: 'api_v1_documents_sign', methods: ['POST'])]
    #[IsGranted('ROLE_CLIENT_USER')]
    public function sign(string $id, Request $request): JsonResponse
    {
        try {
            $document = $this->documentRepository->find($id);
            
            if (!$document) {
                return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Add request metadata
            $data['ip_address'] = $request->getClientIp();
            $data['user_agent'] = $request->headers->get('User-Agent');

            $user = $this->getUser();
            $signature = $this->documentService->signDocument($document, $user, $data);

            return new JsonResponse([
                'message' => 'Document signed successfully',
                'signature_id' => $signature->getId(),
                'document_status' => $document->getStatus()
            ]);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to sign document: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/signature-status', name: 'api_v1_documents_signature_status', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getSignatureStatus(string $id): JsonResponse
    {
        try {
            $document = $this->documentRepository->find($id);
            
            if (!$document) {
                return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            $status = $this->documentService->getSignatureStatus($document);

            return new JsonResponse($status);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to get signature status: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/archive', name: 'api_v1_documents_archive', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function archive(string $id): JsonResponse
    {
        try {
            $document = $this->documentRepository->find($id);
            
            if (!$document) {
                return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            $user = $this->getUser();
            $document = $this->documentService->archiveDocument($document, $user);

            return new JsonResponse([
                'message' => 'Document archived successfully',
                'document_id' => $document->getId(),
                'status' => $document->getStatus()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to archive document: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/templates/{templateId}/create', name: 'api_v1_documents_create_from_template', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createFromTemplate(string $templateId, Request $request): JsonResponse
    {
        try {
            $template = $this->templateRepository->find($templateId);
            
            if (!$template) {
                return new JsonResponse(['error' => 'Template not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $document = $this->documentService->createFromTemplate($template, $data, $user);

            $responseData = $this->serializer->serialize($document, 'json', [
                'groups' => ['document:read', 'document:detail']
            ]);

            return new JsonResponse([
                'message' => 'Document created from template successfully',
                'document' => json_decode($responseData, true)
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => 'Validation failed: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to create document from template: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/client/{clientId}', name: 'api_v1_documents_for_client', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getDocumentsForClient(string $clientId): JsonResponse
    {
        try {
            $client = $this->clientRepository->find($clientId);
            
            if (!$client) {
                return new JsonResponse(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
            }

            $documents = $this->documentService->getDocumentsForClient($client);

            $data = $this->serializer->serialize($documents, 'json', [
                'groups' => ['document:read']
            ]);

            return new JsonResponse([
                'documents' => json_decode($data, true),
                'client_id' => $clientId
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to retrieve client documents: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/ready-for-signature', name: 'api_v1_documents_ready_for_signature', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getDocumentsReadyForSignature(Request $request): JsonResponse
    {
        try {
            $clientId = $request->query->get('client_id');
            $client = null;
            
            if ($clientId) {
                $client = $this->clientRepository->find($clientId);
                if (!$client) {
                    return new JsonResponse(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
                }
            }

            $documents = $this->documentService->getDocumentsReadyForSignature($client);

            $data = $this->serializer->serialize($documents, 'json', [
                'groups' => ['document:read']
            ]);

            return new JsonResponse([
                'documents' => json_decode($data, true),
                'total' => count($documents)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to retrieve documents ready for signature: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
