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
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventventManager = $event->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventventManager);

        $config = $event->getApplication()->getServiceManager()->get('Config');
        $phpSettings = $config['phpSettings'];
        foreach ($phpSettings as $settingName => $settingValue) {
            ini_set($settingName, $settingValue);
        }

        /** @var \Zend\Mvc\MvcEvent $events */
        $events = $event->getTarget()->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'getJsonModelError'), -5100);
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'getJsonModelError'), -5100);

        set_error_handler(['Application\Module', 'errorHandler']);
    }

    /**
     * @param MvcEvent $event
     * @return MvcEvent
     */
    public function getJsonModelError(MvcEvent $event)
    {
        $error = $event->getError();
//        $listeners = $event->getApplication()->getEventManager()->getListeners('dispatch');
        if (!$this->isJson($event) || (!$error && !$event->getResponse()->isNotFound())) {
            return;
        }
        $response = $event->getResponse();
        $exception = $event->getParam('exception');
        $exceptionJson = array();
        if ($exception) {
            $exceptionJson[] = $exception->getMessage();
        } elseif ($response->isNotFound()) {
            $exceptionJson[] = 'Not Found';
        }
        $event->getResponse()->getHeaders()->addHeaderLine('Fury-Notify', \Zend\Json\Json::encode(['error' => $exceptionJson]));
        $model = new JsonModel(array('data' => ['error' => $exceptionJson], 'options' => ['controller' => $event->getParam('controller')]));
        $model->setTerminal(true);
        $event->setResult($model);
        $event->setViewModel($model);

        return $event;
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
     * @param $event
     * @return bool
     */
    protected function isJson(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return false;
        }

        $headers = $request->getHeaders();
        if (!$headers->has('Accept')) {
            return false;
        }

        $accept = $headers->get('Accept');

        $match = $accept->match('application/json');

        if (!$match || $match->getTypeString() == '*/*') {
            // not application/json
            return false;
        }

        return true;
    }

    /**
     * @param $event
     */
    public function jsonHandler(MvcEvent $event)
    {
        /** @var \Zend\Mvc\Controller\Plugin\FlashMessenger $flashmessenger */
        $flashMessenger = $event->getApplication()
            ->getServiceManager()
            ->get('viewHelperManager')
            ->get('flashMessenger');

        $view = $event->getViewModel();
        if ($view instanceof JsonModel) {
            return;
        }

        $children = $event->getViewModel()->getChildren();
        if ($children) {
            foreach ($children as $child) {
                $params = $child->getVariables();
            }
        } else {
            $params = $event->getViewModel()->getVariables();
        }

        $result = array(
            'data' => array(),
            'errors' => array(),
            'options' => array()
        );
        foreach ($params as $param) {
            if (method_exists($param, 'toArray')) {
                $result['data'][] = $param->toArray();
            } elseif ($param instanceof \Zend\Form\Form) {
                foreach ($param->getElements() as $formElement) {
                    $messages = array();
                    if ($formElement->getMessages()) {
                        foreach ($formElement->getMessages() as $type => $message) {
                            $messages[] = $message;
                        }
                        $key = $formElement->getName();
                        $errors[$key] = $messages;
                        //$formElement->getName() => array($formElement->getMessages())
                    }
                }
                $result['errors'] = $errors;
                $result['data'][] = $param->getData();
                $result['options'] = array(
                    'method' => $param->getAttribute('method')
                );
            } else {
                $result['data'][] = $param;
            }
        }
        $result['success'] = $flashMessenger->getCurrentSuccessMessages();
        $flashMessenger->clearCurrentMessagesFromContainer();

        $model = new JsonModel($result);
        $event->setViewModel($model);
    }
}
