<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/docs(?:\\.([^/]++))?(*:62)'
                .'|/\\.well\\-known/genid/([^/]++)(*:98)'
                .'|/validation_errors/([^/]++)(*:132)'
                .'|/(index)?(?:\\.([^/]++))?(*:164)'
                .'|/c(?'
                    .'|ontexts/([^.]+)(?:\\.(jsonld))?(*:207)'
                    .'|a(?'
                        .'|se_studies(?'
                            .'|(?:\\.([^/]++))?(*:247)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:281)'
                            .'|(?:\\.([^/]++))?(*:304)'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:341)'
                            .')'
                        .')'
                        .'|tegories(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:388)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:414)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:452)'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/errors/(\\d+)(?:\\.([^/]++))?(*:492)'
                .'|/validation_errors/([^/]++)(?'
                    .'|(*:530)'
                .')'
                .'|/f(?'
                    .'|aqs(?'
                        .'|(?:\\.([^/]++))?(*:565)'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:599)'
                        .'|(?:\\.([^/]++))?(*:622)'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:659)'
                        .')'
                    .')'
                    .'|orm(?'
                        .'|s(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:705)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:731)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:769)'
                            .')'
                        .')'
                        .'|_submissions(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:820)'
                            .'|(?:\\.([^/]++))?(?'
                                .'|(*:846)'
                            .')'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                .'|(*:884)'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/leads(?'
                    .'|(?:\\.([^/]++))?(*:920)'
                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:954)'
                    .'|(?:\\.([^/]++))?(*:977)'
                .')'
                .'|/p(?'
                    .'|ages(?'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1024)'
                        .'|(?:\\.([^/]++))?(?'
                            .'|(*:1051)'
                        .')'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:1090)'
                        .')'
                    .')'
                    .'|osts(?'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1134)'
                        .'|(?:\\.([^/]++))?(?'
                            .'|(*:1161)'
                        .')'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:1200)'
                        .')'
                    .')'
                .')'
                .'|/s(?'
                    .'|eo_metas(?'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1254)'
                        .'|(?:\\.([^/]++))?(?'
                            .'|(*:1281)'
                        .')'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:1320)'
                        .')'
                    .')'
                    .'|ites(?'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1364)'
                        .'|(?:\\.([^/]++))?(?'
                            .'|(*:1391)'
                        .')'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:1430)'
                        .')'
                    .')'
                .')'
                .'|/t(?'
                    .'|ags(?'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1479)'
                        .'|(?:\\.([^/]++))?(?'
                            .'|(*:1506)'
                        .')'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:1545)'
                        .')'
                    .')'
                    .'|enants(?'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1591)'
                        .'|(?:\\.([^/]++))?(?'
                            .'|(*:1618)'
                        .')'
                        .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                            .'|(*:1657)'
                        .')'
                    .')'
                .')'
                .'|/users(?'
                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:1704)'
                    .'|(?:\\.([^/]++))?(?'
                        .'|(*:1731)'
                    .')'
                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                        .'|(*:1770)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        62 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        98 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => 'true'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        132 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        164 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        207 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        247 => [[['_route' => '_api_/case_studies{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        281 => [[['_route' => '_api_/case_studies/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        304 => [[['_route' => '_api_/case_studies{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        341 => [
            [['_route' => '_api_/case_studies/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/case_studies/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/case_studies/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\CaseStudy', '_api_operation_name' => '_api_/case_studies/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        388 => [[['_route' => '_api_/categories/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        414 => [
            [['_route' => '_api_/categories{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/categories{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        452 => [
            [['_route' => '_api_/categories/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/categories/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Category', '_api_operation_name' => '_api_/categories/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        492 => [[['_route' => '_api_errors', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\State\\ApiResource\\Error', '_api_operation_name' => '_api_errors', '_format' => null], ['status', '_format'], ['GET' => 0], null, false, true, null]],
        530 => [
            [['_route' => '_api_validation_errors_problem', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_problem', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_hydra', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_hydra', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_jsonapi', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_jsonapi', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_xml', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_xml', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
        ],
        565 => [[['_route' => '_api_/faqs{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        599 => [[['_route' => '_api_/faqs/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        622 => [[['_route' => '_api_/faqs{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        659 => [
            [['_route' => '_api_/faqs/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/faqs/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Faq', '_api_operation_name' => '_api_/faqs/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        705 => [[['_route' => '_api_/forms/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        731 => [
            [['_route' => '_api_/forms{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/forms{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        769 => [
            [['_route' => '_api_/forms/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/forms/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Form', '_api_operation_name' => '_api_/forms/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        820 => [[['_route' => '_api_/form_submissions/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        846 => [
            [['_route' => '_api_/form_submissions{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        884 => [
            [['_route' => '_api_/form_submissions/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/form_submissions/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\FormSubmission', '_api_operation_name' => '_api_/form_submissions/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        920 => [[['_route' => '_api_/leads{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null]],
        954 => [[['_route' => '_api_/leads/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        977 => [[['_route' => '_api_/leads{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Lead', '_api_operation_name' => '_api_/leads{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null]],
        1024 => [[['_route' => '_api_/pages/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1051 => [
            [['_route' => '_api_/pages{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/pages{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1090 => [
            [['_route' => '_api_/pages/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/pages/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Page', '_api_operation_name' => '_api_/pages/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1134 => [[['_route' => '_api_/posts/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1161 => [
            [['_route' => '_api_/posts{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/posts{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1200 => [
            [['_route' => '_api_/posts/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/posts/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Post', '_api_operation_name' => '_api_/posts/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1254 => [[['_route' => '_api_/seo_metas/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1281 => [
            [['_route' => '_api_/seo_metas{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1320 => [
            [['_route' => '_api_/seo_metas/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/seo_metas/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\SeoMeta', '_api_operation_name' => '_api_/seo_metas/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1364 => [[['_route' => '_api_/sites/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1391 => [
            [['_route' => '_api_/sites{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/sites{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1430 => [
            [['_route' => '_api_/sites/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/sites/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Site', '_api_operation_name' => '_api_/sites/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1479 => [[['_route' => '_api_/tags/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1506 => [
            [['_route' => '_api_/tags{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tags{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1545 => [
            [['_route' => '_api_/tags/{id}{._format}_put', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_put', '_format' => null], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/tags/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1591 => [[['_route' => '_api_/tenants/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1618 => [
            [['_route' => '_api_/tenants{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/tenants{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1657 => [
            [['_route' => '_api_/tenants/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/tenants/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tenant', '_api_operation_name' => '_api_/tenants/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        1704 => [[['_route' => '_api_/users/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        1731 => [
            [['_route' => '_api_/users{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_get_collection', '_format' => null], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/users{._format}_post', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users{._format}_post', '_format' => null], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        1770 => [
            [['_route' => '_api_/users/{id}{._format}_patch', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_patch', '_format' => null], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/users/{id}{._format}_delete', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_delete', '_format' => null], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
