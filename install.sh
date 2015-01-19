#########################################

echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install

echo "Creating .htaccess"
cp ./public/.htaccess.sample ./public/.htaccess

echo "Creating directories"
mkdir -p data/
mkdir -p data/DoctrineORMModule/Proxy
mkdir -p data/DoctrineORMModule/Migrations
mkdir -p public/uploads
chmod -R 0777 data/*
chmod -R 0777 public/uploads
cp config/application.config.php.dist config/application.config.php
echo "Enabling git-flow"
git flow init -d

echo "Install git hook for PHP_CodeSniffer"
cp data/git_hooks/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit