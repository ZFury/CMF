#########################################

echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install

echo "Doctrine setup"
./vendor/bin/doctrine-module orm:schema-tool:update --force

echo "Update migrations"
vendor/doctrine/doctrine-module/bin/doctrine-module migrations:migrate --dry-run

mkdir -p public/uploads
chmod -R 0777 public/uploads