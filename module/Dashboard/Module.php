<?php
namespace Dashboard;

use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    public function onBootstrap(MvcEvent $e)
    {
        $em = $e->getApplication()->getEventManager();

        $em->attach(MvcEvent::EVENT_DISPATCH, function($e) {
            $controller = $e->getTarget();
            $controllerFullName = $e->getRouteMatch()->getParam('controller');
            $controllerName = explode('\\', $controllerFullName);
            if ($controller instanceof Controller\IndexController || array_pop($controllerName) == 'Management') {
                $controller->layout('layout/dashboard');
            }
        });
    }
}
