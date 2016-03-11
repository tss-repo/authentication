<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 26/08/2015
 * Time: 14:08
 */

namespace TSS\Authentication;

return array(
    'controllers' => array(
        'invokables' => array(
            'TSS\Authentication\Controller\Account' => Controller\AccountController::class,
            'TSS\Authentication\Controller\Auth' => Controller\AuthController::class,
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'tss_authentication_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/TSS/Authentication/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'TSS\Authentication' => 'tss_authentication_entities',
                ),
            ),
        ),
    ),

    'router' => array(
        'routes' => array(
            'tssAuthentication' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        'controller' => 'TSS\Authentication\Controller\Auth',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'authenticate' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/authenticate',
                            'defaults' => array(
                                'action' => 'authenticate',
                            ),
                        ),
                        'priority' => 9,
                    ),
                    'confirm-email' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/confirm-email/:token',
                            'constraints' => array(
                                'token' => '[a-zA-Z0-9]*',
                            ),
                            'defaults' => array(
                                'action' => 'confirm-email',
                            ),
                        ),
                        'priority' => 9,
                    ),
                    'default' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/:controller[/[:action[/[:id]]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'TSS\Authentication\Controller',
                                'action' => 'index',
                            ),
                        ),
                        'priority' => 5,
                    ),
                    'signin' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/signin',
                            'defaults' => array(
                                'action' => 'signin',
                            ),
                        ),
                        'priority' => 9,
                    ),
                    'signout' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/signout',
                            'defaults' => array(
                                'action' => 'signout',
                            ),
                        ),
                        'priority' => 9,
                    ),
                    'signup' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/signup',
                            'defaults' => array(
                                'action' => 'signup',
                            ),
                        ),
                        'priority' => 9,
                    ),
                ),
                'priority' => 10,
            ),
        )
    ),

    'service_manager' => array(
        'aliases' => array(
            'Zend\Authentication\AuthenticationService' => 'authentication',
        ),
        'factories' => array(
            'acl' => function ($sm) {
                $config = $sm->get('config');
                return new Permissions\Acl\Acl($config['tss']['authentication']['acl']);
            },

            'authentication.adapter' => function ($sm) {
                $config = $sm->get('config');
                $options = array(
                    'entityManager' => $sm->get('Doctrine\ORM\EntityManager'),
                    'identityClass' => $config['tss']['authentication']['config']['identityClass'],
                    'identityProperty' => $config['tss']['authentication']['config']['identityProperty'],
                    'credentialClass' => $config['tss']['authentication']['config']['credentialClass'],
                    'credentialProperty' => $config['tss']['authentication']['config']['credentialProperty'],
                    'credentialIdentityProperty' => $config['tss']['authentication']['config']['credentialIdentityProperty'],
                    'credential_callable' => $config['tss']['authentication']['config']['credential_callable'],
                );

                return new Authentication\Adapter\CredentialRepository($options);
            },

            'authentication.storage' => function ($sm) {
                $config = $sm->get('config');
                $options = array(
                    'entityManager' => $sm->get('Doctrine\ORM\EntityManager'),
                    'identityClass' => $config['tss']['authentication']['config']['identityClass'],
                );

                return new Authentication\Storage\CredentialStorage($options);
            },

            'authentication' => function ($sm) {
                $authStorage = $sm->get('authentication.storage');
                $authAdapter = $sm->get('authentication.adapter');

                $authService = new \Zend\Authentication\AuthenticationService();
                $authService->setStorage($authStorage);
                $authService->setAdapter($authAdapter);

                return $authService;
            },
        ),
    ),

    'view_manager' => array(
        'controller_map' => array(
            'TSS\Authentication' => true,
        ),
        'template_map' => array(),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
);