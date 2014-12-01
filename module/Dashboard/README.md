Adding module to the dashboard
==============================
If you created the new module, you should add
so-called "navigation config" to its module.config.php
Here is an example (this example is used in User module):
*
```
'navigation' => array(
        'default' => array(
            array(
                'label' => 'User',
                'controller' => 'user',
                'pages' => array(
                    array(
                        'label' => 'All users',
                        'controller' => 'management',
                        'action' => 'index',
                        'route' => 'user/default',
                        'controller_namespace' => 'User\Controller\Management',
                        'module' => 'User'
                    ),
                    array(
                        'label' => 'Create user',
                        'controller' => 'management',
                        'action' => 'create',
                        'route' => 'user/default',
                        'controller_namespace' => 'User\Controller\Management',
                        'module' => 'User'
                    )
                )
            )
        )
    )
```
*
Take attention on several things:
  * don't change 'default' to anything else. That is not necessary, all right?
  * please, don't forget to add "action" parameter to a bjyauthorize config. Here is an example:
```
*
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'User\Controller\Auth',
                    'action' => array('login', 'logout'),
                    'roles' => array('guest', 'user'),
                )
            )
        )
    )
*
```
  * please, name all admin controllers "ManagementController"
