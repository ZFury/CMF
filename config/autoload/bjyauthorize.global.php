<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',
        'identity_provider' => 'User\Provider\Identity\DoctrineProvider',
        'role_providers'        => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => [],
                'user'  => ['children' => array(
                    'admin' => [],
                )],
            ),
        )
    )
);