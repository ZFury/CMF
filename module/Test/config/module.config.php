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
                    'Test\Entity' => 'test_entity',
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
            'Test\Controller\Image' => 'Test\Controller\ImageController',
            'Test\Controller\Audio' => 'Test\Controller\AudioController',
            'Test\Controller\Video' => 'Test\Controller\VideoController',
            'Test\Controller\Comment' => 'Test\Controller\CommentController',
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
                [
                    'controller' => 'Test\Controller\Image',
                    'roles' => ['user'],
                ],
                [
                    'controller' => 'Test\Controller\Audio',
                    'roles' => ['user'],
                ],
                [
                    'controller' => 'Test\Controller\Video',
                    'roles' => ['user'],
                ],
                [
                    'controller' => 'Test\Controller\Comment',
                    'roles' => ['user'],
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Testing Features',
                'controller' => 'test',
                'pages' => [
                    [
                        'label' => 'Upload Images',
                        'controller' => 'image',
                        'action' => 'upload-image',
                        'route' => 'test/default',
                        'controller_namespace' => 'Test\Controller\Image',
                        'module' => 'Test'
                    ],
                    [
                        'label' => 'Upload Audios',
                        'controller' => 'audio',
                        'action' => 'upload-audio',
                        'route' => 'test/default',
                        'controller_namespace' => 'Test\Controller\Audio',
                        'module' => 'Test'
                    ],
                    [
                        'label' => 'Upload Videos',
                        'controller' => 'video',
                        'action' => 'upload-video',
                        'route' => 'test/default',
                        'controller_namespace' => 'Test\Controller\Video',
                        'module' => 'Test'
                    ],
                    [
                        'label' => 'Test Users',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'test/default',
                        'controller_namespace' => 'Test\Controller\Management',
                        'module' => 'Test'
                    ],
                    [
                        'label' => 'Write Comments',
                        'controller' => 'comment',
                        'action' => 'index',
                        'route' => 'test/default',
                        'controller_namespace' => 'Test\Controller\Comment',
                        'module' => 'Test'
                    ]
                ]
            ]
        ]
    ]
];
