<?php

namespace LaminasApiToolsAddon\Authentication\Adapter;

use LaminasApiToolsAddon\Authentication\UserIdentity;
use Laminas\ApiTools\MvcAuth\Authentication\AdapterInterface;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\ApiTools\MvcAuth\MvcAuthEvent;
use CirclicalUser\Service\AuthenticationService;
use BplUser\Contract\BplUserInterface;

class BplUserAuthAdapter implements AdapterInterface {
    
    public function __construct(private AuthenticationService $authService) {}
    
    public function provides()
    {
        return [
            'bplUserAdapter',
        ];
    }

    public function matches($type)
    {
        return $type == 'bplUserAdapter';
    }

    public function getTypeFromRequest(Request $request)
    {
        return false;
    }

    public function preAuth(Request $request, Response $response)
    {
    }

    public function authenticate(Request $request, Response $response, MvcAuthEvent $mvcAuthEvent)
    {
        $identity = $this->authService->getIdentity();

        if (is_object($identity) && $identity instanceof BplUserInterface) {
            $userIdentity = new UserIdentity(['id' => $identity->getId()]);
            $userIdentity->setName('user');

            return $userIdentity;
        }

        // Force login for all other routes
        $mvcAuthEvent->stopPropagation();
        //$session->redirect = $request->getUriString();
        $response->getHeaders()->addHeaderLine('Location', '/user/login');
        $response->setStatusCode(302);
        $response->sendHeaders();

        return $response;
    }
    
    public static function getMatchType() : string {
        return 'bplUserAdapter';
    }
}

