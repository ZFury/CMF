<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/19/14
 * Time: 4:27 PM
 */

namespace Install;

use Install\Service\Install;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    /**
     * @return mixed
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
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), +100);
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), -1000);
    }

    /**
     * @param MvcEvent $e
     * @return Response
     */
    public function preDispatch(MvcEvent $e)
    {
        if (!$e->getRouteMatch()->getParam('module') || $e->getRouteMatch()->getParam('module') !== 'install') {
            $session = new Container('progress_tracker');
            $currentStep = Install::getSteps()[array_search($session->offsetGet('current_step'), Install::getSteps())];
            switch ($currentStep) {
                case 'global_requirements':
                    $action = 'global-requirements';
                    break;
                case 'db':
                    $action = 'database';
                    break;
                case 'modules_requirements':
                    $action = 'modules-requirements';
                    break;
                default:
                    $action = $currentStep;
                    break;
            }
            $response = new Response();
            $response->setStatusCode(302);
            $response->getHeaders()
                ->addHeaderLine('Location', "/install/index/$action");

            return $response;
        }
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $e->getTarget()->layout('layout/install/install');
    }
}
