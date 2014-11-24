<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace OptionsTest\Controller;

use Options\Controller\ManagementController;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ManagementControllerTest
 * @package OptionsTest\Controller
 */
class ManagementControllerTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError = true;

    /**
     * @var array
     */
    protected $userData = [
        'name' => 'adminTest1',
        'email' => 'adminTest@nix.com',
        'password' => '123456',
        'repeat-password' => '123456',
        'security' => 'e801af97d7724909d619fa44b43ea61f-ecda9ef74bf39983d75c4020e3b560de',
        'submit' => 'Sign Up'
    ];

    /**
     * @var array
     */
    protected $optionData = [
        'namespace' => 'default',
        'key' => '1',
        'value' => 'value',
        'description' => 'description',
        'submit" => "Create'
    ];

    /**
     *  migration up
     */
    public static function setUpBeforeClass()
    {
        exec('vendor/doctrine/doctrine-module/bin/doctrine-module orm:schema-tool:update --force');
    }

    /**
     * migration down
     */
    public static function tearDownAfterClass()
    {
        exec('vendor/doctrine/doctrine-module/bin/doctrine-module orm:schema-tool:drop --force');
    }

    /**
     * @throws \User\Exception\AuthException
     */
    protected function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        parent::setUp();

        //remove user
        $this->removeUser();

        //create user
        $this->createUser();

        /** @var \User\Service\Auth $userAuth */
        $userAuth = $this->getApplicationServiceLocator()->get('\User\Service\Auth');
        $userAuth->authenticateEquals($this->userData['email'], $this->userData['password']);
    }

    /**
     *  remove option
     */
    public function tearDown()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager->getRepository('Options\Entity\Options')
            ->findOneBy(array('namespace' => $this->optionData['namespace'], 'key' => $this->optionData['key']));
        if ($option) {
            $objectManager->remove($option);
            $objectManager->flush();
        }
    }

    /**
     * index action access
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/options');
        $this->assertResponseStatusCode(200);
    }

    /**
     * create action access
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/option/create');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
    }

    /**
     * test create action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateAction()
    {
        $parameters = new Stdlib\Parameters(
            array(
                'namespace' => 'default',
                'key' => '1',
                'value' => 'value',
                'description' => 'description',
                "submit" => "Create"
            )
        );

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $this->dispatch('/option/create');
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/options');
    }

    /**
     * test edit action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testEditAction()
    {
        //create
        $this->createOption();

        //get
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->findOneBy(array('namespace' => $this->optionData['namespace'], 'key' => $this->optionData['key']));

        //dispatch edit + post data
        $parameters = new Stdlib\Parameters([
            'namespace' => $option->getNamespace(),
            'key' => $option->getKey(),
            'value' => $option->getValue(),
            'description' => $option->getDescription()
        ]);

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $editPath = '/options/management/edit/' . $this->optionData["namespace"] . '/' . $this->optionData["key"];
        $this->dispatch($editPath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/options');
    }

    /**
     * test delete action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testDeleteAction()
    {
        //create
        $this->createOption();

        // remove option
        $deletePath = '/options/management/delete/' . $this->optionData["namespace"] . '/' . $this->optionData["key"];
        $this->dispatch($deletePath);

//        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
//        $option = $objectManager
//            ->getRepository('Options\Entity\Options')
//            ->findOneBy(array('namespace' => $this->optionData['namespace'], 'key' => $this->optionData['key']));


        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/options');
    }

    /**
     *  create option
     */
    public function createOption()
    {
        $option = $this->getApplicationServiceLocator()->get('Options\Entity\Options');
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($this->optionData, $option);
        $option->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        $option->setUpdated(new \DateTime(date('Y-m-d H:i:s')));
        $objectManager->persist($option);
        $objectManager->flush();
        $objectManager->getConnection()->commit();
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

    /**
     * remove user
     */
    public function removeUser()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('email' => $this->userData['email']));
        if ($user) {
            $objectManager->remove($user);
            $objectManager->flush();
        }
    }
}
