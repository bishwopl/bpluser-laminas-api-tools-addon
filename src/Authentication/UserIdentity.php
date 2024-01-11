<?php

namespace LaminasApiToolsAddon\Authentication;
use Laminas\ApiTools\MvcAuth\Identity\IdentityInterface;

class UserIdentity implements IdentityInterface {
    
    private $user;
    private $name;

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public function getAuthenticationIdentity()
    {
        return $this->user;
    }

    public function getId()
    {
        return $this->user['id'];
    }

    public function getUser()
    {
        return $this->getAuthenticationIdentity();
    }

    public function getRoleId()
    {
        return $this->name;
    }

    // Alias for roleId
    public function setName($name)
    {
        $this->name = $name;
    }

    public function addChild(\Laminas\Permissions\Rbac\RoleInterface $child): void {
        
    }

    public function addParent(\Laminas\Permissions\Rbac\RoleInterface $parent): void {
        
    }

    public function addPermission(string $name): void {
        
    }

    public function getChildren(): iterable {
        return [];
    }

    public function getName(): string {
        return '';
    }

    public function getParents(): iterable {
        return [];
    }

    public function hasPermission(string $name): bool {
        return true;
    }
}
