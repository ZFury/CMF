<?php

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Add user',
                'controller' => 'management',
                'action' => 'create',
                'route' => 'user/default'

            ),
            array(
                'label' => 'Edit user',
                'controller' => 'management',
                'action' => 'edit',
                'route' => 'user/default'

            ),
            array(
                'label' => 'Delete user',
                'controller' => 'management',
                'action' => 'delete',
                'route' => 'user/default'

            )
        )
    )
);