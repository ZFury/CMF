#########################################

echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install

echo "Doctrine setup"
./vendor/bin/doctrine-module orm:schema-tool:update --force

echo "Creating .htaccess"
cp ./public/.htaccess.sample ./public/.htaccess

echo "Creating directories"
mkdir -p data/
mkdir -p data/DoctrineORMModule/Proxy
mkdir -p data/DoctrineORMModule/Migrations

chmod -R 0777 data/*

echo "Enabling git-flow"
git flow init -d