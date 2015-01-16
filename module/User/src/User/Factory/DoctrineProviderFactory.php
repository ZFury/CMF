<?php

namespace User\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Provider\Identity;

class DoctrineProviderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $authService = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $doctrineProvider = new Identity\DoctrineProvider($entityManager, $authService);
        $doctrineProvider->setServiceLocator($serviceLocator);
        $config = $serviceLocator->get('BjyAuthorize\Config');
        $doctrineProvider->setDefaultRole($config['default_role']);

        return $doctrineProvider;
    }
}
