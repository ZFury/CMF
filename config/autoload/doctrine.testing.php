<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => '{HOSTNAME}',
                    'dbname'   => '{DATABASE}',
                    'user'     => '{USERNAME}',
                    'password' => '{PASSWORD}',
                    'port'     => '{PORT}',
//                    'host'     => 'alpha-team.php.nixsolutions.com',
//                    'port'     => '3306',
//                    'dbname'   => 'p_zfs_tests',
//                    'user' => 'p_zfs_test',
//                    'password' => 'p_zfs_test',
                ),
                'doctrine_type_mappings' => array(
                    'enum' => 'string'
                ),
            )
        )
    ),
    'phpSettings'   => array(
        'error_reporting' => E_ALL,
        'display_errors' => true,
        'display_startup_errors' => true,
    ),
);
