<?php

namespace User\Controller;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use User\Form;
use Zend\Http\Client;
use ZendOAuth\Consumer;
use ZendOAuth\OAuth;
use Zend\Session\Container;
use User\Exception\AuthException;
use User\Service\Auth;

class AuthController extends AbstractActionController
{

    public function loginAction()
    {
        $session = new Container('location');
        $location = $session->location;
        $data = $this->getRequest()->getPost();
        $form = new Form\LoginForm(null, $this->getServiceLocator());
        $flashMessenger = new FlashMessenger();
        if ($this->getRequest()->isPost()) {
            // If you used another name for the authentication service, change it here
            /** @var \User\Service\Auth $userAuth */
            $userAuth = $this->getServiceLocator()->get('\User\Service\Auth');
            try {
                $userAuth->authenticateEquals($data['email'], $data['password']);
                if ($location) {
                    $session->getManager()->getStorage()->clear('location');
                    return $this->redirect()->toUrl($location);
                }

                $flashMessenger->addSuccessMessage('You\'re successfully logged in');
                return $this->redirect()->toRoute('home');
            } catch (AuthException $exception) {
                $flashMessenger->addErrorMessage($exception->getMessage());
            }
        }

        return new ViewModel(array('form' => $form));
    }

    public function logoutAction()
    {
        if ($this->identity()) {
            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
            $authService->clearIdentity();
        }

        return $this->redirect()->toRoute('user/default', ['controller' => 'auth', 'action' => 'login']);
    }

    public function twitterAction()
    {
        $config = $this->getServiceLocator()->get('config')['twitter'];
        $config['callbackUrl'] = $this->url()->fromRoute('user/default', ['controller' => 'auth', 'action' => 'twitter-callback'], ['force_canonical' => true]);
        OAuth::setHttpClient(new Client(null, $config['httpClientOptions']));
        $consumer = new Consumer($config);
        $token = $consumer->getRequestToken();
        // persist the token to storage
        $container = new Container('twitter');
        $container->requestToken = serialize($token);
        // redirect the user
        $consumer->redirect();
    }

    public function twitterCallbackAction()
    {
        $config = $this->getServiceLocator()->get('config')['twitter'];
        $config['callbackUrl'] = $this->url()->fromRoute('user/default', ['controller' => 'auth', 'action' => 'twitter-callback'], ['force_canonical' => true]);
        OAuth::setHttpClient(new Client(null, $config['httpClientOptions']));
        $consumer = new Consumer($config);
        $container = new Container('twitter');
        if ($this->getRequest()->isGet() && $this->params()->fromQuery() && isset($container->requestToken)) {
            $token = $consumer->getAccessToken(
                $this->params()->fromQuery(),
                unserialize($container->requestToken)
            );
            //get user's data
//            $twitter = new Twitter([
//                'accessToken' => $token,
//                'httpClientOptions' => $config['httpClientOptions'],
//                'oauth_options' => $config
//            ]);
//            $response = $twitter->account->verifyCredentials();
//            if (!$response->isSuccess()) {
//                throw new \Exception('Something is wrong with my credentials!');
//            }
//            $twitterUser = $response->toValue();
            /** @var \Doctrine\ORM\EntityManager $objectManager */
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            /** @var \User\Entity\Auth $auth */
            $auth = $objectManager
                ->getRepository('User\Entity\Auth')
                ->getAuthRow(Auth::PROVIDER_TWITTER, $token->user_id);

            if ($auth) {
                $user = $auth->getUser();
                if (!$user->isActive()) {
                    $this->flashMessenger()->addSuccessMessage("User is not active");
                    return $this->redirect()->toRoute('home');
                }
                $auth->setToken($token->oauth_token);
                $auth->setTokenSecret($token->oauth_token_secret);
                $auth->setTokenType(Auth::TYPE_ACCESS);
            } else {
                //if there is no user with provided twitter id and user is not logged in
                if (!$this->identity()) {
                    //create new user
                    /** @var \User\Entity\User $user */
                    $user = $user = new \User\Entity\User();
                    //todo: need to be checked for unique
                    $user->setDisplayName($token->screen_name);
                    $user->setRole($user::ROLE_USER);
                    $user->activate();
                    $objectManager->persist($user);
                    $objectManager->flush();
                } else {
                    //get current authorized user
                    $user = $this->identity()->getUser();
                }
                $auth = new \User\Entity\Auth();
                $auth->setToken($token->oauth_token);
                $auth->setTokenSecret($token->oauth_token_secret);
                $auth->setForeignKey($token->user_id);
                $auth->setProvider(Auth::PROVIDER_TWITTER);
                $auth->setTokenType(Auth::TYPE_ACCESS);
                $auth->setUserId($user->getId());
                $user->getAuths()->add($auth);
                $auth->setUser($user);
            }

            $objectManager->persist($user);
            $objectManager->persist($auth);
            $objectManager->flush();
            $auth->login($this->getServiceLocator());
            // Now that we have an Access Token, we can discard the Request Token
            $container->requestToken = null;

            $this->flashMessenger()->addSuccessMessage("You've successfully registered via twitter");
            return $this->redirect()->toRoute('user/default', ['controller' => 'profile']);
        } else {
            $this->flashMessenger()->addErrorMessage("Invalid callback request. Oops. Sorry.");
            return $this->redirect()->toRoute('home');
        }
    }

