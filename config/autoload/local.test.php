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
                    'host'     => 'alexfloppy-ubnt.php.nixsolutions.com',
                    'port'     => '3306',
//                    'user'     => 'root',
//                    'password' => '',
                    'dbname'   => 'zf2_starter_alpha_test',
                    'username' => 'developer',
                    'password' => '123456',
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
