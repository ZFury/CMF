#########################################

echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install

echo "Changing permissions for data and uploads directories"
find data/ -type d -exec chmod 0777 {} \;
find public/uploads/ -type d -exec chmod 0777 {} \;

echo "Doctrine setup"
./vendor/bin/doctrine-module orm:schema-tool:update --force

echo "Update migrations"
yes | ./vendor/doctrine/doctrine-module/bin/doctrine-module migrations:migrate
