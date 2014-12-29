<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'user_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                //'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/User/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'User\Entity' => 'user_driver',
                )
            )
        ),
        'configuration' => array(
            'orm_default' => array(
                'types' => array(
                    'enumstatus' => 'User\DBAL\Types\EnumStatusType'
                )
            )
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'User\Entity\Auth',
                'identity_property' => 'foreignKey',
                'credential_property' => 'token',
                'credential_callable' => '\User\Service\Auth::encrypt'
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'Auth',
                        'action' => 'login',
                    ),
                ),
                'may_terminate' => true
            ),
            'user' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
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
                    'confirm' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/signup/confirm/:confirm',
                            'defaults' => array(
                                'controller' => 'signup',
                                'action' => 'confirm',
                            ),
                        ),
                    ),
                    'recover-password' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/auth/recover-password/:hash',
                            'defaults' => array(
                                'controller' => 'auth',
                                'action' => 'recover-password',
                            ),
                        ),
                    ),
                ),
            ),
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Signup' => 'User\Controller\SignupController',
            'User\Controller\Auth' => 'User\Controller\AuthController',
            'User\Controller\Mail' => 'User\Controller\MailController',
            'User\Controller\Management' => 'User\Controller\ManagementController',
            'User\Controller\Profile' => 'User\Controller\ProfileController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'error/403' => __DIR__ . '/../view/error/403.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Db\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Zend\Authentication\AuthenticationService' => function ($serviceManager) {
                // If you are using DoctrineORMModule
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
            'User\Service\User' => function ($sm) {
                return new User\Service\User($sm);
            },
            'User\Service\Auth' => function ($sm) {
                return new User\Service\Auth($sm);
            },
            'User\Provider\Identity\DoctrineProvider' => function ($sm) {
                $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                $authService = $sm->get('Zend\Authentication\AuthenticationService');
                $doctrineProvider = new User\Provider\Identity\DoctrineProvider($entityManager, $authService);
                $doctrineProvider->setServiceLocator($sm);
                $config = $sm->get('BjyAuthorize\Config');
                $doctrineProvider->setDefaultRole($config['default_role']);

                return $doctrineProvider;
            },
            'mail.transport' => function (\Zend\ServiceManager\ServiceManager $serviceManager) {
                $config = $serviceManager->get('Config');
                $transport = new \Zend\Mail\Transport\Smtp();
                $transport->setOptions(new \Zend\Mail\Transport\SmtpOptions($config['mail']['transport']['options']));

                return $transport;
                //return smtp transport...
            },
            'mail.message' => function (\Zend\ServiceManager\ServiceManager $serviceManager) {
                $config = $serviceManager->get('Config');
                $message = new \Zend\Mail\Message();
                $headers = new \Zend\Mail\Headers();
                $headers->addHeaders($config['mail']['message']['headers']);
                $message->setHeaders($headers)->setFrom($config['mail']['message']['from']);
                //uncomment this if you want send email around
                //$message->getHeaders()->addHeaderLine('EXTERNAL', 'true');

                return $message;
            }
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'User\Controller\Auth',
                    'roles' => array('guest', 'user'),
                ),
                array(
                    'controller' => 'User\Controller\Auth',
                    'action' => array('recover-password'),
                    'roles' => array('guest'),
                ),
                array(
                    'controller' => 'User\Controller\Signup',
                    'action' => array('index', 'confirm'),
                    'roles' => array('guest', 'user'),
                ),
                array(
                    'controller' => 'User\Controller\Signup',
                    'action' => array('forgot-password'),
                    'roles' => array('guest'),
                ),
                array(
                    'controller' => 'User\Controller\Mail',
                    'action' => array('index'),
                    'roles' => array('admin'),
                ),
                array(
                    'controller' => 'User\Controller\Management',
                    'action' => array('create'),
                    'roles' => array('user'),
                ),
                array(
                    'controller' => 'User\Controller\Management',
                    'action' => array('grid'),
                    'roles' => array('guest', 'user', 'admin'),
                ),
                array(
                    'controller' => 'User\Controller\Management',
                    'action' => array('get-users'),
                    'roles' => array('guest', 'user', 'admin'),
                ),
                array(
                    'controller' => 'User\Controller\Profile',
//                    'action' => array('index'),
                    'roles' => array('user'),
                ),
                array(
                    'controller' => 'User\Controller\Management',
                    'action' => array('index'),
                    'roles' => array('admin'),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'User',
                'controller' => 'user',
                'pages' => array(
                    array(
                        'label' => 'All users',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'user/default',
                        'controller_namespace' => 'User\Controller\Management',
                        'module' => 'User'
                    ),
                    array(
                        'label' => 'Create user',
                        'controller' => 'management',
                        'action' => 'create',
                        'route' => 'user/default',
                        'controller_namespace' => 'User\Controller\Management',
                        'module' => 'User'
                    )
                )
            )
        )
    )
);
