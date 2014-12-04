<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'doctrine_type_mappings' => array(
                    'enum' => 'string'
                ),
            )
        )
    )
);
