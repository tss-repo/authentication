<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Controller\Plugin;

use TSS\Authentication\Permissions\Acl\Acl;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class IsAllowedPlugin extends AbstractPlugin
{
    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var RoleInterface
     */
    protected $role;

    /**
     * IsAllowed constructor.
     * @param Acl $acl
     * @param RoleInterface $role
     */
    public function __construct(Acl $acl, RoleInterface $role)
    {
        $this->acl = $acl;
        $this->role = $role;
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @return IsAllowedPlugin|bool
     */
    public function __invoke($resource = null, $privilege = null)
    {
        if ($resource === null) {
            return $this;
        }

        return $this->isAllowed($resource, $privilege);
    }

    /**
     * @param string|ResourceInterface $resource
     * @param string $privilege
     * @return bool
     * @throws \RuntimeException
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $this->acl->isAllowed($this->getRole(), $resource, $privilege);
    }

    /**
     * @param Acl $acl
     * @return IsAllowedPlugin
     */
    public function setAcl(Acl $acl = null)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * @return Acl
     */
    protected function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param RoleInterface $role
     * @return IsAllowedPlugin
     */
    public function setRole(RoleInterface $role = null)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return RoleInterface
     */
    protected function getRole()
    {
        return $this->role;
    }
}