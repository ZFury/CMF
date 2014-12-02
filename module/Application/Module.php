<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $config = $e->getApplication()->getServiceManager()->get('Config');
        $phpSettings = $config['phpSettings'];
        foreach ($phpSettings as $settingName => $settingValue) {
            ini_set($settingName, $settingValue);
        }

        set_error_handler(['Application\Module', 'errorHandler']);
    }

    /**
     * @param $type
     * @param $message
     * @param $file
     * @param $line
     * @throws \Exception
     */
    public static function errorHandler($type, $message, $file, $line)
    {
        if (!($type & error_reporting())) {
            return;
        }

        throw new \Exception('Error ' . $message . ' in file ' . $file . ' at line ' . $line);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Utility\UnauthorizedStrategy' => function ($sm) {
                    $unauthorizedStrategy = new Utility\UnauthorizedStrategy('layout/layout');
                    return $unauthorizedStrategy;
                }
            ),
        );
    }
}
