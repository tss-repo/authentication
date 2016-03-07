<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 07/03/2016
 * Time: 16:38
 */

namespace TSS\Authentication\Controller\Factory;


use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use TSS\Authentication\Controller\AccountController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceLocator = $container->getServiceLocator();
        $config = $serviceLocator->get('config');
        $entityManager = $serviceLocator->get(EntityManager::class);
        return new AccountController($config, $entityManager);
    }

    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, AccountController::class);
    }
}