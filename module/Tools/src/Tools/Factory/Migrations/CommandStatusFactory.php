<?php

namespace Tools\Factory\Migrations;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineTools\Service\MigrationsCommandFactory;

class CommandStatusFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MigrationsCommandFactory('status');
    }
}
