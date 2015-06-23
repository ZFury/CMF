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
                    'roles' => array('guest'),
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
                        'module' => 'install',
                        '__NAMESPACE__' => 'Install\Controller',
                        'controller' => 'Index',
                        'action' => 'database',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:prevStep]]]',
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
    'service_manager' => array(
        'factories' => [
            'Install\Service\Install' => function ($serviceManager) {
                return new Install\Service\Install($serviceManager);
            },
        ]
    ),
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
    'installation' => [
        'files-to-check-global' => [
            ['config' => 'config'],
            ['config-autoload' => 'config/autoload'],
            ['application-config' => 'config/application.config.php']
        ],
        'tools-to-check-global' => [],
        'extensions-to-check-global' => [],
    ],
);
