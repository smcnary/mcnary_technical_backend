<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/v1/audits/competitors' => [
            [['_route' => '_api_/v1/audits/competitors_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditCompetitor', '_api_operation_name' => '_api_/v1/audits/competitors_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/v1/audits/competitors_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditCompetitor', '_api_operation_name' => '_api_/v1/audits/competitors_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/audits/conversion-goals' => [
            [['_route' => '_api_/v1/audits/conversion-goals_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditConversionGoal', '_api_operation_name' => '_api_/v1/audits/conversion-goals_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/v1/audits/conversion-goals_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditConversionGoal', '_api_operation_name' => '_api_/v1/audits/conversion-goals_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/audits/findings' => [
            [['_route' => '_api_/v1/audits/findings_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditFinding', '_api_operation_name' => '_api_/v1/audits/findings_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/v1/audits/findings_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditFinding', '_api_operation_name' => '_api_/v1/audits/findings_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/audits/intakes' => [
            [['_route' => '_api_/v1/audits/intakes_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditIntake', '_api_operation_name' => '_api_/v1/audits/intakes_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/v1/audits/intakes_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditIntake', '_api_operation_name' => '_api_/v1/audits/intakes_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/audits/keywords' => [
            [['_route' => '_api_/v1/audits/keywords_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditKeyword', '_api_operation_name' => '_api_/v1/audits/keywords_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/v1/audits/keywords_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditKeyword', '_api_operation_name' => '_api_/v1/audits/keywords_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/audits/runs' => [
            [['_route' => '_api_/v1/audits/runs_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditRun', '_api_operation_name' => '_api_/v1/audits/runs_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/v1/audits/runs_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditRun', '_api_operation_name' => '_api_/v1/audits/runs_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'api_v1_audit_runs_list', '_controller' => 'App\\Controller\\Api\\V1\\AuditsController::listAuditRuns'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/notifications' => [
            [['_route' => '_api_/notifications_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Notification', '_api_operation_name' => '_api_/notifications_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/notifications_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Notification', '_api_operation_name' => '_api_/notifications_post', '_format' => null], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/agencies' => [
            [['_route' => 'api_v1_agencies_list', '_controller' => 'App\\Controller\\Api\\V1\\AgencyController::listAgencies'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_agencies_create', '_controller' => 'App\\Controller\\Api\\V1\\AgencyController::createAgency'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/audit-findings' => [[['_route' => 'api_v1_audit_findings_list', '_controller' => 'App\\Controller\\Api\\V1\\AuditFindingsController::listAuditFindings'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/audit-intakes/validate' => [[['_route' => 'api_v1_audit_intakes_validate', '_controller' => 'App\\Controller\\Api\\V1\\AuditIntakeController::validateAuditIntake'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/audit-intakes/check-email' => [[['_route' => 'api_v1_audit_intakes_check_email', '_controller' => 'App\\Controller\\Api\\V1\\AuditIntakeController::checkEmail'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/audit-intakes/check-website' => [[['_route' => 'api_v1_audit_intakes_check_website', '_controller' => 'App\\Controller\\Api\\V1\\AuditIntakeController::checkWebsite'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/audits/run' => [[['_route' => 'api_v1_audits_run', '_controller' => 'App\\Controller\\Api\\V1\\AuditsController::runAudit'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/auth/register' => [[['_route' => 'api_v1_auth_register', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/auth/login' => [[['_route' => 'api_v1_auth_login', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::login'], null, ['POST' => 0, 'OPTIONS' => 1], null, false, false, null]],
        '/api/v1/auth/google' => [[['_route' => 'api_v1_auth_google', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::googleLogin'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/auth/google/callback' => [[['_route' => 'api_v1_auth_google_callback', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::googleCallback'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/auth/google/link' => [[['_route' => 'api_v1_auth_google_link', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::linkGoogleAccount'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/auth/google/unlink' => [[['_route' => 'api_v1_auth_google_unlink', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::unlinkGoogleAccount'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/auth/refresh' => [[['_route' => 'api_v1_auth_refresh', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::refresh'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/auth/logout' => [[['_route' => 'api_v1_auth_logout', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::logout'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/auth/auth/logout' => [[['_route' => 'api_auth_logout', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::logout'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/backlinks' => [
            [['_route' => 'api_v1_backlinks_list', '_controller' => 'App\\Controller\\Api\\V1\\BacklinksController::listBacklinks'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_backlinks_create', '_controller' => 'App\\Controller\\Api\\V1\\BacklinksController::createBacklink'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/backlinks/import' => [[['_route' => 'api_v1_backlinks_import', '_controller' => 'App\\Controller\\Api\\V1\\BacklinksController::importBacklinks'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/campaigns' => [
            [['_route' => 'api_v1_campaigns_list', '_controller' => 'App\\Controller\\Api\\V1\\CampaignsController::listCampaigns'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_campaigns_create', '_controller' => 'App\\Controller\\Api\\V1\\CampaignsController::createCampaign'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/citations' => [
            [['_route' => 'api_v1_citations_list', '_controller' => 'App\\Controller\\Api\\V1\\CitationsController::listCitations'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_citations_create', '_controller' => 'App\\Controller\\Api\\V1\\CitationsController::createCitation'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/clients' => [
            [['_route' => 'api_v1_clients_list', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::listClients'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_clients_create', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::createClient'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/content-briefs' => [
            [['_route' => 'api_v1_content_briefs_list', '_controller' => 'App\\Controller\\Api\\V1\\ContentBriefsController::listContentBriefs'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_content_briefs_create', '_controller' => 'App\\Controller\\Api\\V1\\ContentBriefsController::createContentBrief'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/content-items' => [
            [['_route' => 'api_v1_content_items_list', '_controller' => 'App\\Controller\\Api\\V1\\ContentItemsController::listContentItems'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_content_items_create', '_controller' => 'App\\Controller\\Api\\V1\\ContentItemsController::createContentItem'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/documents' => [
            [['_route' => 'api_v1_documents_list', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_documents_create', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::create'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/faqs' => [[['_route' => 'api_v1_faqs_list', '_controller' => 'App\\Controller\\Api\\V1\\FaqsController::listFaqs'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/gbp/callback' => [[['_route' => 'api_v1_gbp_callback', '_controller' => 'App\\Controller\\Api\\V1\\GoogleBusinessProfileController::handleCallback'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/invoices' => [[['_route' => 'api_v1_invoices_list', '_controller' => 'App\\Controller\\Api\\V1\\InvoicesController::listInvoices'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/keywords' => [
            [['_route' => 'api_v1_keywords_list', '_controller' => 'App\\Controller\\Api\\V1\\KeywordsController::listKeywords'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_keywords_create', '_controller' => 'App\\Controller\\Api\\V1\\KeywordsController::createKeywords'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/lead-status/options' => [[['_route' => 'api_v1_lead_status_options', '_controller' => 'App\\Controller\\Api\\V1\\LeadStatusController::getStatusOptions'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/lead-status/stages' => [[['_route' => 'api_v1_lead_status_stages', '_controller' => 'App\\Controller\\Api\\V1\\LeadStatusController::getStages'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/admin/leadgen/execute' => [[['_route' => 'api_v1_admin_leadgen_execute', '_controller' => 'App\\Controller\\Api\\V1\\LeadgenController::executeCampaign'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/admin/leadgen/verticals' => [[['_route' => 'api_v1_admin_leadgen_verticals', '_controller' => 'App\\Controller\\Api\\V1\\LeadgenController::getVerticals'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/admin/leadgen/sources' => [[['_route' => 'api_v1_admin_leadgen_sources', '_controller' => 'App\\Controller\\Api\\V1\\LeadgenController::getSources'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/admin/leadgen/template' => [[['_route' => 'api_v1_admin_leadgen_template', '_controller' => 'App\\Controller\\Api\\V1\\LeadgenController::getCampaignTemplate'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/leads/simple' => [[['_route' => 'api_v1_leads_simple_list', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::listLeadsSimple'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/leads' => [[['_route' => 'api_v1_leads_list', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::listLeads'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/media-assets' => [[['_route' => 'api_v1_media_assets_list', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::listMediaAssets'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/notifications' => [[['_route' => 'api_v1_notifications_list', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::list'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/phone-numbers' => [[['_route' => 'openphone_phone_numbers', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getPhoneNumbers'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/integrations' => [
            [['_route' => 'openphone_integrations', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getIntegrations'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'openphone_create_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::createIntegration'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/openphone/calls' => [[['_route' => 'openphone_make_call', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::makeCall'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/messages' => [[['_route' => 'openphone_send_message', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::sendMessage'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/call-logs' => [[['_route' => 'openphone_call_logs', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getCallLogs'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/message-logs' => [[['_route' => 'openphone_message_logs', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getMessageLogs'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/webhooks' => [[['_route' => 'openphone_webhooks', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::handleWebhook'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/webhooks/calls' => [[['_route' => 'openphone_call_webhook', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneWebhookController::handleCallWebhook'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/webhooks/messages' => [[['_route' => 'openphone_message_webhook', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneWebhookController::handleMessageWebhook'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/webhooks/contacts' => [[['_route' => 'openphone_contact_webhook', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneWebhookController::handleContactWebhook'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/webhooks/status' => [[['_route' => 'openphone_status_webhook', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneWebhookController::handleStatusWebhook'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/packages' => [[['_route' => 'api_v1_packages_list', '_controller' => 'App\\Controller\\Api\\V1\\PackagesController::listPackages'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/pages' => [[['_route' => 'api_v1_pages_list', '_controller' => 'App\\Controller\\Api\\V1\\PagesController::listPages'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/qr-emails/send-audit-wizard' => [[['_route' => 'api_v1_qr_emails_send_audit_wizard', '_controller' => 'App\\Controller\\Api\\V1\\QrCodeEmailController::sendAuditWizardQrEmail'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/qr-emails/send-bulk-audit-wizard' => [[['_route' => 'api_v1_qr_emails_send_bulk_audit_wizard', '_controller' => 'App\\Controller\\Api\\V1\\QrCodeEmailController::sendBulkAuditWizardQrEmails'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/qr-emails/generate-qr-code' => [[['_route' => 'api_v1_qr_emails_generate_qr_code', '_controller' => 'App\\Controller\\Api\\V1\\QrCodeEmailController::generateQrCode'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/rankings' => [[['_route' => 'api_v1_rankings_list', '_controller' => 'App\\Controller\\Api\\V1\\RankingsController::listRankings'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/rankings/summary' => [[['_route' => 'api_v1_rankings_summary', '_controller' => 'App\\Controller\\Api\\V1\\RankingsController::getRankingsSummary'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/recommendations' => [[['_route' => 'api_v1_recommendations_list', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::listRecommendations'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/reviews' => [[['_route' => 'api_v1_reviews_list', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::listReviews'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/subscriptions' => [
            [['_route' => 'api_v1_subscriptions_list', '_controller' => 'App\\Controller\\Api\\V1\\SubscriptionsController::listSubscriptions'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_subscriptions_create', '_controller' => 'App\\Controller\\Api\\V1\\SubscriptionsController::createSubscription'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/twilio/test-connection' => [[['_route' => 'api_v1_twilio_test_connection', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::testConnection'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/twilio/call-target' => [[['_route' => 'api_v1_twilio_call_target', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::callTargetNumber'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/twilio/sms-target' => [[['_route' => 'api_v1_twilio_sms_target', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::sendSmsToTargetNumber'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/twilio/phone-info' => [[['_route' => 'api_v1_twilio_phone_info', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::getPhoneInfo'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/uploads/sign' => [[['_route' => 'api_v1_uploads_sign', '_controller' => 'App\\Controller\\Api\\V1\\UploadsController::signUpload'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/uploads/media-assets' => [[['_route' => 'api_v1_uploads_media_assets', '_controller' => 'App\\Controller\\Api\\V1\\UploadsController::registerMediaAsset'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/test' => [[['_route' => 'api_v1_test', '_controller' => 'App\\Controller\\Api\\V1\\UserController::test'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/me' => [[['_route' => 'api_v1_me', '_controller' => 'App\\Controller\\Api\\V1\\UserController::me'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/users' => [
            [['_route' => 'api_v1_users_list', '_controller' => 'App\\Controller\\Api\\V1\\UserController::listUsers'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_users_create', '_controller' => 'App\\Controller\\Api\\V1\\UserController::createUser'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/user-profile/greeting' => [[['_route' => 'api_v1_user_profile_greeting', '_controller' => 'App\\Controller\\Api\\V1\\UserProfileController::getUserGreeting'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/webhooks/stripe' => [[['_route' => 'api_v1_webhooks_stripe', '_controller' => 'App\\Controller\\Api\\V1\\WebhooksController::stripeWebhook'], null, ['POST' => 0], null, false, false, null]],
        '/api/auth/login' => [[['_route' => 'api_auth_login', '_controller' => 'App\\Controller\\AuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/auth/me' => [[['_route' => 'api_auth_me', '_controller' => 'App\\Controller\\AuthController::me'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/monitoring/health' => [[['_route' => 'api_monitoring_health', '_controller' => 'App\\Controller\\MonitoringController::health'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/monitoring/metrics' => [[['_route' => 'api_monitoring_metrics', '_controller' => 'App\\Controller\\MonitoringController::metrics'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/monitoring/ready' => [[['_route' => 'api_monitoring_ready', '_controller' => 'App\\Controller\\MonitoringController::ready'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/monitoring/live' => [[['_route' => 'api_monitoring_live', '_controller' => 'App\\Controller\\MonitoringController::live'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api(?'
                    .'|/(?'
                        .'|docs(?:\\.([^/]++))?(*:37)'
                        .'|\\.well\\-known/genid/([^/]++)(*:72)'
                        .'|validation_errors/([^/]++)(*:105)'
                    .')'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:142)'
                    .'|/(?'
                        .'|c(?'
                            .'|onte(?'
                                .'|xts/([^.]+)(?:\\.(jsonld))?(*:191)'
                                .'|nt_(?'
                                    .'|briefs(?'
                                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:240)'
                                        .'|(?:\\.([^/]++))?(?'
                                            .'|(*:266)'
                                        .')'
                                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                            .'|(*:304)'
                                        .')'
                                    .')'
                                    .'|items(?'
                                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:348)'
                                        .'|(?:\\.([^/]++))?(?'
                                            .'|(*:374)'
                                        .')'
                                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                            .'|(*:412)'
                                        .')'
                                    .')'
                                .')'
                            .')'
                            .'|a(?'
                                .'|mpaigns(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:464)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:490)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:528)'
                                    .')'
                                .')'
                                .'|se_studies(?'
                                    .'|(?:\\.([^/]++))?(*:566)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:600)'
                                    .'|(?:\\.([^/]++))?(*:623)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:660)'
                                    .')'
                                .')'
                                .'|tegories(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:707)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:733)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:771)'
                                    .')'
                                .')'
                            .')'
                            .'|itations(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:819)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:845)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:883)'
                                .')'
                            .')'
                            .'|lient(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:931)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:957)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:995)'
                                    .')'
                                .')'
                                .'|_locations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1044)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:1071)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:1110)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|errors/(\\d+)(?:\\.([^/]++))?(*:1150)'
                        .'|v(?'
                            .'|alidation_errors/([^/]++)(?'
                                .'|(*:1191)'
                            .')'
                            .'|1/(?'
                                .'|a(?'
                                    .'|udit(?'
                                        .'|s/(?'
                                            .'|co(?'
                                                .'|mpetitors/([^/]++)(?'
                                                    .'|(*:1248)'
                                                .')'
                                                .'|nversion\\-goals/([^/]++)(?'
                                                    .'|(*:1285)'
                                                .')'
                                            .')'
                                            .'|findings/([^/]++)(?'
                                                .'|(*:1316)'
                                            .')'
                                            .'|intakes/([^/]++)(?'
                                                .'|(*:1345)'
                                            .')'
                                            .'|keywords/([^/]++)(?'
                                                .'|(*:1375)'
                                            .')'
                                            .'|runs/([^/]++)(?'
                                                .'|(*:1401)'
                                            .')'
                                        .')'
                                        .'|\\-findings/([^/]++)(*:1431)'
                                    .')'
                                    .'|gencies/([^/]++)(?'
                                        .'|(*:1460)'
                                        .'|/invite\\-admin(*:1483)'
                                    .')'
                                    .'|dmin/leadgen/status/([^/]++)(*:1521)'
                                .')'
                                .'|c(?'
                                    .'|ampaigns/([^/]++)(*:1552)'
                                    .'|itations/([^/]++)(?'
                                        .'|(*:1581)'
                                    .')'
                                    .'|lients/(?'
                                        .'|([^/]++)(?'
                                            .'|(*:1612)'
                                            .'|/locations(?'
                                                .'|(*:1634)'
                                            .')'
                                        .')'
                                        .'|login(*:1650)'
                                        .'|register(*:1667)'
                                    .')'
                                    .'|ontent\\-(?'
                                        .'|briefs/([^/]++)(*:1703)'
                                        .'|items/([^/]++)(?'
                                            .'|(*:1729)'
                                        .')'
                                    .')'
                                .')'
                                .'|documents/(?'
                                    .'|([^/]++)(?'
                                        .'|(*:1765)'
                                        .'|/(?'
                                            .'|s(?'
                                                .'|end\\-for\\-signature(*:1801)'
                                                .'|ign(?'
                                                    .'|(*:1816)'
                                                    .'|ature\\-status(*:1838)'
                                                .')'
                                            .')'
                                            .'|archive(*:1856)'
                                        .')'
                                    .')'
                                    .'|templates/([^/]++)/create(*:1892)'
                                    .'|client/([^/]++)(*:1916)'
                                    .'|ready\\-for\\-signature(*:1946)'
                                .')'
                                .'|faqs/([^/]++)(*:1969)'
                                .'|gbp/(?'
                                    .'|kpi/([^/]++)(*:1997)'
                                    .'|auth/([^/]++)(*:2019)'
                                    .'|connect/([^/]++)(*:2044)'
                                .')'
                                .'|invoices/([^/]++)(*:2071)'
                                .'|leads/(?'
                                    .'|([^/]++)(?'
                                        .'|(*:2100)'
                                        .'|/(?'
                                            .'|notes(?'
                                                .'|(*:2121)'
                                            .')'
                                            .'|events(*:2137)'
                                        .')'
                                    .')'
                                    .'|import(*:2154)'
                                    .'|leadgen\\-import(*:2178)'
                                    .'|([^/]++)/(?'
                                        .'|events(*:2205)'
                                        .'|statistics(*:2224)'
                                        .'|tech\\-stack(?'
                                            .'|(*:2247)'
                                        .')'
                                    .')'
                                    .'|google\\-sheets\\-import(*:2280)'
                                .')'
                                .'|media\\-assets/([^/]++)(*:2312)'
                                .'|notifications/(?'
                                    .'|([^/]++)(?'
                                        .'|(*:2349)'
                                        .'|/(?'
                                            .'|read(*:2366)'
                                            .'|unread(*:2381)'
                                        .')'
                                    .')'
                                    .'|mark\\-all\\-read(*:2407)'
                                    .'|([^/]++)(*:2424)'
                                    .'|count(*:2438)'
                                .')'
                                .'|openphone/integrations/([^/]++)(?'
                                    .'|(*:2482)'
                                    .'|/sync(*:2496)'
                                .')'
                                .'|pa(?'
                                    .'|ckages/([^/]++)(*:2526)'
                                    .'|ges/([^/]++)(*:2547)'
                                .')'
                                .'|r(?'
                                    .'|ankings/([^/]++)(*:2577)'
                                    .'|e(?'
                                        .'|commendations/([^/]++)(?'
                                            .'|(*:2615)'
                                        .')'
                                        .'|views/(?'
                                            .'|([^/]++)(?'
                                                .'|(*:2645)'
                                                .'|/respond(*:2662)'
                                            .')'
                                            .'|sync(*:2676)'
                                        .')'
                                    .')'
                                .')'
                                .'|subscriptions/([^/]++)(*:2710)'
                                .'|twilio/(?'
                                    .'|call\\-(?'
                                        .'|client/([^/]++)(*:2753)'
                                        .'|details/([^/]++)(*:2778)'
                                    .')'
                                    .'|sms\\-client/([^/]++)(*:2808)'
                                    .'|message\\-details/([^/]++)(*:2842)'
                                .')'
                                .'|users/([^/]++)(*:2866)'
                            .')'
                        .')'
                        .'|agencies(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2914)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2941)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2980)'
                            .')'
                        .')'
                        .'|backlinks(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3029)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:3056)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:3095)'
                            .')'
                        .')'
                        .'|document(?'
                            .'|s(?'
                                .'|(?:\\.([^/]++))?(*:3136)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3171)'
                                .'|(?:\\.([^/]++))?(*:3195)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:3233)'
                                .')'
                            .')'
                            .'|_(?'
                                .'|signatures(?'
                                    .'|(?:\\.([^/]++))?(*:3276)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3311)'
                                    .'|(?:\\.([^/]++))?(*:3335)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3373)'
                                    .')'
                                .')'
                                .'|templates(?'
                                    .'|(?:\\.([^/]++))?(*:3411)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3446)'
                                    .'|(?:\\.([^/]++))?(*:3470)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3508)'
                                    .')'
                                .')'
                                .'|versions(?'
                                    .'|(?:\\.([^/]++))?(*:3545)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3580)'
                                    .'|(?:\\.([^/]++))?(*:3604)'
                                .')'
                            .')'
                        .')'
                        .'|f(?'
                            .'|aqs(?'
                                .'|(?:\\.([^/]++))?(*:3641)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3676)'
                                .'|(?:\\.([^/]++))?(*:3700)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:3738)'
                                .')'
                            .')'
                            .'|orm(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3785)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3812)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3851)'
                                    .')'
                                .')'
                                .'|_submissions(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3903)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3930)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3969)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|invoices(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4019)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:4046)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:4085)'
                            .')'
                        .')'
                        .'|keywords(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4133)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:4160)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:4199)'
                            .')'
                        .')'
                        .'|lead(?'
                            .'|s(?'
                                .'|(?:\\.([^/]++))?(*:4236)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4271)'
                                .'|(?:\\.([^/]++))?(*:4295)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4333)'
                                .')'
                            .')'
                            .'|_(?'
                                .'|events(?'
                                    .'|(?:\\.([^/]++))?(*:4372)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4407)'
                                    .'|(?:\\.([^/]++))?(*:4431)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4466)'
                                .')'
                                .'|sources(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4512)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4539)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4578)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|media_assets(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4632)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:4659)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:4698)'
                            .')'
                        .')'
                        .'|n(?'
                            .'|ewsletter_subscriptions(?'
                                .'|(?:\\.([^/]++))?(*:4754)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4789)'
                            .')'
                            .'|otifications/([^/]++)(?'
                                .'|(*:4823)'
                            .')'
                        .')'
                        .'|o(?'
                            .'|_auth_(?'
                                .'|connections(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4887)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4914)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4953)'
                                    .')'
                                .')'
                                .'|tokens(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4999)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5026)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5065)'
                                    .')'
                                .')'
                            .')'
                            .'|pen_phone_(?'
                                .'|call_logs(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5128)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5155)'
                                    .')'
                                .')'
                                .'|integrations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5207)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5234)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5273)'
                                    .')'
                                .')'
                                .'|message_logs(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5325)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5352)'
                                    .')'
                                .')'
                            .')'
                            .'|rganizations(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5405)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5432)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5471)'
                                .')'
                            .')'
                        .')'
                        .'|p(?'
                            .'|a(?'
                                .'|ckages(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5526)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5553)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5592)'
                                    .')'
                                .')'
                                .'|ges(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5635)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5662)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5701)'
                                    .')'
                                .')'
                            .')'
                            .'|osts(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5746)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5773)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5812)'
                                .')'
                            .')'
                        .')'
                        .'|r(?'
                            .'|anking(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5867)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5894)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5933)'
                                    .')'
                                .')'
                                .'|_dailies(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5981)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:6008)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:6047)'
                                    .')'
                                .')'
                            .')'
                            .'|e(?'
                                .'|commendations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6105)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:6132)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:6171)'
                                    .')'
                                .')'
                                .'|views(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6216)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:6243)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:6282)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|s(?'
                            .'|eo_metas(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6336)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6363)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6402)'
                                .')'
                            .')'
                            .'|ites(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6446)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6473)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6512)'
                                .')'
                            .')'
                            .'|ubscriptions(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6564)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6591)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6630)'
                                .')'
                            .')'
                            .'|ystem_users(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6681)'
                                .'|(?:\\.([^/]++))?(*:6705)'
                            .')'
                        .')'
                        .'|t(?'
                            .'|ags(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6752)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6779)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6818)'
                                .')'
                            .')'
                            .'|enants(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6864)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6891)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6930)'
                                .')'
                            .')'
                        .')'
                        .'|user(?'
                            .'|s(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6979)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:7006)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:7045)'
                                .')'
                            .')'
                            .'|_client_accesses(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:7101)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:7128)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:7167)'
                                .')'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/qr\\-code/([^/]++)/([\\w\\W]+)(*:7209)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        37 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        72 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => 'true'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        105 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        142 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        191 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        240 => [[['_route' => '_api_/content_briefs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentBrief', '_api_operation_name' => '_api_/content_briefs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        266 => [
            [['_route' => '_api_/content_briefs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentBrief', '_api_operation_name' => '_api_/content_briefs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/content_briefs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentBrief', '_api_operation_name' => '_api_/content_briefs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        304 => [
            [['_route' => '_api_/content_briefs/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentBrief', '_api_operation_name' => '_api_/content_briefs/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/content_briefs/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentBrief', '_api_operation_name' => '_api_/content_briefs/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        348 => [[['_route' => '_api_/content_items/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentItem', '_api_operation_name' => '_api_/content_items/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        374 => [
            [['_route' => '_api_/content_items{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentItem', '_api_operation_name' => '_api_/content_items{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/content_items{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentItem', '_api_operation_name' => '_api_/content_items{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        412 => [
            [['_route' => '_api_/content_items/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentItem', '_api_operation_name' => '_api_/content_items/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/content_items/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ContentItem', '_api_operation_name' => '_api_/content_items/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        464 => [[['_route' => '_api_/campaigns/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Campaign', '_api_operation_name' => '_api_/campaigns/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        490 => [
            [['_route' => '_api_/campaigns{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Campaign', '_api_operation_name' => '_api_/campaigns{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/campaigns{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Campaign', '_api_operation_name' => '_api_/campaigns{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        528 => [
            [['_route' => '_api_/campaigns/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Campaign', '_api_operation_name' => '_api_/campaigns/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/campaigns/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Campaign', '_api_operation_name' => '_api_/campaigns/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        566 => [[['_route' => '_api_/case_studies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        600 => [[['_route' => '_api_/case_studies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        623 => [[['_route' => '_api_/case_studies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        660 => [
            [['_route' => '_api_/case_studies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/case_studies/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/case_studies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        707 => [[['_route' => '_api_/categories/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        733 => [
            [['_route' => '_api_/categories{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/categories{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        771 => [
            [['_route' => '_api_/categories/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/categories/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        819 => [[['_route' => '_api_/citations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Citation', '_api_operation_name' => '_api_/citations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        845 => [
            [['_route' => '_api_/citations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Citation', '_api_operation_name' => '_api_/citations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/citations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Citation', '_api_operation_name' => '_api_/citations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        883 => [
            [['_route' => '_api_/citations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Citation', '_api_operation_name' => '_api_/citations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/citations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Citation', '_api_operation_name' => '_api_/citations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        931 => [[['_route' => '_api_/clients/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Client', '_api_operation_name' => '_api_/clients/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        957 => [
            [['_route' => '_api_/clients{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Client', '_api_operation_name' => '_api_/clients{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/clients{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Client', '_api_operation_name' => '_api_/clients{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        995 => [
            [['_route' => '_api_/clients/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Client', '_api_operation_name' => '_api_/clients/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/clients/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Client', '_api_operation_name' => '_api_/clients/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1044 => [[['_route' => '_api_/client_locations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ClientLocation', '_api_operation_name' => '_api_/client_locations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1071 => [
            [['_route' => '_api_/client_locations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ClientLocation', '_api_operation_name' => '_api_/client_locations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/client_locations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ClientLocation', '_api_operation_name' => '_api_/client_locations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1110 => [
            [['_route' => '_api_/client_locations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ClientLocation', '_api_operation_name' => '_api_/client_locations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/client_locations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\ClientLocation', '_api_operation_name' => '_api_/client_locations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1150 => [[['_route' => '_api_errors', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\State\\ApiResource\\Error', '_api_operation_name' => '_api_errors', '_format' => null], ['status', '_format'], ['GET' => 0], null, false, true, null]],
        1191 => [
            [['_route' => '_api_validation_errors_problem', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_problem', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_hydra', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_hydra', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_jsonapi', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_jsonapi', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_xml', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_xml', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
        ],
        1248 => [
            [['_route' => '_api_/v1/audits/competitors/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditCompetitor', '_api_operation_name' => '_api_/v1/audits/competitors/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/v1/audits/competitors/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditCompetitor', '_api_operation_name' => '_api_/v1/audits/competitors/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1285 => [
            [['_route' => '_api_/v1/audits/conversion-goals/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditConversionGoal', '_api_operation_name' => '_api_/v1/audits/conversion-goals/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/v1/audits/conversion-goals/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditConversionGoal', '_api_operation_name' => '_api_/v1/audits/conversion-goals/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1316 => [
            [['_route' => '_api_/v1/audits/findings/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditFinding', '_api_operation_name' => '_api_/v1/audits/findings/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/v1/audits/findings/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditFinding', '_api_operation_name' => '_api_/v1/audits/findings/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1345 => [
            [['_route' => '_api_/v1/audits/intakes/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditIntake', '_api_operation_name' => '_api_/v1/audits/intakes/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/v1/audits/intakes/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditIntake', '_api_operation_name' => '_api_/v1/audits/intakes/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1375 => [
            [['_route' => '_api_/v1/audits/keywords/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditKeyword', '_api_operation_name' => '_api_/v1/audits/keywords/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/v1/audits/keywords/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditKeyword', '_api_operation_name' => '_api_/v1/audits/keywords/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1401 => [
            [['_route' => '_api_/v1/audits/runs/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditRun', '_api_operation_name' => '_api_/v1/audits/runs/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/v1/audits/runs/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\AuditRun', '_api_operation_name' => '_api_/v1/audits/runs/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'api_v1_audit_runs_get', '_controller' => 'App\\Controller\\Api\\V1\\AuditsController::getAuditRun'], ['id'], ['GET' => 0], null, false, true, null],
        ],
        1431 => [[['_route' => 'api_v1_audit_findings_get', '_controller' => 'App\\Controller\\Api\\V1\\AuditFindingsController::getAuditFinding'], ['id'], ['GET' => 0], null, false, true, null]],
        1460 => [[['_route' => 'api_v1_agencies_update', '_controller' => 'App\\Controller\\Api\\V1\\AgencyController::updateAgency'], ['id'], ['PATCH' => 0], null, false, true, null]],
        1483 => [[['_route' => 'api_v1_agencies_invite_admin', '_controller' => 'App\\Controller\\Api\\V1\\AgencyController::inviteAgencyAdmin'], ['id'], ['POST' => 0], null, false, false, null]],
        1521 => [[['_route' => 'api_v1_admin_leadgen_status', '_controller' => 'App\\Controller\\Api\\V1\\LeadgenController::getCampaignStatus'], ['campaignId'], ['GET' => 0], null, false, true, null]],
        1552 => [[['_route' => 'api_v1_campaigns_get', '_controller' => 'App\\Controller\\Api\\V1\\CampaignsController::getCampaign'], ['id'], ['GET' => 0], null, false, true, null]],
        1581 => [
            [['_route' => 'api_v1_citations_get', '_controller' => 'App\\Controller\\Api\\V1\\CitationsController::getCitation'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_citations_update', '_controller' => 'App\\Controller\\Api\\V1\\CitationsController::updateCitation'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1612 => [
            [['_route' => 'api_v1_clients_get', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::getClient'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_clients_update', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::updateClient'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1634 => [
            [['_route' => 'api_v1_clients_locations_get', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::getClientLocations'], ['id'], ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_clients_locations_create', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::createClientLocation'], ['id'], ['POST' => 0], null, false, false, null],
        ],
        1650 => [[['_route' => 'api_v1_clients_login', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::clientLogin'], [], ['POST' => 0], null, false, false, null]],
        1667 => [[['_route' => 'api_v1_clients_register', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::registerClient'], [], ['POST' => 0, 'OPTIONS' => 1], null, false, false, null]],
        1703 => [[['_route' => 'api_v1_content_briefs_get', '_controller' => 'App\\Controller\\Api\\V1\\ContentBriefsController::getContentBrief'], ['id'], ['GET' => 0], null, false, true, null]],
        1729 => [
            [['_route' => 'api_v1_content_items_get', '_controller' => 'App\\Controller\\Api\\V1\\ContentItemsController::getContentItem'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_content_items_update', '_controller' => 'App\\Controller\\Api\\V1\\ContentItemsController::updateContentItem'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1765 => [
            [['_route' => 'api_v1_documents_get', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::get'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_documents_update', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::update'], ['id'], ['PUT' => 0], null, false, true, null],
        ],
        1801 => [[['_route' => 'api_v1_documents_send_for_signature', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::sendForSignature'], ['id'], ['POST' => 0], null, false, false, null]],
        1816 => [[['_route' => 'api_v1_documents_sign', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::sign'], ['id'], ['POST' => 0], null, false, false, null]],
        1838 => [[['_route' => 'api_v1_documents_signature_status', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::getSignatureStatus'], ['id'], ['GET' => 0], null, false, false, null]],
        1856 => [[['_route' => 'api_v1_documents_archive', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::archive'], ['id'], ['POST' => 0], null, false, false, null]],
        1892 => [[['_route' => 'api_v1_documents_create_from_template', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::createFromTemplate'], ['templateId'], ['POST' => 0], null, false, false, null]],
        1916 => [[['_route' => 'api_v1_documents_for_client', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::getDocumentsForClient'], ['clientId'], ['GET' => 0], null, false, true, null]],
        1946 => [[['_route' => 'api_v1_documents_ready_for_signature', '_controller' => 'App\\Controller\\Api\\V1\\DocumentController::getDocumentsReadyForSignature'], [], ['GET' => 0], null, false, false, null]],
        1969 => [[['_route' => 'api_v1_faqs_get', '_controller' => 'App\\Controller\\Api\\V1\\FaqsController::getFaq'], ['id'], ['GET' => 0], null, false, true, null]],
        1997 => [[['_route' => 'api_v1_gbp_kpi', '_controller' => 'App\\Controller\\Api\\V1\\GoogleBusinessProfileController::getGbpKpi'], ['clientId'], ['GET' => 0], null, false, true, null]],
        2019 => [[['_route' => 'api_v1_gbp_auth', '_controller' => 'App\\Controller\\Api\\V1\\GoogleBusinessProfileController::initiateAuth'], ['clientId'], ['GET' => 0], null, false, true, null]],
        2044 => [[['_route' => 'api_v1_gbp_connect', '_controller' => 'App\\Controller\\Api\\V1\\GoogleBusinessProfileController::connectGbp'], ['clientId'], ['POST' => 0], null, false, true, null]],
        2071 => [[['_route' => 'api_v1_invoices_get', '_controller' => 'App\\Controller\\Api\\V1\\InvoicesController::getInvoice'], ['id'], ['GET' => 0], null, false, true, null]],
        2100 => [
            [['_route' => 'api_v1_leads_get', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLead'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_leads_update', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::updateLead'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        2121 => [
            [['_route' => 'api_v1_leads_notes_get', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLeadNotes'], ['id'], ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_leads_notes_save', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::saveLeadNotes'], ['id'], ['POST' => 0], null, false, false, null],
        ],
        2137 => [[['_route' => 'api_v1_leads_events_create', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::createLeadEvent'], ['id'], ['POST' => 0], null, false, false, null]],
        2154 => [[['_route' => 'api_v1_leads_import', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::importLeads'], [], ['POST' => 0], null, false, false, null]],
        2178 => [[['_route' => 'api_v1_leads_leadgen_import', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::importLeadgenData'], [], ['POST' => 0], null, false, false, null]],
        2205 => [[['_route' => 'api_v1_leads_events_list', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLeadEvents'], ['id'], ['GET' => 0], null, false, false, null]],
        2224 => [[['_route' => 'api_v1_leads_statistics', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLeadStatistics'], ['id'], ['GET' => 0], null, false, false, null]],
        2247 => [
            [['_route' => 'api_v1_leads_tech_stack_get', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLeadTechStack'], ['id'], ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_leads_tech_stack_analyze', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::analyzeLeadTechStack'], ['id'], ['POST' => 0], null, false, false, null],
        ],
        2280 => [[['_route' => 'api_v1_leads_google_sheets_import', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::importFromGoogleSheets'], [], ['POST' => 0], null, false, false, null]],
        2312 => [[['_route' => 'api_v1_media_assets_get', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::getMediaAsset'], ['id'], ['GET' => 0], null, false, true, null]],
        2349 => [[['_route' => 'api_v1_notifications_show', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        2366 => [[['_route' => 'api_v1_notifications_mark_read', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::markAsRead'], ['id'], ['PATCH' => 0], null, false, false, null]],
        2381 => [[['_route' => 'api_v1_notifications_mark_unread', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::markAsUnread'], ['id'], ['PATCH' => 0], null, false, false, null]],
        2407 => [[['_route' => 'api_v1_notifications_mark_all_read', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::markAllAsRead'], [], ['PATCH' => 0], null, false, false, null]],
        2424 => [[['_route' => 'api_v1_notifications_delete', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        2438 => [[['_route' => 'api_v1_notifications_count', '_controller' => 'App\\Controller\\Api\\V1\\NotificationsController::count'], [], ['GET' => 0], null, false, false, null]],
        2482 => [
            [['_route' => 'openphone_update_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::updateIntegration'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'openphone_delete_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::deleteIntegration'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        2496 => [[['_route' => 'openphone_sync_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::syncIntegration'], ['id'], ['POST' => 0], null, false, false, null]],
        2526 => [[['_route' => 'api_v1_packages_get', '_controller' => 'App\\Controller\\Api\\V1\\PackagesController::getPackage'], ['id'], ['GET' => 0], null, false, true, null]],
        2547 => [[['_route' => 'api_v1_pages_get', '_controller' => 'App\\Controller\\Api\\V1\\PagesController::getPage'], ['id'], ['GET' => 0], null, false, true, null]],
        2577 => [[['_route' => 'api_v1_rankings_get', '_controller' => 'App\\Controller\\Api\\V1\\RankingsController::getRanking'], ['id'], ['GET' => 0], null, false, true, null]],
        2615 => [
            [['_route' => 'api_v1_recommendations_get', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::getRecommendation'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_recommendations_update', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::updateRecommendation'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        2645 => [[['_route' => 'api_v1_reviews_get', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::getReview'], ['id'], ['GET' => 0], null, false, true, null]],
        2662 => [[['_route' => 'api_v1_reviews_respond', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::respondToReview'], ['id'], ['POST' => 0], null, false, false, null]],
        2676 => [[['_route' => 'api_v1_reviews_sync', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::syncReviews'], [], ['POST' => 0], null, false, false, null]],
        2710 => [[['_route' => 'api_v1_subscriptions_get', '_controller' => 'App\\Controller\\Api\\V1\\SubscriptionsController::getSubscription'], ['id'], ['GET' => 0], null, false, true, null]],
        2753 => [[['_route' => 'api_v1_twilio_call_client', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::callClient'], ['clientId'], ['POST' => 0], null, false, true, null]],
        2778 => [[['_route' => 'api_v1_twilio_call_details', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::getCallDetails'], ['callSid'], ['GET' => 0], null, false, true, null]],
        2808 => [[['_route' => 'api_v1_twilio_sms_client', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::sendSmsToClient'], ['clientId'], ['POST' => 0], null, false, true, null]],
        2842 => [[['_route' => 'api_v1_twilio_message_details', '_controller' => 'App\\Controller\\Api\\V1\\TwilioController::getMessageDetails'], ['messageSid'], ['GET' => 0], null, false, true, null]],
        2866 => [[['_route' => 'api_v1_users_update', '_controller' => 'App\\Controller\\Api\\V1\\UserController::updateUser'], ['id'], ['PATCH' => 0], null, false, true, null]],
        2914 => [[['_route' => '_api_/agencies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2941 => [
            [['_route' => '_api_/agencies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/agencies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2980 => [
            [['_route' => '_api_/agencies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/agencies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3029 => [[['_route' => '_api_/backlinks/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3056 => [
            [['_route' => '_api_/backlinks{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3095 => [
            [['_route' => '_api_/backlinks/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3136 => [[['_route' => '_api_/documents{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3171 => [[['_route' => '_api_/documents/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3195 => [[['_route' => '_api_/documents{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3233 => [
            [['_route' => '_api_/documents/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/documents/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/documents/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3276 => [[['_route' => '_api_/document_signatures{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3311 => [[['_route' => '_api_/document_signatures/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3335 => [[['_route' => '_api_/document_signatures{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3373 => [
            [['_route' => '_api_/document_signatures/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/document_signatures/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3411 => [[['_route' => '_api_/document_templates{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3446 => [[['_route' => '_api_/document_templates/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3470 => [[['_route' => '_api_/document_templates{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3508 => [
            [['_route' => '_api_/document_templates/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/document_templates/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/document_templates/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3545 => [[['_route' => '_api_/document_versions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentVersion', '_api_operation_name' => '_api_/document_versions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3580 => [[['_route' => '_api_/document_versions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentVersion', '_api_operation_name' => '_api_/document_versions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3604 => [[['_route' => '_api_/document_versions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentVersion', '_api_operation_name' => '_api_/document_versions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3641 => [[['_route' => '_api_/faqs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3676 => [[['_route' => '_api_/faqs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3700 => [[['_route' => '_api_/faqs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3738 => [
            [['_route' => '_api_/faqs/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3785 => [[['_route' => '_api_/forms/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3812 => [
            [['_route' => '_api_/forms{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/forms{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3851 => [
            [['_route' => '_api_/forms/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/forms/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3903 => [[['_route' => '_api_/form_submissions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3930 => [
            [['_route' => '_api_/form_submissions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3969 => [
            [['_route' => '_api_/form_submissions/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4019 => [[['_route' => '_api_/invoices/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4046 => [
            [['_route' => '_api_/invoices{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/invoices{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4085 => [
            [['_route' => '_api_/invoices/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/invoices/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4133 => [[['_route' => '_api_/keywords/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4160 => [
            [['_route' => '_api_/keywords{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/keywords{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4199 => [
            [['_route' => '_api_/keywords/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/keywords/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4236 => [[['_route' => '_api_/leads{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        4271 => [[['_route' => '_api_/leads/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4295 => [[['_route' => '_api_/leads{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        4333 => [
            [['_route' => '_api_/leads/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/leads/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
        ],
        4372 => [[['_route' => '_api_/lead_events{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        4407 => [[['_route' => '_api_/lead_events/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4431 => [[['_route' => '_api_/lead_events{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        4466 => [[['_route' => '_api_/lead_events/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null]],
        4512 => [[['_route' => '_api_/lead_sources/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4539 => [
            [['_route' => '_api_/lead_sources{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4578 => [
            [['_route' => '_api_/lead_sources/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4632 => [[['_route' => '_api_/media_assets/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4659 => [
            [['_route' => '_api_/media_assets{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4698 => [
            [['_route' => '_api_/media_assets/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4754 => [[['_route' => '_api_/newsletter_subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        4789 => [[['_route' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4823 => [
            [['_route' => '_api_/notifications/{id}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Notification', '_api_operation_name' => '_api_/notifications/{id}_get', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/notifications/{id}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Notification', '_api_operation_name' => '_api_/notifications/{id}_put', '_format' => null], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/notifications/{id}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Notification', '_api_operation_name' => '_api_/notifications/{id}_patch', '_format' => null], ['id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/notifications/{id}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Notification', '_api_operation_name' => '_api_/notifications/{id}_delete', '_format' => null], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        4887 => [[['_route' => '_api_/o_auth_connections/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4914 => [
            [['_route' => '_api_/o_auth_connections{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4953 => [
            [['_route' => '_api_/o_auth_connections/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4999 => [[['_route' => '_api_/o_auth_tokens/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5026 => [
            [['_route' => '_api_/o_auth_tokens{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5065 => [
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5128 => [[['_route' => '_api_/open_phone_call_logs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneCallLog', '_api_operation_name' => '_api_/open_phone_call_logs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5155 => [
            [['_route' => '_api_/open_phone_call_logs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneCallLog', '_api_operation_name' => '_api_/open_phone_call_logs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_call_logs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneCallLog', '_api_operation_name' => '_api_/open_phone_call_logs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5207 => [[['_route' => '_api_/open_phone_integrations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5234 => [
            [['_route' => '_api_/open_phone_integrations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_integrations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5273 => [
            [['_route' => '_api_/open_phone_integrations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_integrations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5325 => [[['_route' => '_api_/open_phone_message_logs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneMessageLog', '_api_operation_name' => '_api_/open_phone_message_logs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5352 => [
            [['_route' => '_api_/open_phone_message_logs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneMessageLog', '_api_operation_name' => '_api_/open_phone_message_logs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_message_logs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneMessageLog', '_api_operation_name' => '_api_/open_phone_message_logs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5405 => [[['_route' => '_api_/organizations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5432 => [
            [['_route' => '_api_/organizations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/organizations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5471 => [
            [['_route' => '_api_/organizations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/organizations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5526 => [[['_route' => '_api_/packages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5553 => [
            [['_route' => '_api_/packages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/packages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5592 => [
            [['_route' => '_api_/packages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/packages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5635 => [[['_route' => '_api_/pages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5662 => [
            [['_route' => '_api_/pages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/pages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5701 => [
            [['_route' => '_api_/pages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/pages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5746 => [[['_route' => '_api_/posts/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5773 => [
            [['_route' => '_api_/posts{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/posts{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5812 => [
            [['_route' => '_api_/posts/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/posts/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5867 => [[['_route' => '_api_/rankings/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5894 => [
            [['_route' => '_api_/rankings{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/rankings{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5933 => [
            [['_route' => '_api_/rankings/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/rankings/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5981 => [[['_route' => '_api_/ranking_dailies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6008 => [
            [['_route' => '_api_/ranking_dailies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6047 => [
            [['_route' => '_api_/ranking_dailies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6105 => [[['_route' => '_api_/recommendations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6132 => [
            [['_route' => '_api_/recommendations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6171 => [
            [['_route' => '_api_/recommendations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6216 => [[['_route' => '_api_/reviews/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6243 => [
            [['_route' => '_api_/reviews{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/reviews{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6282 => [
            [['_route' => '_api_/reviews/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/reviews/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6336 => [[['_route' => '_api_/seo_metas/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6363 => [
            [['_route' => '_api_/seo_metas{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6402 => [
            [['_route' => '_api_/seo_metas/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6446 => [[['_route' => '_api_/sites/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6473 => [
            [['_route' => '_api_/sites{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/sites{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6512 => [
            [['_route' => '_api_/sites/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/sites/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6564 => [[['_route' => '_api_/subscriptions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6591 => [
            [['_route' => '_api_/subscriptions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6630 => [
            [['_route' => '_api_/subscriptions/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6681 => [[['_route' => '_api_/system_users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6705 => [[['_route' => '_api_/system_users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        6752 => [[['_route' => '_api_/tags/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6779 => [
            [['_route' => '_api_/tags{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tags{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6818 => [
            [['_route' => '_api_/tags/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/tags/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6864 => [[['_route' => '_api_/tenants/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6891 => [
            [['_route' => '_api_/tenants{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tenants{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6930 => [
            [['_route' => '_api_/tenants/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/tenants/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6979 => [[['_route' => '_api_/users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        7006 => [
            [['_route' => '_api_/users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/users{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        7045 => [
            [['_route' => '_api_/users/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/users/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        7101 => [[['_route' => '_api_/user_client_accesses/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        7128 => [
            [['_route' => '_api_/user_client_accesses{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        7167 => [
            [['_route' => '_api_/user_client_accesses/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        7209 => [
            [['_route' => 'qr_code_generate', '_controller' => 'Endroid\\QrCodeBundle\\Controller\\GenerateController'], ['builder', 'data'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
