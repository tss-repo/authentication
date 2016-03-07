<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 07/03/2016
 * Time: 16:05
 */

namespace TSS\Authentication\Controller\Factory;


use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use TSS\Authentication\Controller\AuthController;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceLocator = $container->getServiceLocator();
        $config = $serviceLocator->get('config');
        $translator = $serviceLocator->get('translator');
        $authentication = $serviceLocator->get(AuthenticationService::class);
        $entityManager = $serviceLocator->get(EntityManager::class);
        return new AuthController($config, $authentication, $translator, $entityManager);
    }

    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, AuthController::class);
    }
}