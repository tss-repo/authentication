<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 26/08/2015
 * Time: 08:56
 */

namespace TSS\Authentication;


use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Mvc\Application;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

class Module implements AutoloaderProviderInterface, DependencyIndicatorInterface
{
    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var AbstractPluginManager
     */
    protected $pluginManager;

    /**
     * @var HelperPluginManager
     */
    protected $helperManager;

    public function onBootstrap(MvcEvent $e)
    {
        $this->application = $e->getApplication();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($this->getEventManager());
        $this->getEventManager()->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuthentication')); // Authentication
        $this->getEventManager()->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuthorization')); // Authorization
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ ,
                ),
            ),
        );
    }

    public function getModuleDependencies()
    {
        return array(
            'DoctrineORMModule',
            'TSS\Bootstrap',
        );
    }

    /**
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        if($this->serviceManager == null) {
            $this->serviceManager = $this->application->getServiceManager();
        }

        return $this->serviceManager;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if($this->eventManager == null) {
            $this->eventManager = $this->application->getEventManager();
        }

        return $this->eventManager;
    }

    /**
     * @return AbstractPluginManager
     */
    public function getPluginManager()
    {
        if($this->pluginManager == null) {
            $this->pluginManager = $this->getServiceManager()->get('ControllerPluginManager');
        }

        return $this->pluginManager;
    }

    /**
     * @return HelperPluginManager
     */
    public function getHelperManager()
    {
        if($this->helperManager == null) {
            $this->helperManager = $this->getServiceManager()->get('ViewHelperManager');
        }

        return $this->helperManager;
    }

    public function checkAuthentication(MvcEvent $e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches instanceof RouteMatch) {
            return null;
        }

        //framework error
        $eventParams = $e->getParams();
        if(isset($eventParams['error'])) {
            switch ($eventParams['error']) {

                case Application::ERROR_CONTROLLER_NOT_FOUND:
                    $e->getResponse()->setStatusCode(Response::STATUS_CODE_501);
                    break;

                case Application::ERROR_ROUTER_NO_MATCH:
                    $e->getResponse()->setStatusCode(Response::STATUS_CODE_501);
                    break;

                default:
                    $e->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    break;
            }
            $e->stopPropagation();
            return null;
        }

        $controller = $matches->getParam('controller');
        $action = $matches->getParam('action');

        $config = $this->getServiceManager()->get('config');
        $auth = $this->getServiceManager()->get('authentication');
        $acl = $this->getServiceManager()->get('acl');

        $userRole = $acl->getDefaultRole();

        if(!$acl->hasResource($controller)) {
            throw new \Exception('Resource ' . $controller . ' not defined', Response::STATUS_CODE_501);
        }

        if (!$auth->hasIdentity() && (!$acl->isAllowed($userRole, $controller, $action))) {
            $flashMessenger = $this->getPluginManager()->get('flashmessenger');
            $flashMessenger->addErrorMessage(_('Please, sign in.'));

            $router = $e->getRouter();
            $url = $router->assemble($matches->getParams(), array('name' => $config['tss']['authentication']['routes']['signin']['name']));
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            $e->stopPropagation();

            return $response;
        }

        return null;
    }

    public function checkAuthorization(MvcEvent $e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches instanceof RouteMatch) {
            return null;
        }

        $controller = $matches->getParam('controller');
        $action = $matches->getParam('action');

        $auth = $this->serviceManager->get('authentication');
        $acl = $this->serviceManager->get('acl');

        if ($auth->hasIdentity()) {
            $userRole = $auth->getIdentity()->getRole()->getName();
        } else {
            $userRole = $acl->getDefaultRole();
        }

        if(!$acl->hasResource($controller)) {
            throw new \Exception('Resource ' . $controller . ' not defined', Response::STATUS_CODE_501);
        }

        if (!$acl->isAllowed($userRole, $controller, $action)) {
            throw new \Exception('Resource ' . $controller . ' not allow', Response::STATUS_CODE_403);
        } else {
            $navigation = $this->getHelperManager()->get('navigation');
            $navigation->setAcl($acl)->setRole($userRole);
        }

        return null;
    }
}