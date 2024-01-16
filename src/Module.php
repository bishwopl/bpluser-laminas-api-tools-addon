<?php

namespace LaminasApiToolsAddon;

use LaminasApiToolsAddon\Authorization\AuthorizationListener;
use LaminasApiToolsAddon\Authentication\Adapter\BplUserAuthAdapter;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ApiTools\MvcAuth\Authentication\DefaultAuthenticationListener;
use Laminas\ApiTools\MvcAuth\MvcAuthEvent;
use Laminas\EventManager\EventInterface;
use CirclicalUser\Service\AccessService;

class Module implements ConfigProviderInterface, BootstrapListenerInterface {

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e) {
        $app = $e->getApplication();
        $container = $app->getServiceManager();

        $defaultAuthenticationListener = $container->get(DefaultAuthenticationListener::class);
        $defaultAuthenticationListener->attach($container->get(BplUserAuthAdapter::class));

        $request = $container->get('Request');
        $httpMethod = $request->getMethod();

        $eventManager = $app->getEventManager();
        $allConfig = $container->get('Config');
        $eventManager->attach(
                MvcAuthEvent::EVENT_AUTHORIZATION,
                new AuthorizationListener($container->get(AccessService::class),$allConfig,$httpMethod),
                100
        );
    }
}
