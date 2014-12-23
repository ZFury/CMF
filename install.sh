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
mkdir -p public/uploads
chmod -R 0777 data/*
chown -R www-data public/uploads
chmod -R 0775 public/uploads

echo "Enabling git-flow"
git flow init -d

echo "Install git hook for PHP_CodeSniffer"
cp data/git_hooks/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit