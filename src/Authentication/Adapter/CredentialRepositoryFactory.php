<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Authentication\Adapter;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CredentialRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $options = [
            'objectManager' => $container->get(EntityManager::class),
            'identityClass' => $config['tss']['authentication']['config']['identityClass'],
            'identityProperty' => $config['tss']['authentication']['config']['identityProperty'],
            'credentialClass' => $config['tss']['authentication']['config']['credentialClass'],
            'credentialProperty' => $config['tss']['authentication']['config']['credentialProperty'],
            'credentialIdentityProperty' => $config['tss']['authentication']['config']['credentialIdentityProperty'],
            'credential_callable' => $config['tss']['authentication']['config']['credential_callable'],
        ];

        return new CredentialRepository($options);
    }
}
