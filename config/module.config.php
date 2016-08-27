<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication;

use Zend\Authentication\AuthenticationService;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'controllers' => [
        'aliases' => [
            'TSS\Authentication\Controller\Account' => Controller\AccountController::class,
            'TSS\Authentication\Controller\Auth' => Controller\AuthController::class
        ],
        'factories' => [
            Controller\AccountController::class => Controller\AccountControllerFactory::class,
            Controller\AuthController::class => Controller\AuthControllerFactory::class
        ]
    ],

    'router' => [
        'routes' => [
            'tssAuthentication' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/auth',
                    'defaults' => [
                        'controller' => 'TSS\Authentication\Controller\Auth',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'authenticate' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/authenticate',
                            'defaults' => [
                                'action' => 'authenticate'
                            ]
                        ],
                        'priority' => 9,
                    ],
                    'confirm-email' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/confirm-email/:token',
                            'constraints' => [
                                'token' => '[a-zA-Z0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'confirm-email'
                            ]
                        ],
                        'priority' => 9
                    ],
                    'default' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:controller[/[:action[/[:id]]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'TSS\Authentication\Controller',
                                'action' => 'index'
                            ]
                        ],
                        'priority' => 5
                    ],
                    'password-recover' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/password-recover/:token',
                            'constraints' => [
                                'token' => '[a-zA-Z0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'password-recover'
                            ]
                        ],
                        'priority' => 9
                    ],
                    'recover' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/recover',
                            'defaults' => [
                                'action' => 'recover'
                            ]
                        ],
                        'priority' => 9
                    ],
                    'signin' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/signin',
                            'defaults' => [
                                'action' => 'signin'
                            ]
                        ],
                        'priority' => 9
                    ],
                    'signout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/signout',
                            'defaults' => [
                                'action' => 'signout'
                            ],
                        ],
                        'priority' => 9
                    ],
                    'signup' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/signup',
                            'defaults' => [
                                'action' => 'signup'
                            ],
                        ],
                        'priority' => 9
                    ],
                ],
                'priority' => 10
            ]
        ]
    ],

    'service_manager' => [
        'factories' => [
            Authentication\Adapter\CredentialRepository::class => Authentication\Adapter\CredentialRepositoryFactory::class,
            Authentication\Storage\Session::class => Authentication\Storage\SessionFactory::class,
            AuthenticationService::class => Authentication\AuthenticationServiceFactory::class,
            Permissions\Acl\Acl::class => Permissions\Acl\AclFactory::class
        ]
    ],

    'view_manager' => [
        'controller_map' => [
            'TSS\Authentication' => true,
        ],
        'template_path_stack' => [
            __DIR__ . '/../view'
        ]
    ]
];