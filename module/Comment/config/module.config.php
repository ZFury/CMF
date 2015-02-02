<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'comment_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Comment/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Comment\Entity' => 'comment_entity',
                )
            )
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Comment\Controller\Index',
                    'action' => ['add', 'index', 'edit', 'delete'],
                    'roles' => array('user', 'admin'),
                ),
                array(
                    'controller' => 'Comment\Controller\Index',
                    'action' => ['grid'],
                    'roles' => array('admin'),
                ),
                array(
                    'controller' => 'Comment\Controller\EntityType',
                    'action' => ['create', 'index', 'edit', 'delete'],
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'comment' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/comment',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Comment\Controller',
                        'controller'    => 'index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Comment\Controller\Index' => 'Comment\Controller\IndexController',
            'Comment\Controller\EntityType' => 'Comment\Controller\EntityTypeController',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Comment\Service\EntityType' => function ($sm) {
                return new Comment\Service\EntityType($sm);
            },
            'Comment\Service\Comment' => function ($sm) {
                return new Comment\Service\Comment($sm);
            },
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'comment' => 'Comment\View\Helper\Comment'
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Comment',
                'controller' => 'comment',
                'pages' => array(
                    array(
                        'label' => 'Create entity',
                        'controller' => 'entity-type',
                        'action' => 'create',
                        'route' => 'comment/default',
                        'controller_namespace' => 'Comment\Controller\EntityType',
                        'module' => 'Comment'
                    ),
                    array(
                        'label' => 'All entities',
                        'controller' => 'entity-type',
                        'action' => 'index',
                        'route' => 'comment/default',
                        'controller_namespace' => 'Comment\Controller\EntityType',
                        'module' => 'Comment'
                    ),
                    array(
                        'label' => 'All comments',
                        'controller' => 'index',
                        'action' => 'grid',
                        'route' => 'comment/default',
                        'controller_namespace' => 'Comment\Controller\Index',
                        'module' => 'Comment'
                    )
                )
            )
        )
    )
);
