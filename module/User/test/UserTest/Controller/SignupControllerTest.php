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

class SignupControllerTest extends ControllerTestCase
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
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/user/signup/index');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Signup');
        $this->assertControllerClass('SignupController');
        $this->assertMatchedRouteName('user/default');
    }

    public function testIndex()
    {
        $form = new  \User\Form\SignupForm('create-user', ['serviceLocator' => $this->getApplicationServiceLocator()]);
        $data = [
            'security' => $form->get('security')->getValue(),
            'email' => 'aaaaaa@gmail.com',
            'password' => '123456',
            'repeat-password' => '123456',
        ];
        $this->dispatch('/user/signup/index', 'POST', $data);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
    }

    public function testConfirmAction()
    {
        $user = $this->createUser(null, \User\Entity\User::ROLE_USER);
        $this->dispatch('/user/signup/confirm/' . $user->getConfirm());
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
    }
}
