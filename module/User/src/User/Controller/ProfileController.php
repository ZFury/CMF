<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 25.09.14
 * Time: 11:25
 */

namespace User\Controller;

use User\Exception\AuthException;
use User\Form\ChangePasswordForm;
use User\Form\Filter\ChangePasswordInputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

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
                'token' => '228442924-P7AaZphsNeEkSOrVOL7UlqHNgeLQ6SqxnIQLNOVy',
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

    public function changePasswordAction()
    {
        $form = new ChangePasswordForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new ChangePasswordInputFilter($this->getServiceLocator()));
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $flashMessenger = new FlashMessenger();
                $userAuth = $this->getServiceLocator()->get('\User\Service\Auth');
                try {
                    $userAuth->checkCredentials(
                        $this->identity()->getUser()->getEmail(),
                        $form->getData()['currentPassword']
                    );
                    $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                    $user = $objectManager
                        ->getRepository('User\Entity\User')
                        ->find($this->identity()->getUser()->getId());

                    $userAuth->generateEquals($user, $form->getData()['password']);
                    $flashMessenger->addSuccessMessage("You have successfully changed your password!");

                    return $this->redirect()->toRoute('home');
                } catch (AuthException $exception) {
                    $flashMessenger->addErrorMessage($exception->getMessage());
                }
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
