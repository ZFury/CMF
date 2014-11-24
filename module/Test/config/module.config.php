<?php

return [
    'doctrine' => [
        'driver' => [
            'test_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    __DIR__ . '/../src/Test/Entity'
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Test\Entity' => 'test_entity'
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'test' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/test',
                    'defaults' => [
                        '__NAMESPACE__' => 'Test\Controller',
                        'controller' => 'management',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/[:controller][/:action][/:id]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+'
                            ],
                            'defaults' => [],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Test\Controller\Management' => 'Test\Controller\ManagementController',
        ],
    ],
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'bjyauthorize' => [
        'guards' => [
            'BjyAuthorize\Guard\Controller' => [
                [
                    'controller' => 'Test\Controller\Management',
                    'roles' => [],
                ],
            ],
        ],
    ],
];