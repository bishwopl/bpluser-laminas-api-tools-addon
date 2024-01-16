<?php

use LaminasApiToolsAddon\Authentication\Adapter\BplUserAuthAdapter;
use Psr\Container\ContainerInterface;
use CirclicalUser\Service\AuthenticationService;
use Laminas\ApiTools\Admin;

return [
    'circlical' => [
        'user' => [
            'guards' => [
                'laminasApiToolsAddonConfig' => [
                    'controllers' => [
                        Admin\Controller\App::class => ['default' => ['administrator']],
                        Admin\Controller\Authentication::class => ['default' => ['administrator']],
                        Admin\Controller\Authorization::class => ['default' => ['administrator']],
                        Admin\Controller\CacheEnabled::class => ['default' => ['administrator']],
                        Admin\Controller\Config::class => ['default' => ['administrator']],
                        Admin\Controller\FsPermissions::class => ['default' => ['administrator']],
                        Admin\Controller\HttpBasicAuthentication::class => ['default' => ['administrator']],
                        Admin\Controller\HttpDigestAuthentication::class => ['default' => ['administrator']],
                        Admin\Controller\ModuleConfig::class => ['default' => ['administrator']],
                        Admin\Controller\ModuleCreation::class => ['default' => ['administrator']],
                        Admin\Controller\OAuth2Authentication::class => ['default' => ['administrator']],
                        Admin\Controller\Source::class => ['default' => ['administrator']],
                        Admin\Controller\Versioning::class => ['default' => ['administrator']],
                        Admin\Controller\Dashboard::class => ['default' => ['administrator']],
                        Admin\Controller\RestService::class => ['default' => ['administrator']],
                        Admin\Controller\RpcService::class => ['default' => ['administrator']],
                        Admin\Controller\ContentNegotiation::class => ['default' => ['administrator']],
                        Admin\Controller\DbAdapter::class => ['default' => ['administrator']],
                        Admin\Controller\DoctrineAdapter::class => ['default' => ['administrator']],
                        Admin\Controller\InputFilter::class => ['default' => ['administrator']],
                        Admin\Controller\Validators::class => ['default' => ['administrator']],
                        Admin\Controller\Filters::class => ['default' => ['administrator']],
                        Admin\Controller\Module::class => ['default' => ['administrator']],
                        \Laminas\ApiTools\Documentation\Controller::class => ['default' => ['administrator']],
                        \Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineAutodiscovery::class => ['default' => ['administrator']],
                        \Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRestService::class => ['default' => ['administrator']],
                        \Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineMetadataService::class => ['default' => ['administrator']],
                        \Laminas\ApiTools\Admin\Controller\Hydrators::class => ['default' => ['administrator']],
                        \Laminas\ApiTools\OAuth2\Controller\Auth::class => ['default' => ['administrator']],
                        Admin\Controller\DbAutodiscovery::class => ['default' => ['administrator']],
                        Admin\Controller\AuthenticationType::class => ['default' => ['administrator']],
                        Admin\Controller\Documentation::class => ['default' => ['administrator']],
                        \Application\Controller\IndexController::class => ['default' => ['administrator']],
                    ],
                ],
            ],
        ],
    ],
    'api-tools-mvc-auth' => [
        'authentication' => [
            'map' => [
                'Laminas\\ApiTools\\OAuth2' => 'bplUserAdapter',
            ],
            'adapters' => [
                'bplUserAdapter' => [
                    'adapter' => BplUserAuthAdapter::class,
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            BplUserAuthAdapter::class => function(ContainerInterface $c){
                return new BplUserAuthAdapter(
                        $c->get(AuthenticationService::class)
                );
            }
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
