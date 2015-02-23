<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 2/20/15
 * Time: 6:00 PM
 */
//inspired from https://github.com/doctrine/DoctrineModule/blob/master/tests/DoctrineModuleTest/ServiceManagerTestCase.php
// thanks to Marco Pivetta

namespace ModulesTests;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

class ServiceManagerGrabber
{
    protected static $serviceConfig = null;

    public static function setServiceConfig($config)
    {
        static::$serviceConfig = $config;
    }

    public function getServiceManager()
    {
        $configuration = static::$serviceConfig ? : require_once './config/application.config.php';

        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $serviceManager = new ServiceManager(new ServiceManagerConfig($smConfig));
        $serviceManager->setService('ApplicationConfig', $configuration);

        $serviceManager->get('ModuleManager')->loadModules();

        return $serviceManager;
    }
}
