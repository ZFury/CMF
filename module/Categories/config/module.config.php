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
                            'route' => '/[:controller[/:action]]',
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
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/management/edit/:id',
                            'defaults' => array(
                                'controller' => 'management',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/management/delete/:id',
                            'defaults' => array(
                                'controller' => 'management',
                                'action' => 'delete',
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
        'template_map' => array(
            'error/403' => __DIR__ . '/../view/error/403.phtml',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Db\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Zend\Authentication\AuthenticationService' => function ($serviceManager) {
                // If you are using DoctrineORMModule:
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
            'Categories\Entity\Categories' => function ($sm) {
                return new Categories\Entity\Categories();
            },
//            'Categories\Service\Categories' => function ($sm) {
//                return new Categories\Service\Categories($sm);
//            },
            'Categories\Provider\Identity\DoctrineProvider' => function ($sm) {
                $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                $authService = $sm->get('Zend\Authentication\AuthenticationService');
                $doctrineProvider = new Categories\Provider\Identity\DoctrineProvider($entityManager, $authService);
                $doctrineProvider->setServiceLocator($sm);
                $config = $sm->get('BjyAuthorize\Config');
                $doctrineProvider->setDefaultRole($config['default_role']);

                return $doctrineProvider;
            },
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Categories\Controller\Management',
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
);