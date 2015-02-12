<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 01.10.14
 * Time: 13:48
 */
namespace User\Service;

use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceManager;
use User\Exception\AuthException;

class Auth
{
    const TYPE_ACCESS = 'access';

    const PROVIDER_EQUALS = 'equals';
    const PROVIDER_TOKEN = 'token';
    const PROVIDER_LDAP = 'ldap';
    const PROVIDER_TWITTER = 'twitter';
    const PROVIDER_FACEBOOK = 'facebook';

    /**
     * @var null|\Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @return null|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getObjectManager()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @param \User\Entity\Auth $auth
     * @param $password
     * @return string
     */
    public static function encrypt(\User\Entity\Auth $auth, $password)
    {
        $bcrypt = new Bcrypt;
        $bcrypt->setSalt($auth->getTokenSecret());

        return $bcrypt->create($password);
    }

    /**
     * @param \User\Entity\User $user
     * @param $password
     * @return \User\Entity\Auth
     */
    public function generateEquals(\User\Entity\User $user, $password)
    {
        //delete row
        $auth = $this->getObjectManager()
            ->getRepository('User\Entity\Auth')
            ->findOneBy(['userId' => $user->getId(), 'provider' => Auth::PROVIDER_EQUALS]);
//            ->findOneByUserId($user->getId());

        if ($auth) {
            $this->getObjectManager()->remove($auth);
            $this->getObjectManager()->flush();
        }
        // new auth row
        $row = new \User\Entity\Auth();
        $row->setUserId($user->getId());
        $row->setForeignKey($user->getEmail());
        $row->setProvider(self::PROVIDER_EQUALS);
        $row->setTokenType(self::TYPE_ACCESS);

        // generate secret
        $alpha = range('a', 'z');
        shuffle($alpha);
        $secret = array_slice($alpha, 0, rand(5, 15));
        $secret = md5($user->getId() . join('', $secret));
        $row->setTokenSecret($secret);

        // encrypt password and save as token
        $row->setToken(self::encrypt($row, $password));
        $user->getAuths()->add($row);
        $row->setUser($user);
        $this->getObjectManager()->persist($row);
        $this->getObjectManager()->flush();

        return $row;
    }

    /**
     * @param $email
     * @param $password
     * @return \User\Entity\Auth
     * @throws AuthException
     */
    public function authenticateEquals($email, $password)
    {
        $authService = $this->createAuthService($email, $password);
        $authResult = $authService->authenticate();

        if (!$authResult->isValid()) {
            throw new AuthException('Wrong login or password');
        }

        /**
         * @var \User\Entity\Auth $authRow
         */
        $authRow = $authResult->getIdentity();
        $user = $authRow->getUser();
        try {
            if (!$user->isActive()) {
                if ($user->isUnconfirmed()) {
                    throw new AuthException("Please confirm your email first");
                }
                throw new AuthException("Your account is blocked");
            }
        } catch (AuthException $exception) {
            $authService->clearIdentity();
            throw $exception;
        }

        return $authRow;
    }

    /**
     * Checks if user's password is true or false
     *
     * @param  $email
     * @param  $password
     * @return bool
     * @throws AuthException
     */
    public function checkCredentials($email, $password)
    {
        $authService = $this->createAuthService($email, $password);
        $authResult = $authService->getAdapter()->authenticate();
        if (false == $authResult->getCode()) {
            throw new AuthException('Wrong login or password');
        }
        return true;
    }

    /**
     * Creates authentication service and sets IdentityValue, Credential Value to its adapter
     *
     * @param  $email
     * @param  $password
     * @return array|object
     */
    public function createAuthService($email, $password)
    {
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($email);
        $adapter->setCredentialValue($password);

        return $authService;
    }
}
