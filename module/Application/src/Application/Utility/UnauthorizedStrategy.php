<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11/28/14
 * Time: 1:36 PM
 */

namespace Application\Utility;

use Zend\Session\Container;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;
use BjyAuthorize\Guard\Controller;
use BjyAuthorize\Guard\Route;
use Zend\Mvc\Application;
use BjyAuthorize\Exception\UnAuthorizedException;

class UnauthorizedStrategy extends \BjyAuthorize\View\UnauthorizedStrategy
{
    public function onDispatchError(MvcEvent $e)
    {
        $result = $e->getResult();
        $response = $e->getResponse();

        if ($result instanceof Response || ($response && ! $response instanceof HttpResponse)) {
            return;
        }

        $viewVariables = array(
            'error'      => $e->getParam('error'),
            'identity'   => $e->getParam('identity'),
        );

        switch ($e->getError()) {
            case Controller::ERROR:
                $viewVariables['controller'] = $e->getParam('controller');
                $viewVariables['action']     = $e->getParam('action');

                $router = $e->getRouter();
                if ($e->getParam('exception') instanceof UnAuthorizedException &&
                    !$e->getApplication()->getServiceManager()
                        ->get('Zend\Authentication\AuthenticationService')->hasIdentity()) {
                    $session = new Container('location');
                    $session->location = $e->getRequest()->getUri();
                    // get url to the login route
                    $options['name'] = 'login';
                    $url = $router->assemble(array(), $options);
                    if (!$response) {
                        $response = new HttpResponse();
                        $e->setResponse($response);
                    }
                    $response->getHeaders()->addHeaderLine('Location', $url);
                    $response->setStatusCode(302);

                    return;
                }

                break;
            case Route::ERROR:
                $viewVariables['route'] = $e->getParam('route');
                break;
            case Application::ERROR_EXCEPTION:
                if (!($e->getParam('exception') instanceof UnAuthorizedException)) {
                    return;
                }
                $viewVariables['reason'] = $e->getParam('exception')->getMessage();
                $viewVariables['error']  = 'error-unauthorized';

                break;
            default:
                /*
                 * do nothing if there is no error in the event or the error
                 * does not match one of our predefined errors (we don't want
                 * our 403 template to handle other types of errors)
                 */

                return;
        }

        $model    = new ViewModel($viewVariables);
        $response = $response ?: new HttpResponse();

        $model->setTemplate($this->getTemplate());
        $e->getViewModel()->addChild($model);
        $response->setStatusCode(403);
        $e->setResponse($response);
    }
}