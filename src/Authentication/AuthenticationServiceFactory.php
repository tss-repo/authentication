<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Authentication;

use Interop\Container\ContainerInterface;
use TSS\Authentication\Authentication\Adapter\CredentialRepository;
use TSS\Authentication\Authentication\Storage\Session;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $storage = $container->get(Session::class);
        $adapter = $container->get(CredentialRepository::class);

        return new AuthenticationService($storage, $adapter);
    }
}