    public function facebookAction()
    {
        $config = $this->getServiceLocator()->get('config')['facebook'];
        $config['callbackUrl'] = $this->url()->fromRoute('user/default', ['controller' => 'auth', 'action' => 'facebook-callback'], ['force_canonical' => true]);
        FacebookSession::setDefaultApplication($config['appId'], $config['appSecret']);
        $helper = new FacebookRedirectLoginHelper($config['callbackUrl']);
        $this->redirect()->toUrl($helper->getLoginUrl());

    }

    public function facebookCallbackAction()
    {
        $config = $this->getServiceLocator()->get('config')['facebook'];
        $config['callbackUrl'] = $this->url()->fromRoute('user/default', ['controller' => 'auth', 'action' => 'facebook-callback'], ['force_canonical' => true]);
        FacebookSession::setDefaultApplication($config['appId'], $config['appSecret']);
        $helper = new FacebookRedirectLoginHelper($config['callbackUrl']);

        try {
            $session = $helper->getSessionFromRedirect();
        } catch(\Exception $ex) {
            $this->flashMessenger()->addErrorMessage("Invalid callback request. Oops. Sorry.");
            return $this->redirect()->toRoute('home');
        }
        if ($session) {
            // Logged in
            $request = new FacebookRequest($session, 'GET', '/me');
            $response = $request->execute();
            $graphObject = $response->getGraphObject();

            /** @var \Doctrine\ORM\EntityManager $objectManager */
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            /** @var \User\Entity\Auth $auth */
            $auth = $objectManager
                ->getRepository('User\Entity\Auth')
                ->getAuthRow(Auth::PROVIDER_FACEBOOK, $graphObject->getProperty('id'));

            if ($auth) {
                $user = $auth->getUser();
                if (!$user->isActive()) {
                    $this->flashMessenger()->addSuccessMessage("'User is not active'");
                    return $this->redirect()->toRoute('home');
                }
                $auth->setToken($session->getAccessToken());
                $auth->setTokenSecret(0);
                $auth->setTokenType(Auth::TYPE_ACCESS);
            } else {
                if (!$this->identity()) {
                    //create new user
                    $user = new \User\Entity\User();
                    $user->setDisplayName($graphObject->getProperty('id'));
                    $user->setRole($user::ROLE_USER);
                    $user->activate();
                    $objectManager->persist($user);
                    $objectManager->flush();
                } else {
                    //get current authorized user
                    $user = $this->identity()->getUser();
                }

                $auth = new \User\Entity\Auth();
                $auth->setToken($session->getAccessToken());
                $auth->setTokenSecret(0);
                $auth->setForeignKey($graphObject->getProperty('id'));
                $auth->setProvider(Auth::PROVIDER_FACEBOOK);
                $auth->setTokenType(Auth::TYPE_ACCESS);
                $auth->setUserId($user->getId());
                $user->getAuths()->add($auth);
                $auth->setUser($user);
            }
            $objectManager->persist($user);
            $objectManager->persist($auth);
            $objectManager->flush();
            $auth->login($this->getServiceLocator());

            $this->flashMessenger()->addSuccessMessage("You've successfully registered via facebook");
            return $this->redirect()->toRoute('user/default', ['controller' => 'profile']);
        }

    }
}
