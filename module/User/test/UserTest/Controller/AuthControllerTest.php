<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 22.05.14
 * Time: 16:43
 */

namespace UserTest\Controller;

use SebastianBergmann\Exporter\Exception;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Starter\Test\Controller\ControllerTestCase;
use Zend\Stdlib;

class AuthControllerTest extends ControllerTestCase
{

    /**
     * @var array
     */
    protected $userData = [
        'name' => 'testUser',
        'email' => 'testUser@nix.com',
        'password' => '123456',
        'repeat-password' => '123456',
        'role' => 'user',
        'status' => 'active',
        'confirm' => '09310bf5aa26b8860d1705e361d1ba56'
    ];

    /**
     * Set up tests
     */
    public function setUp()
    {
        $this->setApplicationConfig(
            include './config/application.config.php'
        );

        $this->setTraceError(true);
        parent::setUp();
    }

    /**
     * Set up database
     */
    public static function setUpBeforeClass()
    {
        exec('./vendor/bin/doctrine-module orm:schema-tool:update --force');
    }

    /**
     * Tear down database
     */
    public static function tearDownAfterClass()
    {
        exec('./vendor/bin/doctrine-module orm:schema-tool:drop --force');
    }

    /**
     *  login action test
     */
    public function testLoginActionCanBeAccessed()
    {
        $this->dispatch('/user/auth/login');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Auth');
        $this->assertControllerClass('AuthController');
        $this->assertMatchedRouteName('user/default');
    }

    /**
     *  recovery-password action test
     */
    public function testRecoverPasswordAction()
    {
        $this->createUserWithHash($this->userData);

        $form = new  \User\Form\SetNewPasswordForm(
            'set-password',
            ['serviceLocator' => $this->getApplicationServiceLocator()]
        );
        $path = '/user/auth/recover-password/' . $this->userData['confirm'];
        $data = array(
            'password' => '123456',
            'repeat-password' => '123456',
            'security' => $form->get('security')->getValue()
        );
        $this->dispatch($path, 'POST', $data);
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/');
    }

    /**
     *  logout action test access
     */
    public function testLogoutActionCanBeAccessed()
    {
        $this->setupUser();
        $this->dispatch('/user/auth/logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/auth/login');
    }

    /**
     *  login action test
     */
    public function testLoginAction()
    {
        $form = new  \User\Form\LoginForm('form-login', ['serviceLocator' => $this->getApplicationServiceLocator()]);
        $userData = [
            'name' => 'User',
            'email' => 'user@nix.com',
            'password' => '123456'
        ];
        $this->createUser($userData, \User\Entity\User::ROLE_USER);
        $postData = [
            'email' => $userData['email'],
            'password' => $userData['password'],
            'security' => $form->get('security')->getValue()
        ];

        $this->dispatch('/user/auth/login', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
    }

    /**
     *  logout action test
     */
    public function testLogoutAction()
    {
        $this->setupUser();
        $this->dispatch('/user/auth/logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/auth/login');
    }

    /**
     * @param array $userData
     * @return \User\Entity\User
     */
    public function createUserWithHash(array $userData)
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = new \User\Entity\User();
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($userData, $user);
        $user->setDisplayName($user->getEmail());

        $objectManager->persist($user);
        $objectManager->flush();

        /** @var $authService \User\Service\Auth */
        $authService = $this->getApplicationServiceLocator()->get('User\Service\Auth');
        $authService->generateEquals($user, $userData['password']);

        $objectManager->getConnection()->commit();

        return $user;
    }
}
