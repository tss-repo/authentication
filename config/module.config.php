<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication;

use TSS\Authentication\Controller\AccountControllerFactory;
use TSS\Authentication\Controller\AuthControllerFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'controllers' => [
        'aliases' => [
            'TSS\Authentication\Controller\Account' => Controller\AccountController::class,
            'TSS\Authentication\Controller\Auth' => Controller\AuthController::class,
        ],
        'factories' => [
            Controller\AccountController::class => AccountControllerFactory::class,
            Controller\AuthController::class => AuthControllerFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            'tssAuthentication' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/auth',
                    'defaults' => [
                        'controller' => 'TSS\Authentication\Controller\Auth',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'authenticate' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/authenticate',
                            'defaults' => [
                                'action' => 'authenticate',
                            ],
                        ],
                        'priority' => 9,
                    ],
                    'confirm-email' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/confirm-email/:token',
                            'constraints' => [
                                'token' => '[a-zA-Z0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'confirm-email',
                            ],
                        ],
                        'priority' => 9,
                    ],
                    'default' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:controller[/[:action[/[:id]]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'TSS\Authentication\Controller',
                                'action' => 'index',
                            ],
                        ],
                        'priority' => 5,
                    ],
                    'signin' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/signin',
                            'defaults' => [
                                'action' => 'signin',
                            ],
                        ],
                        'priority' => 9,
                    ],
                    'signout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/signout',
                            'defaults' => [
                                'action' => 'signout',
                            ],
                        ],
                        'priority' => 9,
                    ],
                    'signup' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/signup',
                            'defaults' => [
                                'action' => 'signup',
                            ],
                        ],
                        'priority' => 9,
                    ],
                ],
                'priority' => 10,
            ],
        ],
    ],

    'service_manager' => [
        'aliases' => [
            'Zend\Authentication\AuthenticationService' => 'authentication',
        ],
        'factories' => [
            'acl' => function ($sm) {
                $config = $sm->get('config');
                return new Permissions\Acl\Acl($config['tss']['authentication']['acl']);
            },

            'authentication.adapter' => function ($sm) {
                $config = $sm->get('config');
                $options = [
                    'entityManager' => $sm->get('Doctrine\ORM\EntityManager'),
                    'identityClass' => $config['tss']['authentication']['config']['identityClass'],
                    'identityProperty' => $config['tss']['authentication']['config']['identityProperty'],
                    'credentialClass' => $config['tss']['authentication']['config']['credentialClass'],
                    'credentialProperty' => $config['tss']['authentication']['config']['credentialProperty'],
                    'credentialIdentityProperty' => $config['tss']['authentication']['config']['credentialIdentityProperty'],
                    'credential_callable' => $config['tss']['authentication']['config']['credential_callable'],
                ];

                return new Authentication\Adapter\CredentialRepository($options);
            },

            'authentication.storage' => function ($sm) {
                $config = $sm->get('config');
                $options = [
                    'entityManager' => $sm->get('Doctrine\ORM\EntityManager'),
                    'identityClass' => $config['tss']['authentication']['config']['identityClass'],
                ];

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
        ],
    ],

    'view_manager' => [
        'controller_map' => [
            'TSS\Authentication' => true,
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];