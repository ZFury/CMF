<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11/28/14
 * Time: 1:36 PM
 */

namespace Application\Utility;

use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;

class UnauthorizedStrategy extends \BjyAuthorize\View\UnauthorizedStrategy
{
    public function onDispatchError(MvcEvent $e)
    {
        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        $router = $e->getRouter();
//        $match  = $e->getRouteMatch();

        if (!$e->getApplication()->getServiceManager()->get('Zend\Authentication\AuthenticationService')->hasIdentity()) {
            // get url to the login route
            $options['name'] = 'login';
            $url = $router->assemble(array(), $options);

            // Work out where were we trying to get to
//            $options['name'] = $match->getMatchedRouteName();
//            $redirect = $router->assemble($match->getParams(), $options);

            // set up response to redirect to login page
            $response = $e->getResponse();
            if (!$response) {
                $response = new HttpResponse();
                $e->setResponse($response);
            }
//            $response->getHeaders()->addHeaderLine('Location', $url . '?redirect=' . $redirect);
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
        }
    }
}