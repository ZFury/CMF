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
use Fury\Test\Controller\ControllerTestCase;

class ProfileControllerTest extends ControllerTestCase
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

    public function testIndexActionCanBeAccessed()
    {
        $this->setupUser();

        $this->dispatch('/user/profile');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Profile');
        $this->assertControllerClass('ProfileController');
        $this->assertMatchedRouteName('user/default');
    }

    public function testChangePasswordActionCanBeAccessed()
    {
        $this->setupUser();

        $this->dispatch('/user/profile/change-password');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Profile');
        $this->assertControllerClass('ProfileController');
        $this->assertMatchedRouteName('user/default');
    }

    public function testChangePasswordAction()
    {
        $userData = [
            'name' => 'User',
            'email' => 'user@nix.com',
            'password' => '123456'
        ];
        $user = $this->createUser($userData, \User\Entity\User::ROLE_USER);
        $this->loginUser($user);
        $postData = [
            'currentPassword' => $userData['password'],
            'password' => '111',
            'repeat-password' => '111',
        ];
        $this->dispatch('/user/profile/change-password', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
    }
}
