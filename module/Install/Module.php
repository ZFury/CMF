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
        $application = $e->getApplication();
        $em = $application->getEventManager();
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), +100);
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), -1000);
    }

    public function preDispatch(MvcEvent $e)
    {
        if (!$e->getRouteMatch()->getParam('module') || $e->getRouteMatch()->getParam('module') !== 'install') {
//            $controller = $e->getTarget();
//            $controller->getPluginManager()->get('redirect')->toRoute('home');
//            $e->getRouteMatch()
//                ->setParam('controller', 'Install\Controller\Index')
//                ->setParam('action', 'global-requirements');

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

    public function onDispatch(MvcEvent $e)
    {
        $e->getTarget()->layout('layout/install/install');
    }
}
