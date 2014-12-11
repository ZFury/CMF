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
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;

class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $config = $e->getApplication()->getServiceManager()->get('Config');
        $phpSettings = $config['phpSettings'];
        foreach ($phpSettings as $settingName => $settingValue) {
            ini_set($settingName, $settingValue);
        }

        // attach the JSON view strategy
        $app      = $e->getTarget();
        $locator  = $app->getServiceManager();
        $view     = $locator->get('ZendViewView');
        $strategy = $locator->get('ViewJsonStrategy');
        $view->getEventManager()->attach($strategy, 100);

        /** @var \Zend\Mvc\MvcEvent; $events */
        $events = $e->getTarget()->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), '-1000');
        if ($this->isJson($e)) {
            $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), '99999');
        }

        set_error_handler(['Application\Module', 'errorHandler']);
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatchError(MvcEvent $event)
    {
        if ($this->isJson($event)) {
            $model = new JsonModel();
            $event->setViewModel($model);
        }
        // $eventException = $event->getParam('exception');
    }

    /**
     * @param $e
     */
    public function onDispatch($e)
    {
//        if (!$this->isJson($e)) {
//            return;
//        }

        $response = $e->getResponse();
        $response->getHeaders()->addHeaders(
            array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            )
        );

        $childrens =$e->getViewModel()->getChildren();
        foreach ($childrens as $children) {
            $params = $children->getVariables();
        }

        $result = array();
        foreach ($params as $param) {
            if (method_exists($param, 'toArray')) {
                $result[] = $param->toArray();
            } elseif ($param instanceof \Zend\Form\Form) {
                foreach ($param->getElements() as $formElement) {
                    if ($formElement->getMessages()) {
                        var_dump($formElement->getMessages());
                    }
                }
//                var_dump($param);
                die('form');
            }
            $result[] = $param;
        }

        $model = new JsonModel(array($result));
        $e->setViewModel($model);
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

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Utility\UnauthorizedStrategy' => function ($sm) {
                    $unauthorizedStrategy = new Utility\UnauthorizedStrategy('error/403');
                    return $unauthorizedStrategy;
                }
            ),
        );
    }

    /**
     * @param $e
     * @return bool
     */
    public function isJson($e)
    {
        $request = $e->getRequest();
        if (!$request instanceof HttpRequest) {
            return false;
        }

        $headers = $request->getHeaders();
        if (!$headers->has('Accept')) {
            return false;
        }

        $accept = $headers->get('Accept');
        $match  = $accept->match('application/json');
        if (!$match || $match->getTypeString() == '*/*') {
            // not application/json
            return false;
        }

        return true;
    }
}
