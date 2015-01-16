<?php

namespace Tools\Factory\Migrations;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineTools\Service\MigrationsCommandFactory;

class CommandMigrateFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MigrationsCommandFactory('migrate');
    }
}
