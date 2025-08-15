<?php

namespace App\Controller\Api\V1;

use App\Entity\Faq;
use App\Repository\FaqRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/faqs')]
class FaqsController extends AbstractController
{
    public function __construct(
        private FaqRepository $faqRepository
    ) {}

    #[Route('', name: 'api_v1_faqs_list', methods: ['GET'])]
    public function listFaqs(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'sort_order');
        $category = $request->query->get('category', '');
        $search = $request->query->get('search', '');
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
        $criteria = ['isActive' => true];
        if ($category) {
            $criteria['category'] = $category;
        }
        if ($search) {
            $criteria['search'] = $search;
        }
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }

        // Get FAQs with pagination and filtering
        $faqs = $this->faqRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalFaqs = $this->faqRepository->countByCriteria($criteria);

        $faqData = [];
        foreach ($faqs as $faq) {
            $faqData[] = [
                'id' => $faq->getId(),
                'question' => $faq->getQuestion(),
                'answer' => $faq->getAnswer(),
                'sort' => $faq->getSort(),
                'is_active' => $faq->isActive(),
                'created_at' => $faq->getCreatedAt()->format('c'),
                'updated_at' => $faq->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $faqData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalFaqs,
                'pages' => ceil($totalFaqs / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_faqs_get', methods: ['GET'])]
    public function getFaq(string $id): JsonResponse
    {
        $faq = $this->faqRepository->find($id);
        if (!$faq) {
            return $this->json(['error' => 'FAQ not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if FAQ is active
        if (!$faq->isActive()) {
            return $this->json(['error' => 'FAQ not found'], Response::HTTP_NOT_FOUND);
        }

        $faqData = [
            'id' => $faq->getId(),
            'question' => $faq->getQuestion(),
            'answer' => $faq->getAnswer(),
            'sort' => $faq->getSort(),
            'is_active' => $faq->isActive(),
            'created_at' => $faq->getCreatedAt()->format('c'),
            'updated_at' => $faq->getUpdatedAt()->format('c')
        ];

        return $this->json($faqData);
    }
}
