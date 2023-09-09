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
                    ],
                ],
            ],
            'clothing' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/clothings/:uuid',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'clothing'
                    ],
                ],
            ],
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api',
                    'defaults' => [
                        'controller' => Controller\Rest\ClothingRestController::class
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'clothings' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/clothings',
                            'defaults' => [
                                'action' => 'get-clothings'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'item' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/:uuid',
                                    'defaults' => [
                                        'action' => 'get-clothing'
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'images' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/images',
                                            'defaults' => [
                                                'action' => 'get-clothing-images'
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes' => [
                                            'item' => [
                                                'type' => Segment::class,
                                                'options' => [
                                                    'route' => '/:image_id',
                                                    'defaults' => [
                                                        'action' => 'get-clothing-image'
                                                    ],
                                                ],
                                            ],
                                            'main-item' => [
                                                'type' => Literal::class,
                                                'options' => [
                                                    'route' => '/main',
                                                    'defaults' => [
                                                        'action' => 'get-clothing-main-image'
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\Rest\ClothingRestController::class => Controller\Rest\Factory\ClothingRestControllerFactory::class,
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
