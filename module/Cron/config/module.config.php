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
            'sphinx_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Cron/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Cron\Entity' => 'sphinx_entity',
                )
            )
        ),
    ),
    'router' => array(
        'routes' => array(
            'cron' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/cron',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Cron\Controller',
                        'controller' => 'Index',
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
    'console' => array(
        'router' => array(
            'routes' => array(
                'cron-index' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'cron index',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Index',
                            'action' => 'index',
                        ),
                    ),
                ),
                'sphinx-all' => array(
                    'options' => array(
                        'route' => 'sphinx rotate all',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Sphinx',
                            'action' => 'rotate-all-indexes',
                        ),
                    ),
                ),
                'sphinx-pages-delta' => array(
                    'options' => array(
                        'route' => 'sphinx rotate pages-delta',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Sphinx',
                            'action' => 'pages-delta-index',
                        ),
                    ),
                ),
                'sphinx-users-delta' => array(
                    'options' => array(
                        'route' => 'sphinx rotate users-delta',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Sphinx',
                            'action' => 'users-delta-index',
                        ),
                    ),
                ),
                'sphinx-rotate-custom-index' => array(
                    'options' => array(
                        'route' => 'sphinx rotate <index>',
                        'defaults' => array(
                            'controller' => 'Cron\Controller\Sphinx',
                            'action' => 'rotate-custom-index',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Cron\Controller\Index',
                    'roles' => array(),
                ),
                array(
                    'controller' => 'Cron\Controller\Sphinx',
                    'roles' => array(),
                ),
            ),
        ),
    ),
    'service_manager' => array(),
    'controllers' => array(
        'invokables' => array(
            'Cron\Controller\Index' => 'Cron\Controller\IndexController',
            'Cron\Controller\Sphinx' => 'Cron\Controller\SphinxController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
//    'doctrine' => array(
//        'driver' => array(
//            'mail_entity' => array(
//                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
//                'paths' => array(
//                    __DIR__ . '/../src/Mail/Entity',
//                ),
//            ),
//            'orm_default' => array(
//                'drivers' => array(
//                    'Mail\Entity' => 'mail_entity',
//                )
//            )
//        ),
//    ),
//    'navigation' => array(
//        'default' => array(
//            array(
//                'label' => 'Mail',
//                'controller' => 'mail',
//                'pages' => array(
//                    array(
//                        'label' => 'Create',
//                        'controller' => 'management',
//                        'action' => 'create',
//                        'route' => 'mail/default',
//                        'controller_namespace' => 'Mail\Controller\Management',
//                        'module' => 'Mail'
//                    ),
//                    array(
//                        'label' => 'All',
//                        'controller' => 'management',
//                        'action' => 'index',
//                        'route' => 'mail/default',
//                        'controller_namespace' => 'Mail\Controller\Management',
//                        'module' => 'Mail'
//                    )
//                )
//            )
//        )
//    )
);
