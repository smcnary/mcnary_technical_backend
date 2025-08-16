<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/uploads')]
class UploadsController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    #[Route('/sign', name: 'api_v1_uploads_sign', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function signUpload(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'filename' => [new Assert\NotBlank()],
                'content_type' => [new Assert\NotBlank()],
                'file_size' => [new Assert\NotBlank(), new Assert\Positive()],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'folder' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Generate unique filename
            $extension = pathinfo($data['filename'], PATHINFO_EXTENSION);
            $uniqueFilename = uniqid() . '_' . time() . '.' . $extension;
            
            // Build S3 key
            $folder = $data['folder'] ?? 'uploads';
            $s3Key = "{$folder}/{$uniqueFilename}";

            // Here you would typically generate a pre-signed S3 PUT URL
            // For now, we'll return a mock response
            $uploadData = [
                'upload_id' => uniqid(),
                'filename' => $uniqueFilename,
                's3_key' => $s3Key,
                'presigned_url' => "https://mock-s3-bucket.s3.amazonaws.com/{$s3Key}?presigned=url",
                'fields' => [
                    'key' => $s3Key,
                    'Content-Type' => $data['content_type'],
                    'success_action_status' => '201'
                ],
                'expires_in' => 3600, // 1 hour
                'created_at' => (new \DateTimeImmutable())->format('c')
            ];

            return $this->json([
                'message' => 'Upload signed successfully',
                'upload' => $uploadData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/media-assets', name: 'api_v1_uploads_media_assets', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function registerMediaAsset(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'filename' => [new Assert\NotBlank()],
                'original_filename' => [new Assert\NotBlank()],
                'mime_type' => [new Assert\NotBlank()],
                'file_size' => [new Assert\NotBlank(), new Assert\Positive()],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'title' => [new Assert\Optional([new Assert\NotBlank()])],
                'description' => [new Assert\Optional([new Assert\NotBlank()])],
                'alt_text' => [new Assert\Optional([new Assert\NotBlank()])],
                'type' => [new Assert\Optional([new Assert\Choice(['image', 'video', 'document', 'audio'])])],
                'tags' => [new Assert\Optional([new Assert\Type('array')])],
                'metadata' => [new Assert\Optional([new Assert\Type('array')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Here you would typically create a MediaAsset entity and save it
            // For now, we'll return a mock response
            $mediaAssetData = [
                'id' => uniqid(),
                'filename' => $data['filename'],
                'original_filename' => $data['original_filename'],
                'mime_type' => $data['mime_type'],
                'file_size' => $data['file_size'],
                'file_size_formatted' => $this->formatFileSize($data['file_size']),
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'alt_text' => $data['alt_text'] ?? null,
                'type' => $data['type'] ?? 'image',
                'tags' => $data['tags'] ?? [],
                'metadata' => $data['metadata'] ?? [],
                'status' => 'active',
                'stream_url' => "https://cdn.example.com/{$data['filename']}",
                'thumbnail_url' => $data['type'] === 'image' ? "https://cdn.example.com/thumbnails/{$data['filename']}" : null,
                'processing_status' => 'completed',
                'uploaded_at' => (new \DateTimeImmutable())->format('c'),
                'created_at' => (new \DateTimeImmutable())->format('c'),
                'updated_at' => (new \DateTimeImmutable())->format('c')
            ];

            return $this->json([
                'message' => 'Media asset registered successfully',
                'media_asset' => $mediaAssetData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
