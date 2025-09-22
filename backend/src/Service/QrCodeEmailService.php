<?php

namespace App\Service;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\QrCode;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Psr\Log\LoggerInterface;

class QrCodeEmailService
{
    private BuilderInterface $qrCodeBuilder;
    private MailerInterface $mailer;
    private RouterInterface $router;
    private LoggerInterface $logger;
    private string $frontendUrl;

    public function __construct(
        BuilderInterface $qrCodeBuilder,
        MailerInterface $mailer,
        RouterInterface $router,
        LoggerInterface $logger,
        string $appFrontendUrl
    ) {
        $this->qrCodeBuilder = $qrCodeBuilder;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->logger = $logger;
        $this->frontendUrl = $appFrontendUrl;
    }

    /**
     * Send an email with QR code that routes to the audit wizard
     */
    public function sendAuditWizardQrEmail(
        string $recipientEmail,
        string $recipientName = null,
        array $customData = [],
        string $senderEmail = null,
        string $senderName = null
    ): bool {
        try {
            // Generate the audit wizard URL
            $auditWizardUrl = $this->generateAuditWizardUrl($customData);
            
            // Generate QR code
            $qrCodeDataUri = $this->generateQrCode($auditWizardUrl);
            
            // Create email
            $email = $this->createEmail(
                $recipientEmail,
                $recipientName,
                $auditWizardUrl,
                $qrCodeDataUri,
                $senderEmail,
                $senderName
            );
            
            // Send email
            $this->mailer->send($email);
            
            $this->logger->info('QR code email sent successfully', [
                'recipient' => $recipientEmail,
                'audit_wizard_url' => $auditWizardUrl
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to send QR code email', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Generate QR code for a given URL
     */
    public function generateQrCode(string $url): string
    {
        $qrCode = QrCode::create($url)
            ->setSize(300)
            ->setMargin(10);

        $result = $this->qrCodeBuilder->build($qrCode);
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    /**
     * Generate audit wizard URL with optional parameters
     */
    private function generateAuditWizardUrl(array $customData = []): string
    {
        $baseUrl = rtrim($this->frontendUrl, '/');
        $auditWizardPath = '/audit-wizard';
        
        // Add custom parameters if provided
        if (!empty($customData)) {
            $queryParams = http_build_query($customData);
            $auditWizardPath .= '?' . $queryParams;
        }
        
        return $baseUrl . $auditWizardPath;
    }

    /**
     * Create the email with QR code attachment
     */
    private function createEmail(
        string $recipientEmail,
        ?string $recipientName,
        string $auditWizardUrl,
        string $qrCodeDataUri,
        ?string $senderEmail,
        ?string $senderName
    ): Email {
        $recipientName = $recipientName ?: 'Valued Client';
        
        $email = (new Email())
            ->from($senderEmail ?: 'noreply@tulsa-seo.com')
            ->to($recipientEmail)
            ->subject('Your SEO Audit Wizard - Quick Access')
            ->html($this->getEmailHtml($recipientName, $auditWizardUrl, $qrCodeDataUri))
            ->text($this->getEmailText($recipientName, $auditWizardUrl));

        if ($senderName) {
            $email->from($senderEmail ?: 'noreply@tulsa-seo.com', $senderName);
        }

        return $email;
    }

    /**
     * Get HTML email template
     */
    private function getEmailHtml(string $recipientName, string $auditWizardUrl, string $qrCodeDataUri): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>SEO Audit Wizard Access</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .qr-section { text-align: center; margin: 30px 0; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .qr-code { max-width: 200px; margin: 20px auto; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .button:hover { background: #5a6fd8; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 14px; color: #666; }
                .highlight { background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🚀 Your SEO Audit Wizard</h1>
                <p>Quick access to your personalized SEO analysis</p>
            </div>
            
            <div class='content'>
                <h2>Hello {$recipientName}!</h2>
                
                <p>Thank you for your interest in our SEO services. We've prepared a personalized audit wizard just for you!</p>
                
                <div class='highlight'>
                    <strong>📱 Scan the QR code below</strong> with your mobile device to instantly access your SEO audit wizard, or click the button to get started on your computer.
                </div>
                
                <div class='qr-section'>
                    <h3>Quick Access QR Code</h3>
                    <img src='{$qrCodeDataUri}' alt='QR Code for SEO Audit Wizard' class='qr-code'>
                    <p><strong>Scan with your phone camera</strong></p>
                </div>
                
                <div style='text-align: center;'>
                    <a href='{$auditWizardUrl}' class='button'>Start Your SEO Audit</a>
                </div>
                
                <h3>What you'll get:</h3>
                <ul>
                    <li>✅ Comprehensive website analysis</li>
                    <li>✅ Technical SEO audit</li>
                    <li>✅ Keyword research and recommendations</li>
                    <li>✅ Competitor analysis</li>
                    <li>✅ Custom SEO strategy roadmap</li>
                </ul>
                
                <p>Our audit wizard will guide you through a few quick questions to personalize your analysis. The entire process takes less than 5 minutes!</p>
                
                <div class='footer'>
                    <p><strong>Tulsa SEO</strong><br>
                    Professional SEO Services<br>
                    📧 <a href='mailto:info@tulsa-seo.com'>info@tulsa-seo.com</a><br>
                    🌐 <a href='https://tulsa-seo.com'>tulsa-seo.com</a></p>
                    
                    <p style='font-size: 12px; color: #999;'>
                        This QR code is personalized for you and will expire in 30 days for security purposes.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get plain text email template
     */
    private function getEmailText(string $recipientName, string $auditWizardUrl): string
    {
        return "
Hello {$recipientName}!

Thank you for your interest in our SEO services. We've prepared a personalized audit wizard just for you!

QUICK ACCESS:
{$auditWizardUrl}

What you'll get:
- Comprehensive website analysis
- Technical SEO audit  
- Keyword research and recommendations
- Competitor analysis
- Custom SEO strategy roadmap

Our audit wizard will guide you through a few quick questions to personalize your analysis. The entire process takes less than 5 minutes!

Best regards,
Tulsa SEO Team

Professional SEO Services
Email: info@tulsa-seo.com
Website: https://tulsa-seo.com

This link is personalized for you and will expire in 30 days for security purposes.
        ";
    }

    /**
     * Send bulk QR code emails
     */
    public function sendBulkAuditWizardQrEmails(array $recipients, array $customData = []): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($recipients as $recipient) {
            $email = $recipient['email'] ?? null;
            $name = $recipient['name'] ?? null;
            
            if (!$email) {
                $results['failed'][] = [
                    'recipient' => $recipient,
                    'error' => 'Email address is required'
                ];
                continue;
            }

            $success = $this->sendAuditWizardQrEmail($email, $name, $customData);
            
            if ($success) {
                $results['success'][] = $email;
            } else {
                $results['failed'][] = [
                    'recipient' => $recipient,
                    'error' => 'Failed to send email'
                ];
            }
        }

        return $results;
    }
}
