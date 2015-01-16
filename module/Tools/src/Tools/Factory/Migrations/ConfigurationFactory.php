<?php

namespace Tools\Factory\Migrations;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Doctrine\DBAL\Migrations\Configuration\Configuration;

class ConfigurationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('doctrine.connection.orm_default');

        $appConfig = $serviceLocator->get('Config');
        $migrationsConfig = $appConfig['doctrinetools']['migrations'];

        $configuration = new Configuration($connection);
        $configuration->setMigrationsDirectory($migrationsConfig['directory']);
        $configuration->setMigrationsNamespace($migrationsConfig['namespace']);
        $configuration->setMigrationsTableName($migrationsConfig['table']);
        $configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

        return $configuration;
    }
}
