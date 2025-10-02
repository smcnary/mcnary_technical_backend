<?php

namespace App\Command;

use App\Service\TwilioService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-twilio',
    description: 'Test Twilio integration by making a call to 786-213-3333'
)]
class TestTwilioCommand extends Command
{
    public function __construct(
        private TwilioService $twilioService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('call', 'c', InputOption::VALUE_NONE, 'Make a test call to 786-213-3333')
            ->addOption('sms', 's', InputOption::VALUE_OPTIONAL, 'Send a test SMS to 786-213-3333', 'Hello from CounselRank Legal Services!')
            ->addOption('connection', null, InputOption::VALUE_NONE, 'Test Twilio connection')
            ->addOption('info', 'i', InputOption::VALUE_NONE, 'Show Twilio phone information');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('connection')) {
            return $this->testConnection($io);
        }

        if ($input->getOption('info')) {
            return $this->showPhoneInfo($io);
        }

        if ($input->getOption('call')) {
            return $this->makeTestCall($io);
        }

        if ($input->getOption('sms')) {
            $message = $input->getOption('sms');
            return $this->sendTestSms($io, $message);
        }

        // Default: show all options
        $io->title('Twilio Test Command');
        $io->text('Available options:');
        $io->listing([
            '--connection: Test Twilio connection',
            '--info: Show phone number information',
            '--call: Make a test call to 786-213-3333',
            '--sms [message]: Send test SMS to 786-213-3333'
        ]);

        return Command::SUCCESS;
    }

    private function testConnection(SymfonyStyle $io): int
    {
        $io->section('Testing Twilio Connection');
        
        $result = $this->twilioService->testConnection();
        
        if ($result['success']) {
            $io->success('Twilio connection successful!');
            $io->table(['Property', 'Value'], [
                ['Account SID', $result['accountSid']],
                ['Friendly Name', $result['friendlyName']],
                ['Status', $result['status']]
            ]);
            return Command::SUCCESS;
        }

        $io->error('Twilio connection failed: ' . $result['error']);
        return Command::FAILURE;
    }

    private function showPhoneInfo(SymfonyStyle $io): int
    {
        $io->section('Twilio Phone Information');
        
        $twilioPhone = $this->twilioService->getTwilioPhoneNumber();
        
        $io->table(['Property', 'Value'], [
            ['Twilio Phone Number', $twilioPhone],
            ['Target Phone Number', '+17862133333'],
            ['Target Phone (Formatted)', '786-213-3333']
        ]);

        return Command::SUCCESS;
    }

    private function makeTestCall(SymfonyStyle $io): int
    {
        $io->section('Making Test Call to 786-213-3333');
        
        $twiml = '<Response><Say>Hello! This is a test call from CounselRank Legal Services. Thank you for your interest in our services. This call will end in 5 seconds.</Say><Pause length="5"/></Response>';
        
        $result = $this->twilioService->makeCallToTargetNumber(null, $twiml);
        
        if ($result['success']) {
            $io->success('Call initiated successfully!');
            $io->table(['Property', 'Value'], [
                ['Call SID', $result['callSid']],
                ['Status', $result['status']],
                ['From', $result['from']],
                ['To', $result['to']],
                ['Direction', $result['direction']]
            ]);
            
            $io->note('You can check call details with: php bin/console app:twilio-call-details ' . $result['callSid']);
            return Command::SUCCESS;
        }

        $io->error('Failed to make call: ' . $result['error']);
        return Command::FAILURE;
    }

    private function sendTestSms(SymfonyStyle $io, string $message): int
    {
        $io->section('Sending Test SMS to 786-213-3333');
        
        $result = $this->twilioService->sendSmsToTargetNumber($message);
        
        if ($result['success']) {
            $io->success('SMS sent successfully!');
            $io->table(['Property', 'Value'], [
                ['Message SID', $result['messageSid']],
                ['Status', $result['status']],
                ['From', $result['from']],
                ['To', $result['to']],
                ['Message', $result['body']]
            ]);
            
            $io->note('You can check message details with: php bin/console app:twilio-message-details ' . $result['messageSid']);
            return Command::SUCCESS;
        }

        $io->error('Failed to send SMS: ' . $result['error']);
        return Command::FAILURE;
    }
}
