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
                    'roles' => array(),
                ),
                array(
                    'controller' => 'Options\Controller\Management',
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
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'Db\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Zend\Authentication\AuthenticationService' => function ($serviceManager) {
                // If you are using DoctrineORMModule:
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
            'Options\Entity\Options' => function ($sm) {
                return new Options\Entity\Options();
            },
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Options\Controller\Index' => 'Options\Controller\IndexController',
            'Options\Controller\Management' => 'Options\Controller\ManagementController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
