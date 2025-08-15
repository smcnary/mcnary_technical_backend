<?php

namespace App\Controller\Api\V1;

use App\Entity\MediaAsset;
use App\Repository\MediaAssetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/media-assets')]
class MediaAssetsController extends AbstractController
{
    public function __construct(
        private MediaAssetRepository $mediaAssetRepository
    ) {}

    #[Route('', name: 'api_v1_media_assets_list', methods: ['GET'])]
    public function listMediaAssets(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $type = $request->query->get('type', '');
        $status = $request->query->get('status', 'active');
        $clientId = $request->query->get('client_id', '');

        // Parse sort parameter
        $sortFields = [];
        foreach (explode(',', $sort) as $field) {
            $direction = 'ASC';
            if (str_starts_with($field, '-')) {
                $direction = 'DESC';
                $field = substr($field, 1);
            }
            $sortFields[$field] = $direction;
        }

        // Build criteria
        $criteria = ['status' => $status];
        if ($type) {
            $criteria['type'] = $type;
        }
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }

        // Get media assets with pagination and filtering
        $mediaAssets = $this->mediaAssetRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalMediaAssets = $this->mediaAssetRepository->countByCriteria($criteria);

        $mediaAssetData = [];
        foreach ($mediaAssets as $mediaAsset) {
            $mediaAssetData[] = [
                'id' => $mediaAsset->getId(),
                'filename' => $mediaAsset->getFilename(),
                'original_filename' => $mediaAsset->getOriginalFilename(),
                'mime_type' => $mediaAsset->getMimeType(),
                'file_size' => $mediaAsset->getFileSize(),
                'file_size_formatted' => $mediaAsset->getFileSizeFormatted(),
                'title' => $mediaAsset->getTitle(),
                'description' => $mediaAsset->getDescription(),
                'alt_text' => $mediaAsset->getAltText(),
                'type' => $mediaAsset->getType(),
                'dimensions' => $mediaAsset->getDimensions(),
                'width' => $mediaAsset->getWidth(),
                'height' => $mediaAsset->getHeight(),
                'status' => $mediaAsset->getStatus(),
                'stream_url' => $mediaAsset->getStreamUrl(),
                'thumbnail_url' => $mediaAsset->getThumbnailUrl(),
                'uploaded_at' => $mediaAsset->getUploadedAt()->format('c'),
                'created_at' => $mediaAsset->getCreatedAt()->format('c'),
                'updated_at' => $mediaAsset->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $mediaAssetData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalMediaAssets,
                'pages' => ceil($totalMediaAssets / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_media_assets_get', methods: ['GET'])]
    public function getMediaAsset(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $mediaAsset = $this->mediaAssetRepository->find($id);
        if (!$mediaAsset) {
            return $this->json(['error' => 'Media asset not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if media asset is active
        if ($mediaAsset->getStatus() !== 'active') {
            return $this->json(['error' => 'Media asset not found'], Response::HTTP_NOT_FOUND);
        }

        $mediaAssetData = [
            'id' => $mediaAsset->getId(),
            'filename' => $mediaAsset->getFilename(),
            'original_filename' => $mediaAsset->getOriginalFilename(),
            'mime_type' => $mediaAsset->getMimeType(),
            'file_size' => $mediaAsset->getFileSize(),
            'file_size_formatted' => $mediaAsset->getFileSizeFormatted(),
            'title' => $mediaAsset->getTitle(),
            'description' => $mediaAsset->getDescription(),
            'alt_text' => $mediaAsset->getAltText(),
            'type' => $mediaAsset->getType(),
            'dimensions' => $mediaAsset->getDimensions(),
            'width' => $mediaAsset->getWidth(),
            'height' => $mediaAsset->getHeight(),
            'status' => $mediaAsset->getStatus(),
            'stream_url' => $mediaAsset->getStreamUrl(),
            'thumbnail_url' => $mediaAsset->getThumbnailUrl(),
            'metadata' => $mediaAsset->getMetadata(),
            'processing_status' => $mediaAsset->getProcessingStatus(),
            'uploaded_at' => $mediaAsset->getUploadedAt()->format('c'),
            'created_at' => $mediaAsset->getCreatedAt()->format('c'),
            'updated_at' => $mediaAsset->getUpdatedAt()->format('c')
        ];

        return $this->json($mediaAssetData);
    }
}
