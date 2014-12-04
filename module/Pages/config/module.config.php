<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'doctrine' => array(
        'driver' => array(
            'pages_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Pages/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Pages\Entity' => 'pages_entity',
                )
            )
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Pages\Controller\Index',
                    'roles' => array('user'),
                ),
                array(
                    'controller' => 'Pages\Controller\Management',
                    'action' => ['index', 'create',  'view', 'edit', 'delete'],
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'pages' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/pages',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Pages\Controller',
                        'controller'    => 'Index',
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
                                'id'         => '[0-9]*'
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'page_index' => array(
                'type'    => 'Regex',
                'options' => array(
                    'regex' => '/(?<alias>[a-zA-Z0-9_-]+)\.html',
                    'defaults' => array(
                        'controller' => 'Pages\Controller\Index',
                        'action'     => 'index',
                    ),
                    'spec' => '/%alias%.%format%',
                )
            ),
        ),
    ),
    'service_manager' => array(

    ),
    'controllers' => array(
        'invokables' => array(
            'Pages\Controller\Index' => 'Pages\Controller\IndexController',
            'Pages\Controller\Management' => 'Pages\Controller\ManagementController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Pages',
                'controller' => 'pages',
                'pages' => array(
                    array(
                        'label' => 'Create',
                        'controller' => 'management',
                        'action' => 'create',
                        'route' => 'pages/default',
                        'controller_namespace' => 'Pages\Controller\Management',
                        'module' => 'Pages'
                    ),
                    array(
                        'label' => 'All',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'pages/default',
                        'controller_namespace' => 'Pages\Controller\Management',
                        'module' => 'Pages'
                    )
                )
            )
        )
    )
);
