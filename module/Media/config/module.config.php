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
            'Media\Service\Image' => function ($serviceManager) {
                return new Media\Service\Image($serviceManager);
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
    'installation' => [
        'files-to-check' => [
            ['uploads' => 'public/uploads'],
        ],
        'tools-to-check' => [
            [
                'libav (do not forget sudo apt-get install libavcodec-extra-53)' => 'avconv -version',
                'version' => '0.10.12',
                'install' => 'sudo apt-get install libav-tools, sudo apt-get install libavcodec-extra-53']
        ]
    ]
);
