<?php

namespace Tools\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Symfony\Component\Console\Helper;
use \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

class HelperSetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('doctrine.connection.orm_default');

        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        $helperSet = new Helper\HelperSet(array(
            'dialog' => new Helper\DialogHelper(),
            'db' => new ConnectionHelper($connection),
            'em' => new EntityManagerHelper($entityManager)
        ));

        return $helperSet;
    }
}
