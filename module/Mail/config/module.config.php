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
            'mail_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Mail/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Mail\Entity' => 'mail_entity',
                )
            )
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Mail\Controller\Index',
                    'roles' => array(),
                ),
                array(
                    'controller' => 'Mail\Controller\Management',
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'mail' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/mail',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Mail\Controller',
                        'controller' => 'Management',
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
                                'id' => '[0-9]*'
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(),
    'controllers' => array(
        'invokables' => array(
            'Mail\Controller\Index' => 'Mail\Controller\IndexController',
            'Mail\Controller\Management' => 'Mail\Controller\ManagementController'
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
            'routes' => array(),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Mail',
                'controller' => 'mail',
                'pages' => array(
                    array(
                        'label' => 'Create',
                        'controller' => 'management',
                        'action' => 'create',
                        'route' => 'mail/default',
                        'controller_namespace' => 'Mail\Controller\Management',
                        'module' => 'Mail'
                    ),
                    array(
                        'label' => 'All',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'mail/default',
                        'controller_namespace' => 'Mail\Controller\Management',
                        'module' => 'Mail'
                    )
                )
            )
        )
    )
);
