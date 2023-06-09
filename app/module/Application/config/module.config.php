<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index'
                    ]
                ],
            ],
            'clothings-item' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/clothings/:id',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'clothings'
                    ]
                ]
            ],
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api',
                    'defaults' => [
                        'controller' => Controller\Ajax\IndexAjaxController::class
                    ]
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'clothings' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/clothings',
                            'defaults' => [
                                'action' => 'clothings'
                            ]
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'item' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/:id',
                                    'defaults' => [
                                        'action' => 'clothing'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\Ajax\IndexAjaxController::class => Controller\Ajax\Factory\IndexAjaxControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\ClothingService::class => Service\Factory\ClothingServiceFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
