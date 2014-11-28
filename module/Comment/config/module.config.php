<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'comment_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
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
                    'route'    => '/comment[/:action[/:id]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Comment\Controller\Index',
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
            }
        ),
    )

);