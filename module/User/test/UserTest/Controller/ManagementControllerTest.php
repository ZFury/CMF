<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace User\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Response;
use Zend\Stdlib;
use Starter\Test\Controller\ControllerTestCase;

/**
 * Class ManagementControllerTest
 * @package User\Controller
 */
class ManagementControllerTest extends ControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError = true;

    /**
     * @var array
     */
    protected $userData = [
        'name' => 'testUser',
        'email' => 'testUser@nix.com',
        'password' => '123456',
        'repeat-password' => '123456',
        'role' => 'user',
        'status' => 'active'
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
        $this->setTraceError(true);
        parent::setUp();

        $this->setupAdmin();
    }

    /**
     *  remove entity
     */
    public function tearDown()
    {
        $this->removeUser($this->userData);
    }

    /**
     * index action access
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/user/management/index');
        $this->assertResponseStatusCode(200);
    }

    /**
     * create action access
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/user/management/create');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
    }

    /**
     * test create action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateAction()
    {
        $parameters = new Stdlib\Parameters($this->userData);

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $this->dispatch('/user/management/create');
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/user/management');
    }

    /**
     * test edit action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testEditAction()
    {
        //create
        $this->createUser($this->userData);

        //get
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        /** @var \User\Entity\User $entity */
        $entity = $objectManager->getRepository('User\Entity\User')
            ->findOneBy(array('email' => $this->userData['email']));

        //dispatch edit + post data
        $parameters = new Stdlib\Parameters(
            [
                'id' => $entity->getId(),
                'email' => $entity->getEmail(),
                'displayName' => $entity->getDisplayName(),
                'password' => $this->userData['email'],
                'repeat-password' => $this->userData['email'],
                'role' => $entity->getRole(),
                'status' => $entity->getStatus()
            ]
        );

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $editPath = '/user/management/edit/' . $entity->getId();
        $this->dispatch($editPath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/user/management');
    }

    /**
     * test delete action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testDeleteAction()
    {
        //create
        $entity = $this->createUser($this->userData);

        // remove
        $deletePath = '/user/management/delete/' . $entity->getId();
        $this->dispatch($deletePath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/user/management');
    }
}
