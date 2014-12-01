<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 28.11.14
 * Time: 13:06
 */

namespace CategoriesTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class ManagementControllerTest
 * @package CategoriesTest\Controller
 */
class ManagementControllerTest extends AbstractHttpControllerTestCase
{

    /**
     * @var array
     */
    protected $userData = [
        'name' => 'adminTest1',
        'email' => 'aaa@gmail.com',
        'password' => '111',
        'repeat-password' => '111',
        'security' => 'e801af97d7724909d619fa44b43ea61f-ecda9ef74bf39983d75c4020e3b560de',
        'submit' => 'Sign Up'
    ];

    public function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        parent::setUp();

//        //remove user
//        $this->removeUser();

        //create user
//        $this->createUser();

        /** @var \User\Service\Auth $userAuth */
        $userAuth = $this->getApplicationServiceLocator()->get('\User\Service\Auth');
        $userAuth->authenticateEquals($this->userData['email'], $this->userData['password']);
    }

//    /**
//     *  migration up
//     */
//    public static function setUpBeforeClass()
//    {
//        exec('vendor/doctrine/doctrine-module/bin/doctrine-module orm:schema-tool:update --force');
//    }
//
//    /**
//     * migration down
//     */
//    public static function tearDownAfterClass()
//    {
//        exec('vendor/doctrine/doctrine-module/bin/doctrine-module orm:schema-tool:drop --force');
//    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/categories/management');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\ManagementController');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/default');
    }


    /**
     * remove user
     */
    public function removeUser()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $objectManager->getRepository('User\Entity\User')
            ->findOneBy(array('email' => $this->userData['email']));
        if ($user) {
            $objectManager->remove($user);
            $objectManager->flush();
        }
    }

    /**
     *  create user
     */
    public function createUser()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $this->getApplicationServiceLocator()->get('User\Entity\User');
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($this->userData, $user);
        $user->setDisplayName($user->getEmail());
        $user->setRole($user::ROLE_USER);
        $user->setConfirm($user->generateConfirm());
        $user->setStatus($user::STATUS_ACTIVE);
        $user->setRole($user::ROLE_ADMIN);
        $objectManager->persist($user);
        $objectManager->flush();

        /** @var $authService Service\Auth */
        $authService = $this->getApplicationServiceLocator()->get('User\Service\Auth');
        $authService->generateEquals($user, $this->userData['password']);

        $objectManager->getConnection()->commit();
    }

} 