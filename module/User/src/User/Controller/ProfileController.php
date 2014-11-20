<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 25.09.14
 * Time: 11:25
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel([]);
    }

    public function checkIfFollowing()
    {
        $httpClientOptions = array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false
            ),
        );
        $config = array(
            'access_token' => array(
                'token'  => '228442924-P7AaZphsNeEkSOrVOL7UlqHNgeLQ6SqxnIQLNOVy',
                'secret' => '8jXYDcJ8O6p3Z5X51WHfbSZqm8y7YPU54xFzdPPjfv8kx',
            ),
            'oauth_options' => array(
                'consumerKey' => 'oh906btm1R0oTSPN5cBFHsRus',
                'consumerSecret' => 'dX9JBVIr5suWs09v9kWX0nbKDPfKS4nGEh8wWHXnT9LxDtCsY0',
            ),
            'httpClientOptions' => $httpClientOptions
        );
        $twitter = new Twitter($config);
        $response = $twitter->account->verifyCredentials();
        if (!$response->isSuccess()) {
            die('Something is wrong with my credentials!');
        }
        $twitter->usersSearch('twitter')->toValue()[0]->following;
    }
} 