<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:03 AM
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Media\Controller\Image' => 'Media\Controller\ImageController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'media' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/media',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller'    => 'Image',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Media\Controller\Image',
                    'roles' => array('user'),
                )
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Media',
                'controller' => 'media',
                'pages' => array(
                    array(
                        'label' => 'Image',
                        'controller' => 'image',
                        'action' => 'index',
                        'route' => 'media/default',
                        'controller_namespace' => 'Media\Controller\Image',
                        'module' => 'Media'
                    )
                )
            )
        )
    ),
    'doctrine' => array(
        'driver' => array(
            'media_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Media/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Media\Entity' => 'media_driver',
                )
            )
        ),
    ),
);
