<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace OptionsTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Starter\Test\Controller\ControllerTestCase;
use Zend\Http\Response;
use Zend\Stdlib;

/**
 * Class ManagementControllerTest
 * @package OptionsTest\Controller
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
        'name' => 'adminTest1',
        'email' => 'adminTest@nix.com',
        'password' => '123456',
        'repeat-password' => '123456',
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
        'submit' => 'Create'
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
    public function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        $this->setTraceError(true);
        parent::setUp();

        $this->setupAdmin();
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
        $this->dispatch('/options/management/create');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
    }

    /**
     * test create action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateAction()
    {
        $parameters = new Stdlib\Parameters($this->optionData);

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $this->dispatch('/options/management/create');
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/options/management');
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
        $parameters = new Stdlib\Parameters(
            [
            'namespace' => $option->getNamespace(),
            'key' => $option->getKey(),
            'value' => $option->getValue(),
            'description' => $option->getDescription()
            ]
        );

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $editPath = '/options/management/edit/' . $this->optionData["namespace"] . '/' . $this->optionData["key"];
        $this->dispatch($editPath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/options/management');
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

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/options/management');
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
}
