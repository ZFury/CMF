<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'phpSettings' => array(
        'date.timezone'  => 'Europe/Kiev',
        'error_reporting' => E_ALL,
        'display_errors' => true,
        'display_startup_errors' => true
    ),
    // Service config for memcached
//    'service_manager' => array(
//        'abstract_factories' => array(
//            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
//        )
//    )
);
