<?php

namespace App\Controller\Api\V1;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/invoices')]
class InvoicesController extends AbstractController
{
    public function __construct(
        private InvoiceRepository $invoiceRepository
    ) {}

    #[Route('', name: 'api_v1_invoices_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listInvoices(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $subscriptionId = $request->query->get('subscription_id', '');
        $status = $request->query->get('status', '');

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
        $criteria = [];
        if ($subscriptionId) {
            $criteria['subscription_id'] = $subscriptionId;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        // Get invoices with pagination and filtering
        $invoices = $this->invoiceRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalInvoices = $this->invoiceRepository->countByCriteria($criteria);

        $invoiceData = [];
        foreach ($invoices as $invoice) {
            $invoiceData[] = [
                'id' => $invoice->getId(),
                'subscription_id' => $invoice->getSubscriptionId(),
                'invoice_number' => $invoice->getInvoiceNumber(),
                'status' => $invoice->getStatus(),
                'amount' => $invoice->getAmount(),
                'currency' => $invoice->getCurrency(),
                'due_date' => $invoice->getDueDate()?->format('Y-m-d'),
                'paid_date' => $invoice->getPaidDate()?->format('Y-m-d'),
                'stripe_invoice_id' => $invoice->getStripeInvoiceId(),
                'created_at' => $invoice->getCreatedAt()->format('c'),
                'updated_at' => $invoice->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $invoiceData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalInvoices,
                'pages' => ceil($totalInvoices / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_invoices_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getInvoice(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $invoice = $this->invoiceRepository->find($id);
        if (!$invoice) {
            return $this->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $invoiceData = [
            'id' => $invoice->getId(),
            'subscription_id' => $invoice->getSubscriptionId(),
            'invoice_number' => $invoice->getInvoiceNumber(),
            'status' => $invoice->getStatus(),
            'amount' => $invoice->getAmount(),
            'currency' => $invoice->getCurrency(),
            'due_date' => $invoice->getDueDate()?->format('Y-m-d'),
            'paid_date' => $invoice->getPaidDate()?->format('Y-m-d'),
            'stripe_invoice_id' => $invoice->getStripeInvoiceId(),
            'metadata' => $invoice->getMetadata(),
            'created_at' => $invoice->getCreatedAt()->format('c'),
            'updated_at' => $invoice->getUpdatedAt()->format('c')
        ];

        return $this->json($invoiceData);
    }
}
