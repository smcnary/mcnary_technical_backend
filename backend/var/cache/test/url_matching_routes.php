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
                                .'|invoices/([^/]++)(*:1743)'
                                .'|leads/([^/]++)(?'
                                    .'|(*:1769)'
                                    .'|/events(*:1785)'
                                .')'
                                .'|media\\-assets/([^/]++)(*:1817)'
                                .'|pa(?'
                                    .'|ckages/([^/]++)(*:1846)'
                                    .'|ges/([^/]++)(*:1867)'
                                .')'
                                .'|r(?'
                                    .'|ankings/([^/]++)(*:1897)'
                                    .'|e(?'
                                        .'|commendations/([^/]++)(?'
                                            .'|(*:1935)'
                                        .')'
                                        .'|views/(?'
                                            .'|([^/]++)(?'
                                                .'|(*:1965)'
                                                .'|/respond(*:1982)'
                                            .')'
                                            .'|sync(*:1996)'
                                        .')'
                                    .')'
                                .')'
                                .'|subscriptions/([^/]++)(*:2030)'
                                .'|users/([^/]++)(*:2053)'
                            .')'
                        .')'
                        .'|agencies(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2101)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2128)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2167)'
                            .')'
                        .')'
                        .'|backlinks(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2216)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2243)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2282)'
                            .')'
                        .')'
                        .'|f(?'
                            .'|aqs(?'
                                .'|(?:\\.([^/]++))?(*:2318)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2353)'
                                .'|(?:\\.([^/]++))?(*:2377)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:2415)'
                                .')'
                            .')'
                            .'|orm(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2462)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:2489)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:2528)'
                                    .')'
                                .')'
                                .'|_submissions(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2580)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:2607)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:2646)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|invoices(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2696)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2723)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2762)'
                            .')'
                        .')'
                        .'|keywords(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2810)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:2837)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:2876)'
                            .')'
                        .')'
                        .'|lead(?'
                            .'|s(?'
                                .'|(?:\\.([^/]++))?(*:2913)'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:2948)'
                                .'|(?:\\.([^/]++))?(*:2972)'
                            .')'
                            .'|_(?'
                                .'|events(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3021)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3048)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3087)'
                                    .')'
                                .')'
                                .'|sources(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3134)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3161)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3200)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|media_assets(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3254)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:3281)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:3320)'
                            .')'
                        .')'
                        .'|newsletter_subscriptions(?'
                            .'|(?:\\.([^/]++))?(*:3373)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3408)'
                        .')'
                        .'|o(?'
                            .'|_auth_(?'
                                .'|connections(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3471)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3498)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3537)'
                                    .')'
                                .')'
                                .'|tokens(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3583)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3610)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3649)'
                                    .')'
                                .')'
                            .')'
                            .'|rganizations(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3702)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:3729)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:3768)'
                                .')'
                            .')'
                        .')'
                        .'|p(?'
                            .'|a(?'
                                .'|ckages(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3823)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3850)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3889)'
                                    .')'
                                .')'
                                .'|ges(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:3932)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:3959)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:3998)'
                                    .')'
                                .')'
                            .')'
                            .'|osts(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4043)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4070)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4109)'
                                .')'
                            .')'
                        .')'
                        .'|r(?'
                            .'|anking(?'
                                .'|s(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4164)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4191)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4230)'
                                    .')'
                                .')'
                                .'|_dailies(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4278)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4305)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4344)'
                                    .')'
                                .')'
                            .')'
                            .'|e(?'
                                .'|commendations(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4402)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4429)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4468)'
                                    .')'
                                .')'
                                .'|views(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4513)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:4540)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:4579)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|s(?'
                            .'|eo_metas(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4633)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4660)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4699)'
                                .')'
                            .')'
                            .'|ites(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4743)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4770)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4809)'
                                .')'
                            .')'
                            .'|ubscriptions(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4861)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:4888)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:4927)'
                                .')'
                            .')'
                            .'|ystem_users(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:4978)'
                                .'|(?:\\.([^/]++))?(*:5002)'
                            .')'
                        .')'
                        .'|t(?'
                            .'|ags(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5049)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5076)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5115)'
                                .')'
                            .')'
                            .'|enants(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5161)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5188)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5227)'
                                .')'
                            .')'
                        .')'
                        .'|user(?'
                            .'|s(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5276)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5303)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5342)'
                                .')'
                            .')'
                            .'|_client_accesses(?'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(*:5398)'
                                .'|(?:\\.([^/]++))?(?'
                                    .'|(*:5425)'
                                .')'
                                .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                    .'|(*:5464)'
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
        1743 => [[['_route' => 'api_v1_invoices_get', '_controller' => 'App\\Controller\\Api\\V1\\InvoicesController::getInvoice'], ['id'], ['GET' => 0], null, false, true, null]],
        1769 => [
            [['_route' => 'api_v1_leads_get', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::getLead'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_leads_update', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::updateLead'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1785 => [[['_route' => 'api_v1_leads_events_create', '_controller' => 'App\\Controller\\Api\\V1\\LeadsController::createLeadEvent'], ['id'], ['POST' => 0], null, false, false, null]],
        1817 => [[['_route' => 'api_v1_media_assets_get', '_controller' => 'App\\Controller\\Api\\V1\\MediaAssetsController::getMediaAsset'], ['id'], ['GET' => 0], null, false, true, null]],
        1846 => [[['_route' => 'api_v1_packages_get', '_controller' => 'App\\Controller\\Api\\V1\\PackagesController::getPackage'], ['id'], ['GET' => 0], null, false, true, null]],
        1867 => [[['_route' => 'api_v1_pages_get', '_controller' => 'App\\Controller\\Api\\V1\\PagesController::getPage'], ['id'], ['GET' => 0], null, false, true, null]],
        1897 => [[['_route' => 'api_v1_rankings_get', '_controller' => 'App\\Controller\\Api\\V1\\RankingsController::getRanking'], ['id'], ['GET' => 0], null, false, true, null]],
        1935 => [
            [['_route' => 'api_v1_recommendations_get', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::getRecommendation'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_v1_recommendations_update', '_controller' => 'App\\Controller\\Api\\V1\\RecommendationsController::updateRecommendation'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        1965 => [[['_route' => 'api_v1_reviews_get', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::getReview'], ['id'], ['GET' => 0], null, false, true, null]],
        1982 => [[['_route' => 'api_v1_reviews_respond', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::respondToReview'], ['id'], ['POST' => 0], null, false, false, null]],
        1996 => [[['_route' => 'api_v1_reviews_sync', '_controller' => 'App\\Controller\\Api\\V1\\ReviewsController::syncReviews'], [], ['POST' => 0], null, false, false, null]],
        2030 => [[['_route' => 'api_v1_subscriptions_get', '_controller' => 'App\\Controller\\Api\\V1\\SubscriptionsController::getSubscription'], ['id'], ['GET' => 0], null, false, true, null]],
        2053 => [[['_route' => 'api_v1_users_update', '_controller' => 'App\\Controller\\Api\\V1\\UserController::updateUser'], ['id'], ['PATCH' => 0], null, false, true, null]],
        2101 => [[['_route' => '_api_/agencies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2128 => [
            [['_route' => '_api_/agencies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/agencies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2167 => [
            [['_route' => '_api_/agencies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/agencies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Agency', '_api_operation_name' => '_api_/agencies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2216 => [[['_route' => '_api_/backlinks/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2243 => [
            [['_route' => '_api_/backlinks{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2282 => [
            [['_route' => '_api_/backlinks/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/backlinks/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Backlink', '_api_operation_name' => '_api_/backlinks/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2318 => [[['_route' => '_api_/faqs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        2353 => [[['_route' => '_api_/faqs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2377 => [[['_route' => '_api_/faqs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        2415 => [
            [['_route' => '_api_/faqs/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2462 => [[['_route' => '_api_/forms/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2489 => [
            [['_route' => '_api_/forms{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/forms{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2528 => [
            [['_route' => '_api_/forms/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/forms/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2580 => [[['_route' => '_api_/form_submissions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2607 => [
            [['_route' => '_api_/form_submissions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2646 => [
            [['_route' => '_api_/form_submissions/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2696 => [[['_route' => '_api_/invoices/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2723 => [
            [['_route' => '_api_/invoices{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/invoices{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2762 => [
            [['_route' => '_api_/invoices/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/invoices/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Invoice', '_api_operation_name' => '_api_/invoices/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2810 => [[['_route' => '_api_/keywords/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2837 => [
            [['_route' => '_api_/keywords{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/keywords{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        2876 => [
            [['_route' => '_api_/keywords/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/keywords/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Keyword', '_api_operation_name' => '_api_/keywords/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        2913 => [[['_route' => '_api_/leads{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        2948 => [[['_route' => '_api_/leads/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        2972 => [[['_route' => '_api_/leads{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        3021 => [[['_route' => '_api_/lead_events/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3048 => [
            [['_route' => '_api_/lead_events{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/lead_events{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3087 => [
            [['_route' => '_api_/lead_events/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/lead_events/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadEvent', '_api_operation_name' => '_api_/lead_events/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3134 => [[['_route' => '_api_/lead_sources/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3161 => [
            [['_route' => '_api_/lead_sources{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3200 => [
            [['_route' => '_api_/lead_sources/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/lead_sources/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\LeadSource', '_api_operation_name' => '_api_/lead_sources/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3254 => [[['_route' => '_api_/media_assets/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3281 => [
            [['_route' => '_api_/media_assets{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3320 => [
            [['_route' => '_api_/media_assets/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/media_assets/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\MediaAsset', '_api_operation_name' => '_api_/media_assets/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3373 => [[['_route' => '_api_/newsletter_subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        3408 => [[['_route' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\NewsletterSubscription', '_api_operation_name' => '_api_/newsletter_subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3471 => [[['_route' => '_api_/o_auth_connections/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3498 => [
            [['_route' => '_api_/o_auth_connections{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3537 => [
            [['_route' => '_api_/o_auth_connections/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_connections/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthConnection', '_api_operation_name' => '_api_/o_auth_connections/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3583 => [[['_route' => '_api_/o_auth_tokens/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3610 => [
            [['_route' => '_api_/o_auth_tokens{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3649 => [
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/o_auth_tokens/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\OAuthToken', '_api_operation_name' => '_api_/o_auth_tokens/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3702 => [[['_route' => '_api_/organizations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3729 => [
            [['_route' => '_api_/organizations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/organizations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3768 => [
            [['_route' => '_api_/organizations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/organizations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Organization', '_api_operation_name' => '_api_/organizations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3823 => [[['_route' => '_api_/packages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3850 => [
            [['_route' => '_api_/packages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/packages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3889 => [
            [['_route' => '_api_/packages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/packages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Package', '_api_operation_name' => '_api_/packages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        3932 => [[['_route' => '_api_/pages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        3959 => [
            [['_route' => '_api_/pages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/pages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        3998 => [
            [['_route' => '_api_/pages/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/pages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4043 => [[['_route' => '_api_/posts/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4070 => [
            [['_route' => '_api_/posts{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/posts{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4109 => [
            [['_route' => '_api_/posts/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/posts/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4164 => [[['_route' => '_api_/rankings/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4191 => [
            [['_route' => '_api_/rankings{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/rankings{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4230 => [
            [['_route' => '_api_/rankings/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/rankings/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Ranking', '_api_operation_name' => '_api_/rankings/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4278 => [[['_route' => '_api_/ranking_dailies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4305 => [
            [['_route' => '_api_/ranking_dailies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4344 => [
            [['_route' => '_api_/ranking_dailies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/ranking_dailies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\RankingDaily', '_api_operation_name' => '_api_/ranking_dailies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4402 => [[['_route' => '_api_/recommendations/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4429 => [
            [['_route' => '_api_/recommendations{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4468 => [
            [['_route' => '_api_/recommendations/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/recommendations/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Recommendation', '_api_operation_name' => '_api_/recommendations/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4513 => [[['_route' => '_api_/reviews/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4540 => [
            [['_route' => '_api_/reviews{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/reviews{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4579 => [
            [['_route' => '_api_/reviews/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/reviews/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Review', '_api_operation_name' => '_api_/reviews/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4633 => [[['_route' => '_api_/seo_metas/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4660 => [
            [['_route' => '_api_/seo_metas{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4699 => [
            [['_route' => '_api_/seo_metas/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4743 => [[['_route' => '_api_/sites/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4770 => [
            [['_route' => '_api_/sites{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/sites{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4809 => [
            [['_route' => '_api_/sites/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/sites/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4861 => [[['_route' => '_api_/subscriptions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        4888 => [
            [['_route' => '_api_/subscriptions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        4927 => [
            [['_route' => '_api_/subscriptions/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/subscriptions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Subscription', '_api_operation_name' => '_api_/subscriptions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        4978 => [[['_route' => '_api_/system_users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5002 => [[['_route' => '_api_/system_users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SystemUser', '_api_operation_name' => '_api_/system_users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        5049 => [[['_route' => '_api_/tags/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5076 => [
            [['_route' => '_api_/tags{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tags{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5115 => [
            [['_route' => '_api_/tags/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/tags/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5161 => [[['_route' => '_api_/tenants/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5188 => [
            [['_route' => '_api_/tenants{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tenants{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5227 => [
            [['_route' => '_api_/tenants/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/tenants/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5276 => [[['_route' => '_api_/users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5303 => [
            [['_route' => '_api_/users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/users{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5342 => [
            [['_route' => '_api_/users/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/users/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        5398 => [[['_route' => '_api_/user_client_accesses/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        5425 => [
            [['_route' => '_api_/user_client_accesses{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        5464 => [
            [['_route' => '_api_/user_client_accesses/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/user_client_accesses/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\UserClientAccess', '_api_operation_name' => '_api_/user_client_accesses/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
