<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 12/3/14
 * Time: 6:27 PM
 */

namespace Starter\Test\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use User\Service\Auth;
use User\Entity;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

abstract class ControllerTestCase extends AbstractHttpControllerTestCase
{
    /**
     * Default user data.
     *
     * @var array
     */
    private $userData = [
        'name' => 'Admin',
        'email' => 'admin@nix.com',
        'password' => '123456'
    ];

    /**
     * Creates and logs in new user.
     *
     * @param array $userData
     */
    protected function setupUser(array $userData = null)
    {
        $this->removeUser($userData);
        $user = $this->createUser($userData);
        $this->loginUser($user);
    }

    /**
     * Creates and logs in new admin user.
     *
     * @param array $userData
     */
    protected function setupAdmin(array $userData = null)
    {
        $this->removeUser($userData);
        $user = $this->createUser($userData, Entity\User::ROLE_ADMIN);
        $this->loginUser($user);
    }

    /**
     * Deletes user.
     *
     * @param array $userData
     */
    protected function removeUser(array $userData = null)
    {
        !$userData ? $data = $this->userData : $data = $userData;
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $objectManager->getRepository('User\Entity\User')
            ->findOneBy(array('email' => $data['email']));
        if ($user) {
            $objectManager->remove($user);
            $objectManager->flush();
        }
    }

    /**
     * Creates new user.
     *
     * @param array $userData
     * @return \User\Entity\User
     */
    protected function createUser(array $userData = null, $role = null)
    {
        !$userData ? $data = $this->userData : $data = $userData;
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = new Entity\User();
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($data, $user);
        $user->setDisplayName($user->getEmail());
        $user->setRole($user::ROLE_USER);
        $user->setConfirm($user->generateConfirm());
        $user->setStatus($user::STATUS_ACTIVE);
        if ($role == $user::ROLE_ADMIN) {
            $user->setRole($user::ROLE_ADMIN);
        }
        $objectManager->persist($user);
        $objectManager->flush();

        /** @var $authService \User\Service\Auth */
        $authService = $this->getApplicationServiceLocator()->get('User\Service\Auth');
        $authService->generateEquals($user, $data['password']);

        $objectManager->getConnection()->commit();

        return $user;
    }

    /**
     * Authorizes user.
     *
     * @param \User\Entity\User $user
     */
    protected function loginUser(Entity\User $user)
    {
        /** @var \User\Entity\Auth $userAuth */
        $userAuth = new Entity\Auth();

        $userAuth->setUserId($user->getId());
        $userAuth->setProvider(Auth::PROVIDER_EQUALS);
        $userAuth->setUser($user);

        $userAuth->login($this->getApplicationServiceLocator());
    }
}
