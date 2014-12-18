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
            'Media\Service\File' => function ($serviceManager) {
                return new Media\Service\File($serviceManager);
            },
            'Media\Service\Video' => function ($serviceManager) {
                return new Media\Service\Video($serviceManager);
            },
            'Media\Service\Audio' => function ($serviceManager) {
                return new Media\Service\Audio($serviceManager);
            },
            'Media\Service\Blueimp' => function ($serviceManager) {
                return new Media\Service\Blueimp($serviceManager);
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
