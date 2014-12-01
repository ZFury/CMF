<?php

return array(
    'bjyauthorize' => array(
        'unauthorized_strategy' => 'Application\Utility\UnauthorizedStrategy',
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