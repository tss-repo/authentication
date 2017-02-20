<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use TSS\Authentication\Permissions\Acl\Acl;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\ServiceManager\Factory\FactoryInterface;

class IsAllowedHelper implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth = $container->get(AuthenticationService::class);
        $acl = $container->get(Acl::class);

        $role = $auth->hasIdentity() ? new GenericRole($auth->getIdentity()->getRoleName()) : $acl->getDefaultRole();

        return new \TSS\Authentication\View\Helper\IsAllowedHelper($acl, $role);
    }

}
