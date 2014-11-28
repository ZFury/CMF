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
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'dbname'   => 'zf2_starter_alpha_test',
                    'user' => 'root',
                    'password' => '',
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
