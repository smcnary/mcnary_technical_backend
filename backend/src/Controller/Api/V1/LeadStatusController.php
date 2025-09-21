<?php

namespace App\Controller\Api\V1;

use App\ValueObject\LeadStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/lead-status', name: 'api_v1_lead_status_')]
class LeadStatusController extends AbstractController
{
    #[Route('/options', name: 'options', methods: ['GET'])]
    public function getStatusOptions(): JsonResponse
    {
        $options = [];
        foreach (LeadStatus::cases() as $status) {
            $options[] = [
                'value' => $status->value,
                'label' => $status->getLabel(),
                'description' => $status->getDescription(),
                'stage' => $this->getStageInfo($status),
            ];
        }

        return $this->json([
            'status_options' => $options,
        ]);
    }

    #[Route('/stages', name: 'stages', methods: ['GET'])]
    public function getStages(): JsonResponse
    {
        $stages = [
            'early_stage' => [
                'name' => 'Early Stage',
                'description' => 'Initial contact and qualification',
                'statuses' => [],
            ],
            'interview_stage' => [
                'name' => 'Interview Stage',
                'description' => 'Interview scheduling and completion',
                'statuses' => [],
            ],
            'application_stage' => [
                'name' => 'Application Stage',
                'description' => 'Application received and processing',
                'statuses' => [],
            ],
            'audit_stage' => [
                'name' => 'Audit Stage',
                'description' => 'Audit in progress and completion',
                'statuses' => [],
            ],
            'final_stage' => [
                'name' => 'Final Stage',
                'description' => 'Client enrollment',
                'statuses' => [],
            ],
        ];

        foreach (LeadStatus::cases() as $status) {
            if ($status->isEarlyStage()) {
                $stages['early_stage']['statuses'][] = $status->value;
            } elseif ($status->isInterviewStage()) {
                $stages['interview_stage']['statuses'][] = $status->value;
            } elseif ($status->isApplicationStage()) {
                $stages['application_stage']['statuses'][] = $status->value;
            } elseif ($status->isAuditStage()) {
                $stages['audit_stage']['statuses'][] = $status->value;
            } elseif ($status->isFinalStage()) {
                $stages['final_stage']['statuses'][] = $status->value;
            }
        }

        return $this->json([
            'stages' => $stages,
        ]);
    }

    private function getStageInfo(LeadStatus $status): string
    {
        if ($status->isEarlyStage()) {
            return 'early_stage';
        } elseif ($status->isInterviewStage()) {
            return 'interview_stage';
        } elseif ($status->isApplicationStage()) {
            return 'application_stage';
        } elseif ($status->isAuditStage()) {
            return 'audit_stage';
        } elseif ($status->isFinalStage()) {
            return 'final_stage';
        }

        return 'unknown';
    }
}
