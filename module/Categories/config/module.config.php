<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'categories_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                //'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Categories/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Categories\Entity' => 'categories_driver',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'categories' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/categories',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Categories\Controller',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                    'create' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/management/create[/:parentId]',
                            'defaults' => array(
                                'controller' => 'management',
                                'action' => 'create',
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Categories\Controller\Management' => 'Categories\Controller\ManagementController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'treeBuild' => 'Categories\Helper\TreeBuild'
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Categories\Entity\Categories' => function ($sm) {
                return new Categories\Entity\Categories();
            },
            'Categories\Service\Categories' => function ($sm) {
                return new Categories\Service\Categories($sm);
            },
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Categories\Controller\Management',
                    'action' => ['create', 'index', 'edit', 'order', 'delete', 'start-image-upload', 'delete-image'],
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Category',
                'controller' => 'category',
                'pages' => array(
                    array(
                        'label' => 'Create',
                        'controller' => 'management',
                        'action' => 'create',
                        'route' => 'categories/default',
                        'controller_namespace' => 'Categories\Controller\Management',
                        'module' => 'Categories'
                    ),
                    array(
                        'label' => 'All',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'categories/default',
                        'controller_namespace' => 'Categories\Controller\Management',
                        'module' => 'Categories'
                    )
                )
            )
        )
    )
);
