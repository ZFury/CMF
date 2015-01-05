<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 22.05.14
 * Time: 16:43
 */

namespace UserTest\Controller;

use SebastianBergmann\Exporter\Exception;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Starter\Test\Controller\ControllerTestCase;

class AuthControllerTest extends ControllerTestCase
{
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

    public function testRecoverPasswordAction()
    {

    }

    public function testLogoutActionCanBeAccessed()
    {
        $this->setupUser();
        $this->dispatch('/user/auth/logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/auth/login');
    }

    public function testLoginAction()
    {
        $userData = [
            'name' => 'User',
            'email' => 'user@nix.com',
            'password' => '123456'
        ];
        $this->createUser($userData, \User\Entity\User::ROLE_USER);
        $postData = [
            'email' => $userData['email'],
            'password' => $userData['password']
        ];

        $this->dispatch('/user/auth/login', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
    }

    public function testLogoutAction()
    {
        $this->setupUser();
        $this->dispatch('/user/auth/logout');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/auth/login');
    }
}
