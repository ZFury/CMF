<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/19/14
 * Time: 4:27 PM
 */

return array(
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Install\Controller\Index',
                    'roles' => array('user'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'install' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/install',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Install\Controller',
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
            'Install\Controller\Index' => 'Install\Controller\IndexController'
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
);
