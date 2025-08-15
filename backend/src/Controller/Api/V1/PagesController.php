<?php

namespace App\Controller\Api\V1;

use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/pages')]
class PagesController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {}

    #[Route('', name: 'api_v1_pages_list', methods: ['GET'])]
    public function listPages(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'sort_order');
        $type = $request->query->get('type', '');
        $status = $request->query->get('status', 'published');
        $slug = $request->query->get('slug', '');

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
        if ($slug) {
            $criteria['slug'] = $slug;
        }

        // Get pages with pagination and filtering
        $pages = $this->pageRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalPages = $this->pageRepository->countByCriteria($criteria);

        $pageData = [];
        foreach ($pages as $pageEntity) {
            $pageData[] = [
                'id' => $pageEntity->getId(),
                'title' => $pageEntity->getTitle(),
                'slug' => $pageEntity->getSlug(),
                'excerpt' => $pageEntity->getExcerpt(),
                'content' => $pageEntity->getContent(),
                'meta_title' => $pageEntity->getMetaTitle(),
                'meta_description' => $pageEntity->getMetaDescription(),
                'meta_keywords' => $pageEntity->getMetaKeywords(),
                'featured_image' => $pageEntity->getFeaturedImage(),
                'type' => $pageEntity->getType(),
                'status' => $pageEntity->getStatus(),
                'sort_order' => $pageEntity->getSortOrder(),
                'published_at' => $pageEntity->getPublishedAt()?->format('c'),
                'created_at' => $pageEntity->getCreatedAt()->format('c'),
                'updated_at' => $pageEntity->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $pageData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalPages,
                'pages' => ceil($totalPages / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_pages_get', methods: ['GET'])]
    public function getPage(string $id): JsonResponse
    {
        $page = $this->pageRepository->find($id);
        if (!$page) {
            return $this->json(['error' => 'Page not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if page is published
        if ($page->getStatus() !== 'published') {
            return $this->json(['error' => 'Page not found'], Response::HTTP_NOT_FOUND);
        }

        $pageData = [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'slug' => $page->getSlug(),
            'excerpt' => $page->getExcerpt(),
            'content' => $page->getContent(),
            'meta_title' => $page->getMetaTitle(),
            'meta_description' => $page->getMetaDescription(),
            'meta_keywords' => $page->getMetaKeywords(),
            'featured_image' => $page->getFeaturedImage(),
            'type' => $page->getType(),
            'status' => $page->getStatus(),
            'sort_order' => $page->getSortOrder(),
            'metadata' => $page->getMetadata(),
            'seo_settings' => $page->getSeoSettings(),
            'published_at' => $page->getPublishedAt()?->format('c'),
            'created_at' => $page->getCreatedAt()->format('c'),
            'updated_at' => $page->getUpdatedAt()->format('c')
        ];

        return $this->json($pageData);
    }
}
