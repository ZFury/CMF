<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'comment_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                //'cache' => 'array',
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
//                    'action' => array('add'),
                    'roles' => array('user'),
                )
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'comment' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/comment[/:action[:id]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
//                        '__NAMESPACE__' => 'Comment\Controller',
                        'controller' => 'Comment\Controller\Index',
                 //       'action' => 'add',
                    )
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
        ),
    ),
    'service_manager' => array(
        'entityTypes' => array(
            'User',
            'Example'
        ),
        'factories' => array(
            'Comment\Service\EntityType' => function($sm) {
               return new Comment\Service\EntityType($sm);
           },
            'Comment\Service\Comment' => function($sm) {
                return new Comment\Service\Comment($sm);
            },
            /*/er\Entity\User' => function($sm) {
               return new User\Entity\User();
           },

           'User\Service\Auth' => function($sm) {
               return new User\Service\Auth($sm);
           },
           'User\Provider\Identity\DoctrineProvider' => function($sm) {
               $entityManager = $sm->get('Doctrine\ORM\EntityManager');
               $authService = $sm->get('Zend\Authentication\AuthenticationService');
               $doctrineProvider = new User\Provider\Identity\DoctrineProvider($entityManager, $authService);
               $doctrineProvider->setServiceLocator($sm);
               $config = $sm->get('BjyAuthorize\Config');
               $doctrineProvider->setDefaultRole($config['default_role']);

               return $doctrineProvider;
           }*/
        ),
    )

);