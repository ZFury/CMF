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
            'options_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Options/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Options\Entity' => 'options_entity',
                )
            )
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Options\Controller\Index',
                    'roles' => array('user'),
                ),
                array(
                    'controller' => 'Options\Controller\Management',
                    'action' => ['index', 'create',  'view', 'edit', 'delete'],
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'options' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/options',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Options\Controller',
                        'controller'    => 'Management',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:namespace[/:key]]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'namespace' => '[a-zA-Z0-9_-]*',
                                'key' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(

    ),
    'controllers' => array(
        'invokables' => array(
            'Options\Controller\Index' => 'Options\Controller\IndexController',
            'Options\Controller\Management' => 'Options\Controller\ManagementController'
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
                'label' => 'Option',
                'controller' => 'option',
                'pages' => array(
                    array(
                        'label' => 'Create',
                        'controller' => 'management',
                        'action' => 'create',
                        'route' => 'options/default',
                        'controller_namespace' => 'Options\Controller\Management',
                        'module' => 'Options'
                    ),
                    array(
                        'label' => 'All',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'options/default',
                        'controller_namespace' => 'Options\Controller\Management',
                        'module' => 'Options'
                    )
                )
            )
        )
    )
);
