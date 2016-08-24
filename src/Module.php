<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication;

use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

class Module
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
     * @var ControllerPluginManager
     */
    protected $pluginManager;

    /**
     * @var HelperPluginManager
     */
    protected $helperManager;

    /**
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function onBootstrap($e)
    {
        $this->application = $e->getApplication();
        $eventeManager = $this->application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventeManager);

        $eventeManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAuthentication']); // Authentication
        $eventeManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAuthorization']); // Authorization
    }

    /**
     * Provide application configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();
        return $provider->getConfig();
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
     * @return ControllerPluginManager
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
        $auth = $this->getServiceManager()->get(AuthenticationService::class);
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
            $userRole = $auth->getIdentity()->getRoleName();
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
