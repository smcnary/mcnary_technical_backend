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
        '/api/v1/auth/login' => [[['_route' => 'api_v1_auth_login', '_controller' => 'App\\Controller\\Api\\V1\\AuthController::login'], null, ['POST' => 0], null, false, false, null]],
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
        '/api/v1/faqs' => [[['_route' => 'api_v1_faqs_list', '_controller' => 'App\\Controller\\Api\\V1\\FaqsController::listFaqs'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/invoices' => [[['_route' => 'api_v1_invoices_list', '_controller' => 'App\\Controller\\Api\\V1\\InvoicesController::listInvoices'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/keywords' => [
            [['_route' => 'api_v1_keywords_list', '_controller' => 'App\\Controller\\Api\\V1\\KeywordsController::listKeywords'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_keywords_create', '_controller' => 'App\\Controller\\Api\\V1\\KeywordsController::createKeywords'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/v1/leads' => [[['_route' => 'api_v1_leads_list', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::listLeads'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/media-assets' => [[['_route' => 'api_v1_media_assets_list', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::listMediaAssets'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/packages' => [[['_route' => 'api_v1_packages_list', '_controller' => 'App\\Controller\\Api\\V1\\PackagesController::listPackages'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1/pages' => [[['_route' => 'api_v1_pages_list', '_controller' => 'App\\Controller\\Api\\V1\\PagesController::listPages'], null, ['GET' => 0], null, false, false, null]],
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
                                .')'
                                .'|c(?'
                                    .'|ampaigns/([^/]++)(*:1515)'
                                    .'|itations/([^/]++)(?'
                                        .'|(*:1544)'
                                    .')'
                                    .'|lients/(?'
                                        .'|([^/]++)(?'
                                            .'|(*:1575)'
                                            .'|/locations(?'
                                                .'|(*:1597)'
                                            .')'
                                        .')'
                                        .'|login(*:1613)'
                                        .'|register(*:1630)'
                                    .')'
                                    .'|ontent\\-(?'
                                        .'|briefs/([^/]++)(*:1666)'
                                        .'|items/([^/]++)(?'
                                            .'|(*:1692)'
                                        .')'
                                    .')'
                                .')'
                                .'|faqs/([^/]++)(*:1717)'
                                .'|gbp/(?'
                                    .'|kpi/([^/]++)(*:1745)'
                                    .'|connect/([^/]++)(*:1770)'
                                .')'
                                .'|invoices/([^/]++)(*:1797)'
                                .'|leads/([^/]++)(?'
                                    .'|(*:1823)'
                                    .'|/events(*:1839)'
                                .')'
                                .'|media\\-assets/([^/]++)(*:1871)'
                                .'|pa(?'
                                    .'|ckages/([^/]++)(*:1900)'
                                    .'|ges/([^/]++)(*:1921)'
                                .')'
                                .'|r(?'
                                    .'|ankings/([^/]++)(*:1951)'
                                    .'|e(?'
                                        .'|commendations/([^/]++)(?'
                                            .'|(*:1989)'
                                        .')'
                                        .'|views/(?'
                                            .'|([^/]++)(?'
                                                .'|(*:2019)'
                                                .'|/respond(*:2036)'
                                            .')'
                                            .'|sync(*:2050)'
                                        .')'
                                    .')'
                                .')'
                                .'|subscriptions/([^/]++)(*:2084)'
                                .'|users/([^/]++)(*:2107)'
                            .')'
                        .')'
                        .'|agencies(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2155)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2182)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2221)'
                            .')'
                        .')'
                        .'|backlinks(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2270)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2297)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2336)'
                            .')'
                        .')'
                        .'|f(?'
                            .'|aqs(?'
                                .'|(?:\\.([^/]++))?(*:2372)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2407)'
                                .'|(?:\\.([^/]++))?(*:2431)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:2469)'
                                .')'
                            .')'
                            .'|orm(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2516)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:2543)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:2582)'
                                    .')'
                                .')'
                                .'|_submissions(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2634)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:2661)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:2700)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|invoices(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2750)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2777)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2816)'
                            .')'
                        .')'
                        .'|keywords(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2864)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2891)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2930)'
                            .')'
                        .')'
                        .'|lead(?'
                            .'|s(?'
                                .'|(?:\\.([^/]++))?(*:2967)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3002)'
                                .'|(?:\\.([^/]++))?(*:3026)'
                            .')'
                            .'|_(?'
                                .'|events(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3075)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3102)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3141)'
                                    .')'
                                .')'
                                .'|sources(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3188)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3215)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3254)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|media_assets(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3308)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:3335)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:3374)'
                            .')'
                        .')'
                        .'|newsletter_subscriptions(?'
                            .'|(?:\\.([^/]++))?(*:3427)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3462)'
                        .')'
                        .'|o(?'
                            .'|_auth_(?'
                                .'|connections(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3525)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3552)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3591)'
                                    .')'
                                .')'
                                .'|tokens(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3637)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3664)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3703)'
                                    .')'
                                .')'
                            .')'
                            .'|rganizations(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3756)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:3783)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:3822)'
                                .')'
                            .')'
                        .')'
                        .'|p(?'
                            .'|a(?'
                                .'|ckages(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3877)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3904)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3943)'
                                    .')'
                                .')'
                                .'|ges(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3986)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4013)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4052)'
                                    .')'
                                .')'
                            .')'
                            .'|osts(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4097)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4124)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4163)'
                                .')'
                            .')'
                        .')'
                        .'|r(?'
                            .'|anking(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4218)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4245)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4284)'
                                    .')'
                                .')'
                                .'|_dailies(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4332)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4359)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4398)'
                                    .')'
                                .')'
                            .')'
                            .'|e(?'
                                .'|commendations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4456)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4483)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4522)'
                                    .')'
                                .')'
                                .'|views(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4567)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4594)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4633)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|s(?'
                            .'|eo_metas(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4687)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4714)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4753)'
                                .')'
                            .')'
                            .'|ites(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4797)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4824)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4863)'
                                .')'
                            .')'
                            .'|ubscriptions(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4915)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4942)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4981)'
                                .')'
                            .')'
                            .'|ystem_users(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5032)'
                                .'|(?:\\.([^/]++))?(*:5056)'
                            .')'
                        .')'
                        .'|t(?'
                            .'|ags(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5103)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5130)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5169)'
                                .')'
                            .')'
                            .'|enants(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5215)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5242)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5281)'
                                .')'
                            .')'
                        .')'
                        .'|user(?'
                            .'|s(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5330)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5357)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5396)'
                                .')'
                            .')'
                            .'|_client_accesses(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5452)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5479)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5518)'
                                .')'
                            .')'
                        .')'
                    .')'
                .')'
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
        1515 => [[['_route' => 'api_v1_campaigns_get', '_controller' => 'App\\Controller\\Api\\V1\\CampaignsController::getCampaign'], ['id'], ['GET' => 0], null, false, true, null]],
        1544 => [
            [['_route' => 'api_v1_citations_get', '_controller' => 'App\\Controller\\Api\\V1\\CitationsController::getCitation'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_citations_update', '_controller' => 'App\\Controller\\Api\\V1\\CitationsController::updateCitation'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1575 => [
            [['_route' => 'api_v1_clients_get', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::getClient'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_clients_update', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::updateClient'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1597 => [
            [['_route' => 'api_v1_clients_locations_get', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::getClientLocations'], ['id'], ['GET' => 0], null, false, false, null],
            [['_route' => 'api_v1_clients_locations_create', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::createClientLocation'], ['id'], ['POST' => 0], null, false, false, null],
        ],
        1613 => [[['_route' => 'api_v1_clients_login', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::clientLogin'], [], ['POST' => 0], null, false, false, null]],
        1630 => [[['_route' => 'api_v1_clients_register', '_controller' => 'App\\Controller\\Api\\V1\\ClientController::registerClient'], [], ['POST' => 0], null, false, false, null]],
        1666 => [[['_route' => 'api_v1_content_briefs_get', '_controller' => 'App\\Controller\\Api\\V1\\ContentBriefsController::getContentBrief'], ['id'], ['GET' => 0], null, false, true, null]],
        1692 => [
            [['_route' => 'api_v1_content_items_get', '_controller' => 'App\\Controller\\Api\\V1\\ContentItemsController::getContentItem'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_content_items_update', '_controller' => 'App\\Controller\\Api\\V1\\ContentItemsController::updateContentItem'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1717 => [[['_route' => 'api_v1_faqs_get', '_controller' => 'App\\Controller\\Api\\V1\\FaqsController::getFaq'], ['id'], ['GET' => 0], null, false, true, null]],
        1745 => [[['_route' => 'api_v1_gbp_kpi', '_controller' => 'App\\Controller\\Api\\V1\\GoogleBusinessProfileController::getGbpKpi'], ['clientId'], ['GET' => 0], null, false, true, null]],
        1770 => [[['_route' => 'api_v1_gbp_connect', '_controller' => 'App\\Controller\\Api\\V1\\GoogleBusinessProfileController::connectGbp'], ['clientId'], ['POST' => 0], null, false, true, null]],
        1797 => [[['_route' => 'api_v1_invoices_get', '_controller' => 'App\\Controller\\Api\\V1\\InvoicesController::getInvoice'], ['id'], ['GET' => 0], null, false, true, null]],
        1823 => [
            [['_route' => 'api_v1_leads_get', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLead'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_leads_update', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::updateLead'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1839 => [[['_route' => 'api_v1_leads_events_create', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::createLeadEvent'], ['id'], ['POST' => 0], null, false, false, null]],
        1871 => [[['_route' => 'api_v1_media_assets_get', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::getMediaAsset'], ['id'], ['GET' => 0], null, false, true, null]],
        1900 => [[['_route' => 'api_v1_packages_get', '_controller' => 'App\\Controller\\Api\\V1\\PackagesController::getPackage'], ['id'], ['GET' => 0], null, false, true, null]],
        1921 => [[['_route' => 'api_v1_pages_get', '_controller' => 'App\\Controller\\Api\\V1\\PagesController::getPage'], ['id'], ['GET' => 0], null, false, true, null]],
        1951 => [[['_route' => 'api_v1_rankings_get', '_controller' => 'App\\Controller\\Api\\V1\\RankingsController::getRanking'], ['id'], ['GET' => 0], null, false, true, null]],
        1989 => [
            [['_route' => 'api_v1_recommendations_get', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::getRecommendation'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_recommendations_update', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::updateRecommendation'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        2019 => [[['_route' => 'api_v1_reviews_get', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::getReview'], ['id'], ['GET' => 0], null, false, true, null]],
        2036 => [[['_route' => 'api_v1_reviews_respond', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::respondToReview'], ['id'], ['POST' => 0], null, false, false, null]],
        2050 => [[['_route' => 'api_v1_reviews_sync', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::syncReviews'], [], ['POST' => 0], null, false, false, null]],
        2084 => [[['_route' => 'api_v1_subscriptions_get', '_controller' => 'App\\Controller\\Api\\V1\\SubscriptionsController::getSubscription'], ['id'], ['GET' => 0], null, false, true, null]],
        2107 => [[['_route' => 'api_v1_users_update', '_controller' => 'App\\Controller\\Api\\V1\\UserController::updateUser'], ['id'], ['PATCH' => 0], null, false, true, null]],
        2155 => [[['_route' => '_api_/agencies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2182 => [
            [['_route' => '_api_/agencies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/agencies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2221 => [
            [['_route' => '_api_/agencies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/agencies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2270 => [[['_route' => '_api_/backlinks/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2297 => [
            [['_route' => '_api_/backlinks{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2336 => [
            [['_route' => '_api_/backlinks/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2372 => [[['_route' => '_api_/faqs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        2407 => [[['_route' => '_api_/faqs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2431 => [[['_route' => '_api_/faqs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        2469 => [
            [['_route' => '_api_/faqs/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2516 => [[['_route' => '_api_/forms/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2543 => [
            [['_route' => '_api_/forms{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/forms{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2582 => [
            [['_route' => '_api_/forms/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/forms/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2634 => [[['_route' => '_api_/form_submissions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2661 => [
            [['_route' => '_api_/form_submissions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2700 => [
            [['_route' => '_api_/form_submissions/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2750 => [[['_route' => '_api_/invoices/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2777 => [
            [['_route' => '_api_/invoices{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/invoices{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2816 => [
            [['_route' => '_api_/invoices/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/invoices/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2864 => [[['_route' => '_api_/keywords/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2891 => [
            [['_route' => '_api_/keywords{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/keywords{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2930 => [
            [['_route' => '_api_/keywords/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/keywords/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2967 => [[['_route' => '_api_/leads{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3002 => [[['_route' => '_api_/leads/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3026 => [[['_route' => '_api_/leads{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3075 => [[['_route' => '_api_/lead_events/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3102 => [
            [['_route' => '_api_/lead_events{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/lead_events{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3141 => [
            [['_route' => '_api_/lead_events/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/lead_events/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3188 => [[['_route' => '_api_/lead_sources/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3215 => [
            [['_route' => '_api_/lead_sources{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3254 => [
            [['_route' => '_api_/lead_sources/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3308 => [[['_route' => '_api_/media_assets/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3335 => [
            [['_route' => '_api_/media_assets{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3374 => [
            [['_route' => '_api_/media_assets/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3427 => [[['_route' => '_api_/newsletter_subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3462 => [[['_route' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3525 => [[['_route' => '_api_/o_auth_connections/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3552 => [
            [['_route' => '_api_/o_auth_connections{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3591 => [
            [['_route' => '_api_/o_auth_connections/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3637 => [[['_route' => '_api_/o_auth_tokens/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3664 => [
            [['_route' => '_api_/o_auth_tokens{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3703 => [
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3756 => [[['_route' => '_api_/organizations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3783 => [
            [['_route' => '_api_/organizations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/organizations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3822 => [
            [['_route' => '_api_/organizations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/organizations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3877 => [[['_route' => '_api_/packages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3904 => [
            [['_route' => '_api_/packages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/packages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3943 => [
            [['_route' => '_api_/packages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/packages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3986 => [[['_route' => '_api_/pages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4013 => [
            [['_route' => '_api_/pages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/pages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4052 => [
            [['_route' => '_api_/pages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/pages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4097 => [[['_route' => '_api_/posts/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4124 => [
            [['_route' => '_api_/posts{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/posts{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4163 => [
            [['_route' => '_api_/posts/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/posts/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4218 => [[['_route' => '_api_/rankings/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4245 => [
            [['_route' => '_api_/rankings{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/rankings{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4284 => [
            [['_route' => '_api_/rankings/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/rankings/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4332 => [[['_route' => '_api_/ranking_dailies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4359 => [
            [['_route' => '_api_/ranking_dailies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4398 => [
            [['_route' => '_api_/ranking_dailies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4456 => [[['_route' => '_api_/recommendations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4483 => [
            [['_route' => '_api_/recommendations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4522 => [
            [['_route' => '_api_/recommendations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4567 => [[['_route' => '_api_/reviews/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4594 => [
            [['_route' => '_api_/reviews{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/reviews{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4633 => [
            [['_route' => '_api_/reviews/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/reviews/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4687 => [[['_route' => '_api_/seo_metas/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4714 => [
            [['_route' => '_api_/seo_metas{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4753 => [
            [['_route' => '_api_/seo_metas/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4797 => [[['_route' => '_api_/sites/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4824 => [
            [['_route' => '_api_/sites{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/sites{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4863 => [
            [['_route' => '_api_/sites/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/sites/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4915 => [[['_route' => '_api_/subscriptions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4942 => [
            [['_route' => '_api_/subscriptions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4981 => [
            [['_route' => '_api_/subscriptions/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5032 => [[['_route' => '_api_/system_users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5056 => [[['_route' => '_api_/system_users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        5103 => [[['_route' => '_api_/tags/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5130 => [
            [['_route' => '_api_/tags{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tags{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5169 => [
            [['_route' => '_api_/tags/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/tags/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5215 => [[['_route' => '_api_/tenants/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5242 => [
            [['_route' => '_api_/tenants{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tenants{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5281 => [
            [['_route' => '_api_/tenants/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/tenants/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5330 => [[['_route' => '_api_/users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5357 => [
            [['_route' => '_api_/users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/users{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5396 => [
            [['_route' => '_api_/users/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/users/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5452 => [[['_route' => '_api_/user_client_accesses/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5479 => [
            [['_route' => '_api_/user_client_accesses{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5518 => [
            [['_route' => '_api_/user_client_accesses/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
