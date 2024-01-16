<?php

namespace LaminasApiToolsAddon\Authorization;

use LaminasApiToolsAddon\Authorization\DummyResource;
use LaminasApiToolsAddon\Authentication\Adapter\BplUserAuthAdapter;
use Laminas\ApiTools\MvcAuth\MvcAuthEvent;
use CirclicalUser\Service\AccessService;

class AuthorizationListener {

    public function __construct(private AccessService $authorizationService, private array $allConfig, private string $httpMethod = "") { }

    public function __invoke(MvcAuthEvent $mvcAuthEvent) {
        $resourceName = $resourceId = $mvcAuthEvent->getResource();
        $resource = explode('::', $resourceName);
        if (!isset($resource[1])) {
            $resource[1] = 'default';
        }

        $deny = true;
        $authenticationConfig = $this->allConfig['api-tools-mvc-auth']['authentication']['map'] ?? [];
        $authorizationConfig = $this->allConfig['api-tools-mvc-auth']['authorization'][$resource[0]]??[];
        

        $nameArray = explode('\\', $resource[0]);
        $namespace = $nameArray[0] . '\\' . $nameArray[1];

        $handleAuth = isset($authenticationConfig[$namespace]) && $authenticationConfig[$namespace] == BplUserAuthAdapter::getMatchType();

        if ($handleAuth) {

            $isFromApiTools = sizeof($authorizationConfig) > 0;
            $isEntity = str_contains($resourceName, 'entity');
            $isCollection = str_contains($resourceName, 'collection');
            $isRestResource = $isFromApiTools && ($isEntity || $isCollection);
            $doesRestResourceRequiresAuthorization = $isRestResource &&
                    isset($authorizationConfig[$resource[1]]) &&
                    $authorizationConfig[$resource[1]][$this->httpMethod] == true;

            $isRpcAndRequiresAuthorization = $isFromApiTools && !$isRestResource &&
                    isset($authorizationConfig['actions']) &&
                    isset($authorizationConfig['actions'][$resource[1]]) &&
                    isset($authorizationConfig['actions'][$resource[1]][$this->httpMethod]) &&
                    $authorizationConfig['actions'][$resource[1]][$this->httpMethod] == true;

            if (
                    $isFromApiTools &&
                    (($isRestResource && $doesRestResourceRequiresAuthorization) || $isRpcAndRequiresAuthorization)
            ) {
                if($isRestResource && $isEntity){
                    $resourceConfig = $this->allConfig['api-tools-rest'][$resource[0]];
                    $identifier = $mvcAuthEvent->getMvcEvent()->getRouteMatch()->getParam($resourceConfig['route_identifier_name']);
                    $resourceId = $identifier??$resourceId;
                }
                
                $allowed = $this->authorizationService->isAllowedByResourceClass($resourceName, $this->httpMethod);
                if(!$allowed){
                    $allowed = $this->authorizationService->isAllowed(new DummyResource($resourceName, $resourceId), $this->httpMethod);
                }
                $deny = !$allowed;
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
