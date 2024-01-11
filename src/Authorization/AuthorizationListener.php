<?php

namespace LaminasApiToolsAddon\Authorization;

use LaminasApiToolsAddon\Authorization\DummyResource;
use LaminasApiToolsAddon\Authentication\Adapter\BplUserAuthAdapter;
use Laminas\ApiTools\MvcAuth\MvcAuthEvent;
use CirclicalUser\Service\AccessService;

class AuthorizationListener {

    public function __construct(private AccessService $authorizationService, private array $allConfig, private string $httpMethod = "") {
        
    }

    public function __invoke(MvcAuthEvent $mvcAuthEvent) {
        $resourceName = $mvcAuthEvent->getResource();
        $resource = explode('::', $resourceName);
        if (!isset($resource[1])) {
            $resource[1] = 'default';
        }

        $deny = true;
        $authenticationConfig = $this->allConfig['api-tools-mvc-auth']['authentication']['map'] ?? [];
        $authorizationConfig = $this->allConfig['api-tools-mvc-auth']['authorization'];

        $nameArray = explode('\\', $resource[0]);
        $namespace = $nameArray[0] . '\\' . $nameArray[1];

        $handleAuth = isset($authenticationConfig[$namespace]) && $authenticationConfig[$namespace] == BplUserAuthAdapter::getMatchType();

        if ($handleAuth) {

            $isFromApiTools = isset($authorizationConfig[$resource[0]]);
            $isRestResource = $isFromApiTools && str_contains($resourceName, 'entity') || str_contains($resourceName, 'collection');
            $doesRestResourceRequiresAuthorization = $isRestResource &&
                    isset($authorizationConfig[$resource[0]][$resource[1]]) &&
                    $authorizationConfig[$resource[0]][$resource[1]][$this->httpMethod] == true;

            $isRpcAndRequiresAuthorization = $isFromApiTools && !$isRestResource &&
                    isset($authorizationConfig[$resource[0]]['actions']) &&
                    isset($authorizationConfig[$resource[0]]['actions'][$resource[1]]) &&
                    isset($authorizationConfig[$resource[0]]['actions'][$resource[1]][$this->httpMethod]) &&
                    $authorizationConfig[$resource[0]]['actions'][$resource[1]][$this->httpMethod] == true;

            if (
                    $isFromApiTools &&
                    (($isRestResource && $doesRestResourceRequiresAuthorization) || $isRpcAndRequiresAuthorization)
            ) {
                $deny = !$this->authorizationService->isAllowed(new DummyResource($resourceName, $resourceName), $this->httpMethod);
            } else {
                $deny = !$this->authorizationService->canAccessAction($resource[0], $resource[1]);
            }

            if ($deny === true) {
                $mvcAuthEvent->getAuthorizationService()->deny();
            } else {
                return true;
            }
        }
    }
}
