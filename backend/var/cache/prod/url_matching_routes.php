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
        '/api/v1/leads' => [[['_route' => 'api_v1_leads_list', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::listLeads'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/media-assets' => [[['_route' => 'api_v1_media_assets_list', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::listMediaAssets'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/phone-numbers' => [[['_route' => 'openphone_phone_numbers', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getPhoneNumbers'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/integrations' => [
            [['_route' => 'openphone_integrations', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getIntegrations'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'openphone_create_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::createIntegration'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/openphone/calls' => [[['_route' => 'openphone_make_call', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::makeCall'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/messages' => [[['_route' => 'openphone_send_message', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::sendMessage'], null, ['POST' => 0], null, false, false, null]],
        '/api/v1/openphone/call-logs' => [[['_route' => 'openphone_call_logs', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getCallLogs'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/openphone/message-logs' => [[['_route' => 'openphone_message_logs', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::getMessageLogs'], null, ['GET' => 0], null, false, false, null]],
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
                                        .'|/events(*:2116)'
                                    .')'
                                    .'|import(*:2132)'
                                    .'|leadgen\\-import(*:2156)'
                                    .'|([^/]++)/(?'
                                        .'|events(*:2183)'
                                        .'|statistics(*:2202)'
                                    .')'
                                .')'
                                .'|media\\-assets/([^/]++)(*:2235)'
                                .'|openphone/integrations/([^/]++)(?'
                                    .'|(*:2278)'
                                    .'|/sync(*:2292)'
                                .')'
                                .'|pa(?'
                                    .'|ckages/([^/]++)(*:2322)'
                                    .'|ges/([^/]++)(*:2343)'
                                .')'
                                .'|r(?'
                                    .'|ankings/([^/]++)(*:2373)'
                                    .'|e(?'
                                        .'|commendations/([^/]++)(?'
                                            .'|(*:2411)'
                                        .')'
                                        .'|views/(?'
                                            .'|([^/]++)(?'
                                                .'|(*:2441)'
                                                .'|/respond(*:2458)'
                                            .')'
                                            .'|sync(*:2472)'
                                        .')'
                                    .')'
                                .')'
                                .'|subscriptions/([^/]++)(*:2506)'
                                .'|users/([^/]++)(*:2529)'
                            .')'
                        .')'
                        .'|agencies(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2577)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2604)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2643)'
                            .')'
                        .')'
                        .'|backlinks(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2692)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2719)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2758)'
                            .')'
                        .')'
                        .'|document(?'
                            .'|s(?'
                                .'|(?:\\.([^/]++))?(*:2799)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2834)'
                                .'|(?:\\.([^/]++))?(*:2858)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:2896)'
                                .')'
                            .')'
                            .'|_(?'
                                .'|signatures(?'
                                    .'|(?:\\.([^/]++))?(*:2939)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2974)'
                                    .'|(?:\\.([^/]++))?(*:2998)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3036)'
                                    .')'
                                .')'
                                .'|templates(?'
                                    .'|(?:\\.([^/]++))?(*:3074)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3109)'
                                    .'|(?:\\.([^/]++))?(*:3133)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3171)'
                                    .')'
                                .')'
                                .'|versions(?'
                                    .'|(?:\\.([^/]++))?(*:3208)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3243)'
                                    .'|(?:\\.([^/]++))?(*:3267)'
                                .')'
                            .')'
                        .')'
                        .'|f(?'
                            .'|aqs(?'
                                .'|(?:\\.([^/]++))?(*:3304)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3339)'
                                .'|(?:\\.([^/]++))?(*:3363)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:3401)'
                                .')'
                            .')'
                            .'|orm(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3448)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3475)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3514)'
                                    .')'
                                .')'
                                .'|_submissions(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3566)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3593)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3632)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|invoices(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3682)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:3709)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:3748)'
                            .')'
                        .')'
                        .'|keywords(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3796)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:3823)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:3862)'
                            .')'
                        .')'
                        .'|lead(?'
                            .'|s(?'
                                .'|(?:\\.([^/]++))?(*:3899)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3934)'
                                .'|(?:\\.([^/]++))?(*:3958)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:3996)'
                                .')'
                            .')'
                            .'|_(?'
                                .'|events(?'
                                    .'|(?:\\.([^/]++))?(*:4035)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4070)'
                                    .'|(?:\\.([^/]++))?(*:4094)'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4129)'
                                .')'
                                .'|sources(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4175)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4202)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4241)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|media_assets(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4295)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:4322)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:4361)'
                            .')'
                        .')'
                        .'|newsletter_subscriptions(?'
                            .'|(?:\\.([^/]++))?(*:4414)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4449)'
                        .')'
                        .'|o(?'
                            .'|_auth_(?'
                                .'|connections(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4512)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4539)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4578)'
                                    .')'
                                .')'
                                .'|tokens(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4624)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4651)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4690)'
                                    .')'
                                .')'
                            .')'
                            .'|pen_phone_(?'
                                .'|call_logs(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4753)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4780)'
                                    .')'
                                .')'
                                .'|integrations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4832)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4859)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4898)'
                                    .')'
                                .')'
                                .'|message_logs(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4950)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4977)'
                                    .')'
                                .')'
                            .')'
                            .'|rganizations(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5030)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5057)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5096)'
                                .')'
                            .')'
                        .')'
                        .'|p(?'
                            .'|a(?'
                                .'|ckages(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5151)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5178)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5217)'
                                    .')'
                                .')'
                                .'|ges(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5260)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5287)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5326)'
                                    .')'
                                .')'
                            .')'
                            .'|osts(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5371)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5398)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5437)'
                                .')'
                            .')'
                        .')'
                        .'|r(?'
                            .'|anking(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5492)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5519)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5558)'
                                    .')'
                                .')'
                                .'|_dailies(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5606)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5633)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5672)'
                                    .')'
                                .')'
                            .')'
                            .'|e(?'
                                .'|commendations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5730)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5757)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5796)'
                                    .')'
                                .')'
                                .'|views(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5841)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:5868)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:5907)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|s(?'
                            .'|eo_metas(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5961)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5988)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6027)'
                                .')'
                            .')'
                            .'|ites(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6071)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6098)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6137)'
                                .')'
                            .')'
                            .'|ubscriptions(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6189)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6216)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6255)'
                                .')'
                            .')'
                            .'|ystem_users(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6306)'
                                .'|(?:\\.([^/]++))?(*:6330)'
                            .')'
                        .')'
                        .'|t(?'
                            .'|ags(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6377)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6404)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6443)'
                                .')'
                            .')'
                            .'|enants(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6489)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6516)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6555)'
                                .')'
                            .')'
                        .')'
                        .'|user(?'
                            .'|s(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6604)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6631)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6670)'
                                .')'
                            .')'
                            .'|_client_accesses(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:6726)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:6753)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:6792)'
                                .')'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/qr\\-code/([^/]++)/([\\w\\W]+)(*:6834)'
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
        2116 => [[['_route' => 'api_v1_leads_events_create', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::createLeadEvent'], ['id'], ['POST' => 0], null, false, false, null]],
        2132 => [[['_route' => 'api_v1_leads_import', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::importLeads'], [], ['POST' => 0], null, false, false, null]],
        2156 => [[['_route' => 'api_v1_leads_leadgen_import', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::importLeadgenData'], [], ['POST' => 0], null, false, false, null]],
        2183 => [[['_route' => 'api_v1_leads_events_list', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLeadEvents'], ['id'], ['GET' => 0], null, false, false, null]],
        2202 => [[['_route' => 'api_v1_leads_statistics', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLeadStatistics'], ['id'], ['GET' => 0], null, false, false, null]],
        2235 => [[['_route' => 'api_v1_media_assets_get', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::getMediaAsset'], ['id'], ['GET' => 0], null, false, true, null]],
        2278 => [
            [['_route' => 'openphone_update_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::updateIntegration'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'openphone_delete_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::deleteIntegration'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        2292 => [[['_route' => 'openphone_sync_integration', '_controller' => 'App\\Controller\\Api\\V1\\OpenPhoneController::syncIntegration'], ['id'], ['POST' => 0], null, false, false, null]],
        2322 => [[['_route' => 'api_v1_packages_get', '_controller' => 'App\\Controller\\Api\\V1\\PackagesController::getPackage'], ['id'], ['GET' => 0], null, false, true, null]],
        2343 => [[['_route' => 'api_v1_pages_get', '_controller' => 'App\\Controller\\Api\\V1\\PagesController::getPage'], ['id'], ['GET' => 0], null, false, true, null]],
        2373 => [[['_route' => 'api_v1_rankings_get', '_controller' => 'App\\Controller\\Api\\V1\\RankingsController::getRanking'], ['id'], ['GET' => 0], null, false, true, null]],
        2411 => [
            [['_route' => 'api_v1_recommendations_get', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::getRecommendation'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_recommendations_update', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::updateRecommendation'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        2441 => [[['_route' => 'api_v1_reviews_get', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::getReview'], ['id'], ['GET' => 0], null, false, true, null]],
        2458 => [[['_route' => 'api_v1_reviews_respond', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::respondToReview'], ['id'], ['POST' => 0], null, false, false, null]],
        2472 => [[['_route' => 'api_v1_reviews_sync', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::syncReviews'], [], ['POST' => 0], null, false, false, null]],
        2506 => [[['_route' => 'api_v1_subscriptions_get', '_controller' => 'App\\Controller\\Api\\V1\\SubscriptionsController::getSubscription'], ['id'], ['GET' => 0], null, false, true, null]],
        2529 => [[['_route' => 'api_v1_users_update', '_controller' => 'App\\Controller\\Api\\V1\\UserController::updateUser'], ['id'], ['PATCH' => 0], null, false, true, null]],
        2577 => [[['_route' => '_api_/agencies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2604 => [
            [['_route' => '_api_/agencies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/agencies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2643 => [
            [['_route' => '_api_/agencies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/agencies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2692 => [[['_route' => '_api_/backlinks/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2719 => [
            [['_route' => '_api_/backlinks{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2758 => [
            [['_route' => '_api_/backlinks/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2799 => [[['_route' => '_api_/documents{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        2834 => [[['_route' => '_api_/documents/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2858 => [[['_route' => '_api_/documents{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        2896 => [
            [['_route' => '_api_/documents/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/documents/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/documents/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Document', '_api_operation_name' => '_api_/documents/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2939 => [[['_route' => '_api_/document_signatures{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        2974 => [[['_route' => '_api_/document_signatures/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2998 => [[['_route' => '_api_/document_signatures{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3036 => [
            [['_route' => '_api_/document_signatures/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/document_signatures/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentSignature', '_api_operation_name' => '_api_/document_signatures/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3074 => [[['_route' => '_api_/document_templates{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3109 => [[['_route' => '_api_/document_templates/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3133 => [[['_route' => '_api_/document_templates{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3171 => [
            [['_route' => '_api_/document_templates/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/document_templates/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/document_templates/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentTemplate', '_api_operation_name' => '_api_/document_templates/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3208 => [[['_route' => '_api_/document_versions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentVersion', '_api_operation_name' => '_api_/document_versions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3243 => [[['_route' => '_api_/document_versions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentVersion', '_api_operation_name' => '_api_/document_versions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3267 => [[['_route' => '_api_/document_versions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\DocumentVersion', '_api_operation_name' => '_api_/document_versions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3304 => [[['_route' => '_api_/faqs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3339 => [[['_route' => '_api_/faqs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3363 => [[['_route' => '_api_/faqs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3401 => [
            [['_route' => '_api_/faqs/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3448 => [[['_route' => '_api_/forms/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3475 => [
            [['_route' => '_api_/forms{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/forms{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3514 => [
            [['_route' => '_api_/forms/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/forms/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3566 => [[['_route' => '_api_/form_submissions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3593 => [
            [['_route' => '_api_/form_submissions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3632 => [
            [['_route' => '_api_/form_submissions/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3682 => [[['_route' => '_api_/invoices/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3709 => [
            [['_route' => '_api_/invoices{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/invoices{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3748 => [
            [['_route' => '_api_/invoices/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/invoices/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3796 => [[['_route' => '_api_/keywords/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3823 => [
            [['_route' => '_api_/keywords{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/keywords{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3862 => [
            [['_route' => '_api_/keywords/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/keywords/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3899 => [[['_route' => '_api_/leads{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3934 => [[['_route' => '_api_/leads/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3958 => [[['_route' => '_api_/leads{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3996 => [
            [['_route' => '_api_/leads/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/leads/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
        ],
        4035 => [[['_route' => '_api_/lead_events{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        4070 => [[['_route' => '_api_/lead_events/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4094 => [[['_route' => '_api_/lead_events{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        4129 => [[['_route' => '_api_/lead_events/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null]],
        4175 => [[['_route' => '_api_/lead_sources/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4202 => [
            [['_route' => '_api_/lead_sources{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4241 => [
            [['_route' => '_api_/lead_sources/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4295 => [[['_route' => '_api_/media_assets/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4322 => [
            [['_route' => '_api_/media_assets{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4361 => [
            [['_route' => '_api_/media_assets/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4414 => [[['_route' => '_api_/newsletter_subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        4449 => [[['_route' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4512 => [[['_route' => '_api_/o_auth_connections/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4539 => [
            [['_route' => '_api_/o_auth_connections{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4578 => [
            [['_route' => '_api_/o_auth_connections/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4624 => [[['_route' => '_api_/o_auth_tokens/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4651 => [
            [['_route' => '_api_/o_auth_tokens{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4690 => [
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4753 => [[['_route' => '_api_/open_phone_call_logs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneCallLog', '_api_operation_name' => '_api_/open_phone_call_logs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4780 => [
            [['_route' => '_api_/open_phone_call_logs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneCallLog', '_api_operation_name' => '_api_/open_phone_call_logs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_call_logs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneCallLog', '_api_operation_name' => '_api_/open_phone_call_logs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4832 => [[['_route' => '_api_/open_phone_integrations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4859 => [
            [['_route' => '_api_/open_phone_integrations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_integrations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4898 => [
            [['_route' => '_api_/open_phone_integrations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_integrations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneIntegration', '_api_operation_name' => '_api_/open_phone_integrations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4950 => [[['_route' => '_api_/open_phone_message_logs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneMessageLog', '_api_operation_name' => '_api_/open_phone_message_logs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4977 => [
            [['_route' => '_api_/open_phone_message_logs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneMessageLog', '_api_operation_name' => '_api_/open_phone_message_logs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/open_phone_message_logs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OpenPhoneMessageLog', '_api_operation_name' => '_api_/open_phone_message_logs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5030 => [[['_route' => '_api_/organizations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5057 => [
            [['_route' => '_api_/organizations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/organizations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5096 => [
            [['_route' => '_api_/organizations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/organizations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5151 => [[['_route' => '_api_/packages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5178 => [
            [['_route' => '_api_/packages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/packages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5217 => [
            [['_route' => '_api_/packages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/packages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5260 => [[['_route' => '_api_/pages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5287 => [
            [['_route' => '_api_/pages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/pages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5326 => [
            [['_route' => '_api_/pages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/pages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5371 => [[['_route' => '_api_/posts/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5398 => [
            [['_route' => '_api_/posts{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/posts{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5437 => [
            [['_route' => '_api_/posts/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/posts/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5492 => [[['_route' => '_api_/rankings/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5519 => [
            [['_route' => '_api_/rankings{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/rankings{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5558 => [
            [['_route' => '_api_/rankings/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/rankings/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5606 => [[['_route' => '_api_/ranking_dailies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5633 => [
            [['_route' => '_api_/ranking_dailies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5672 => [
            [['_route' => '_api_/ranking_dailies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5730 => [[['_route' => '_api_/recommendations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5757 => [
            [['_route' => '_api_/recommendations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5796 => [
            [['_route' => '_api_/recommendations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5841 => [[['_route' => '_api_/reviews/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5868 => [
            [['_route' => '_api_/reviews{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/reviews{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5907 => [
            [['_route' => '_api_/reviews/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/reviews/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5961 => [[['_route' => '_api_/seo_metas/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5988 => [
            [['_route' => '_api_/seo_metas{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6027 => [
            [['_route' => '_api_/seo_metas/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6071 => [[['_route' => '_api_/sites/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6098 => [
            [['_route' => '_api_/sites{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/sites{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6137 => [
            [['_route' => '_api_/sites/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/sites/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6189 => [[['_route' => '_api_/subscriptions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6216 => [
            [['_route' => '_api_/subscriptions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6255 => [
            [['_route' => '_api_/subscriptions/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6306 => [[['_route' => '_api_/system_users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6330 => [[['_route' => '_api_/system_users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        6377 => [[['_route' => '_api_/tags/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6404 => [
            [['_route' => '_api_/tags{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tags{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6443 => [
            [['_route' => '_api_/tags/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/tags/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6489 => [[['_route' => '_api_/tenants/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6516 => [
            [['_route' => '_api_/tenants{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tenants{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6555 => [
            [['_route' => '_api_/tenants/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/tenants/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6604 => [[['_route' => '_api_/users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6631 => [
            [['_route' => '_api_/users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/users{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6670 => [
            [['_route' => '_api_/users/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/users/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6726 => [[['_route' => '_api_/user_client_accesses/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        6753 => [
            [['_route' => '_api_/user_client_accesses{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        6792 => [
            [['_route' => '_api_/user_client_accesses/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        6834 => [
            [['_route' => 'qr_code_generate', '_controller' => 'Endroid\\QrCodeBundle\\Controller\\GenerateController'], ['builder', 'data'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
