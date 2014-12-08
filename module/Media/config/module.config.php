<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:03 AM
 */

return array(
    'service_manager' => [
        'factories' => [
            'Media\Service\Image' => function ($serviceManager) {
                return new Media\Service\Image($serviceManager);
            },
        ]
    ],
    'doctrine' => [
        'driver' => [
            'media_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Media/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    'Media\Entity' => 'media_driver'
                ],
            ],
        ],
    ],
);
