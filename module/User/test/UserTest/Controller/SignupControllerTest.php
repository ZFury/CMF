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
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Starter\Test\Controller\ControllerTestCase;

class SignupControllerTest extends ControllerTestCase
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
     *  forgot-password action test
     */
    public function testForgotPasswordAction()
    {
        $this->createUserWithHash($this->userData);
        $form = new  \User\Form\SetNewPasswordForm('forgot-password', ['serviceLocator' => $this->getApplicationServiceLocator()]);
        $data = array(
            'email' => $this->userData['email'],
            'security' => $form->get('security')->getValue()
        );
        $this->dispatch('/user/signup/forgot-password', 'POST', $data);
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/');
    }

    /**
     *  index action access test
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

    /**
     *  signup index action test
     */
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

    /**
     *  confirm action test
     */
    public function testConfirmAction()
    {
        $user = $this->createUser(null, \User\Entity\User::ROLE_USER);
        $this->dispatch('/user/signup/confirm/' . $user->getConfirm());
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
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
