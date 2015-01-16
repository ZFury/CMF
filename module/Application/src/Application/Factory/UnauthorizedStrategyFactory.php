<?php

namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Utility;

class UnauthorizedStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $unauthorizedStrategy = new Utility\UnauthorizedStrategy('error/403');
        return $unauthorizedStrategy;
    }
}
