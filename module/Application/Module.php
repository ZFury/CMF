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

        // attach the JSON view strategy
        $app = $event->getTarget();
        $sm = $app->getServiceManager();
        $view = $sm->get('ZendViewView');
        $strategy = $sm->get('ViewJsonStrategy');
        $view->getEventManager()->attach($strategy, 100);

        /** @var \Zend\Mvc\MvcEvent $events */
        $events = $event->getTarget()->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), '-1000');
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), '99999');
        //$events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), '99999');

        set_error_handler(['Application\Module', 'errorHandler']);
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatchError(MvcEvent $event)
    {
        if (!$this->isJson($event)) {
            return;
        }

        $message = '';
        $code = '';

        if ($eventException = $event->getParam('exception')) {
            $message = $eventException->getMessage();
            $code = $eventException->getCode();
        } elseif ($error = $event->getParam('error')) {
            $message = $error;
        }

        $result = array(
            'errors' => array(
                'exception' => array(
                    'message' => $message,
                    'code' => $code,
                ),
            'data' => array(),
            'options' => array()
            )
        );

        $model = new JsonModel($result);
        $event->setViewModel($model);
        $event->stopPropagation(true);
    }

    /**
     * @param $event
     */
    public function onDispatch(MvcEvent $event)
    {
        if (!$this->isJson($event)) {
            return;
        }
        /** @var \Zend\Http\Request $response */
        $response = $event->getResponse();
        $response->getHeaders()->addHeaders(
            array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            )
        );

        $this->jsonHandler($event);
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
                'Application\Utility\UnauthorizedStrategy' => 'Application\Factory\UnauthorizedStrategyFactory',
//                'Application\Utility\UnauthorizedStrategy' => function ($sm) {
//                    $unauthorizedStrategy = new Utility\UnauthorizedStrategy('error/403');
//                    return $unauthorizedStrategy;
//                }
            ),
        );
    }

    /**
     * @param $event
     * @return bool
     */
    public function isJson(MvcEvent $event)
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
        $flashmessenger = $event->getApplication()
            ->getServiceManager()
            ->get('viewhelpermanager')
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
        $result['success'] = $flashmessenger->getCurrentSuccessMessages();
        $flashmessenger->clearCurrentMessagesFromContainer();

        $model = new JsonModel($result);
        $event->setViewModel($model);
    }
}
