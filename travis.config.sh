# Creating directories
mkdir logs
mkdir logs/phpcb
mkdir coverage/options
mkdir coverage/pages
mkdir coverage/categories
mkdir coverage/comment
mkdir coverage/media
mkdir coverage/user
mkdir coverage/install

# Copying dist files
copy config/autoload/doctrine.local.php.dist config/autoload/doctrine.local.php
copy config/autoload/doctrine.testing.php.dist config/autoload/doctrine.testing.php
copy config/autoload/testing.php.dist config/autoload/testing.php
# copy public/.htaccess.sample public/.htaccess

# Configuring
sed -i "s/{HOSTNAME}/127.0.0.1/g" config/autoload/doctrine.local.php
sed -i "s/{DATABASE}/p_zfury/g" config/autoload/doctrine.local.php
sed -i "s/{USERNAME}/travis/g" config/autoload/doctrine.local.php
sed -i "s/{PASSWORD}//g" config/autoload/doctrine.local.php
sed -i "s/{PORT}/3306/g" config/autoload/doctrine.local.php

sed -i "s/{HOSTNAME}/127.0.0.1/g" config/autoload/doctrine.testing.php
sed -i "s/{DATABASE}/p_zfury_test/g" config/autoload/doctrine.testing.php
sed -i "s/{USERNAME}/travis/g" config/autoload/doctrine.testing.php
sed -i "s/{PASSWORD}//g" config/autoload/doctrine.testing.php
sed -i "s/{PORT}/3306/g" config/autoload/doctrine.testing.php

sed -i "s/\/\/'BjyAuthorize'/'BjyAuthorize'/g" config/application.config.php



