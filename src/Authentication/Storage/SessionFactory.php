<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Authentication\Storage;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SessionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!is_null($options)) {
            if (isset($options['namespace'])) {
                $namespace = $options['namespace'];
            } else {
                $namespace = null;
            }

            if (isset($options['member'])) {
                $member = $options['member'];
            } else {
                $member = null;
            }

            if (isset($options['manager'])) {
                $manager = $options['manager'];
            } else {
                $manager = null;
            }
        } else {
            $namespace = null;
            $member = null;
            $manager = null;
        }

        $config = $container->get('config');
        $options = [
            'objectManager' => $container->get(EntityManager::class),
            'identityClass' => $config['tss']['authentication']['config']['identityClass'],
        ];

        return new Session($namespace, $member, $manager, $options);
    }

}