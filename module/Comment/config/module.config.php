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
                    'roles' => array('user'),
                ),
                array(
                    'controller' => 'Comment\Controller\Management',
                    'action' => ['create', 'index', 'edit', 'delete'],
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'comment' => array(
//                'type' => 'Segment',
//                'options' => array(
//                    'route' => '/comment[/:action[/:id]]',
//                    'constraints' => array(
//                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'Comment\Controller\Index',
//                    )
//                ),
//                'may_terminate' => true,
//                'child_routes' => array(
//                    'default' => array(
//                        'type'    => 'Segment',
//                        'options' => array(
//                            'route'    => '/[:controller[/:action[/:id]]]',
//                            'constraints' => array(
//                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//                            ),
//                            'defaults' => array(
//
//                            ),
//                        ),
//                    ),
//                ),
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
                            'defaults' => array(

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
            'Comment\Controller\Management' => 'Comment\Controller\ManagementController',
        ),
    ),
    'service_manager' => array(
        'entityTypes' => array(
            'User',
            'Example'
        ),
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

);