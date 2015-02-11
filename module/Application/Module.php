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
use Zend\Json\Json;
use Zend\Form\Form;

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
        //Bjy UnauthorizedStrategy has -5000 priority
        //we should attach our methods to be executed after all
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
        $view = $event->getViewModel();
        if ($view instanceof JsonModel) {
            return;
        }
        if (!$this->isJson($event)) {
            return;
        }
        // || (!$event->getError() && !$event->getResponse()->isNotFound()
        if ($event->getViewModel()->getChildren()) {
            foreach ($event->getViewModel()->getChildren() as $child) {
                $params = $child->getVariables();
            }
        } else {
            $params = $event->getViewModel()->getVariables();
        }

        $result = array(
            'data' => array(),
            'errors' => array(),
            'options' => array(),
            'success' => array()
        );
        foreach ($params as $key => $param) {
            if (method_exists($param, 'toArray')) {
                $result['data'][] = $param->toArray();
            } elseif ($param instanceof Form) {
                foreach ($param->getElements() as $formElement) {
                    $messages = array();
                    if ($formElement->getMessages()) {
                        foreach ($formElement->getMessages() as $type => $message) {
                            $messages[] = $message;
                        }
                        $result['errors'][$formElement->getName()] = $messages;
                    }
                }
                $result['data'][$key] = $param->getData();
                $result['options'] = ['method' => $param->getAttribute('method')];
            } else {
                $result['data'][$key] = $param;
            }
        }
        /** @var \Zend\Mvc\Controller\Plugin\FlashMessenger $flashMessenger */
        $flashMessenger = $event->getApplication()
            ->getServiceManager()
            ->get('viewHelperManager')
            ->get('flashMessenger');
        $result['success'] = $flashMessenger->getCurrentSuccessMessages();
        $flashMessenger->clearCurrentMessagesFromContainer();

        $notifyErrors = array();
        if ($event->getParam('exception')) {
            $notifyErrors[] = $event->getParam('exception')->getMessage();
        } elseif ($event->getResponse()->isNotFound()) {
            $notifyErrors[] = isset($result['data']['message']) ? $result['data']['message'] : 'Not Found';
        }
        $event->getResponse()->getHeaders()->addHeaderLine('Fury-Notify', Json::encode(['error' => $notifyErrors]));
        $model = new JsonModel($result);
        $model->setTerminal(true);
        $event->setResult($model)->setViewModel($model);

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
}
