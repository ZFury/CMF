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
                    'host'     => 'alpha-team.php.nixsolutions.com',
                    'port'     => '3306',
                    'dbname'   => 'p_zfs_test',
                    'username' => 'p_zfs_tests',
                    'password' => 'p_zfs_test',
//                    'host'     => 'alexfloppy-ubnt.php.nixsolutions.com',
//                    'port'     => '3306',
//                    'dbname'   => 'zf2_starter_alpha_test',
//                    'username' => 'developer',
//                    'password' => '123456',
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
