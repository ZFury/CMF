#########################################

echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install

echo "Doctrine setup"
./vendor/bin/doctrine-module orm:schema-tool:update --force

mkdir -p public/uploads
chown -R www-data public/uploads
chmod -R 0775 public/uploads