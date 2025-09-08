<?php

namespace App\Command;

use App\Service\MonitoringService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:monitoring:report',
    description: 'Generate monitoring and performance report'
)]
class MonitoringReportCommand extends Command
{
    private MonitoringService $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format (json, table)', 'table')
            ->addOption('health-only', null, InputOption::VALUE_NONE, 'Show only health status')
            ->addOption('metrics-only', null, InputOption::VALUE_NONE, 'Show only performance metrics');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getOption('format');
        $healthOnly = $input->getOption('health-only');
        $metricsOnly = $input->getOption('metrics-only');

        if ($format === 'json') {
            $this->outputJson($io, $healthOnly, $metricsOnly);
        } else {
            $this->outputTable($io, $healthOnly, $metricsOnly);
        }

        return Command::SUCCESS;
    }

    private function outputJson(SymfonyStyle $io, bool $healthOnly, bool $metricsOnly): void
    {
        $data = [];

        if (!$metricsOnly) {
            $data['health'] = $this->monitoringService->getHealthStatus();
        }

        if (!$healthOnly) {
            $data['metrics'] = $this->monitoringService->getPerformanceMetrics();
        }

        $io->writeln(json_encode($data, JSON_PRETTY_PRINT));
    }

    private function outputTable(SymfonyStyle $io, bool $healthOnly, bool $metricsOnly): void
    {
        if (!$metricsOnly) {
            $this->displayHealthStatus($io);
        }

        if (!$healthOnly) {
            $this->displayPerformanceMetrics($io);
        }
    }

    private function displayHealthStatus(SymfonyStyle $io): void
    {
        $io->title('Health Status');
        
        $healthStatus = $this->monitoringService->getHealthStatus();
        
        $io->section('Overall Status');
        $statusIcon = $healthStatus['status'] === 'healthy' ? '✅' : '❌';
        $io->writeln("{$statusIcon} Status: {$healthStatus['status']}");
        
        $io->section('Service Checks');
        $checks = [];
        foreach ($healthStatus['checks'] as $service => $check) {
            $icon = $check['status'] === 'healthy' ? '✅' : '❌';
            $checks[] = [
                'Service' => $service,
                'Status' => "{$icon} {$check['status']}",
                'Details' => $this->formatCheckDetails($check)
            ];
        }
        
        $io->table(['Service', 'Status', 'Details'], $checks);
    }

    private function displayPerformanceMetrics(SymfonyStyle $io): void
    {
        $io->title('Performance Metrics');
        
        $metrics = $this->monitoringService->getPerformanceMetrics();
        
        $io->section('Memory Usage');
        $memoryData = $metrics['memory'];
        $io->table(['Metric', 'Value'], [
            ['Current Memory', $this->formatBytes($memoryData['current'])],
            ['Peak Memory', $this->formatBytes($memoryData['peak'])],
            ['Memory Limit', $memoryData['limit']]
        ]);
        
        $io->section('System Information');
        $io->table(['Metric', 'Value'], [
            ['PHP Version', $metrics['php']['version']],
            ['SAPI', $metrics['php']['sapi']],
            ['OPcache Enabled', $metrics['opcache']['enabled'] ? 'Yes' : 'No'],
            ['System Uptime', $metrics['system']['uptime'] ? $this->formatUptime($metrics['system']['uptime']) : 'N/A']
        ]);
        
        if ($metrics['system']['load_average']) {
            $io->section('Load Average');
            $loadAvg = $metrics['system']['load_average'];
            $io->table(['Period', 'Load'], [
                ['1 minute', $loadAvg[0]],
                ['5 minutes', $loadAvg[1]],
                ['15 minutes', $loadAvg[2]]
            ]);
        }
    }

    private function formatCheckDetails(array $check): string
    {
        $details = [];
        
        if (isset($check['response_time'])) {
            $details[] = "Response: " . round($check['response_time'] * 1000, 2) . "ms";
        }
        
        if (isset($check['usage_percent'])) {
            $details[] = "Usage: " . $check['usage_percent'] . "%";
        }
        
        if (isset($check['error'])) {
            $details[] = "Error: " . $check['error'];
        }
        
        return implode(', ', $details);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%dd %dh %dm', $days, $hours, $minutes);
    }
}
