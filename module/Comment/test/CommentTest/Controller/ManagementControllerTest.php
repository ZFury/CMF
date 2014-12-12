<?php

namespace CommenTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Response;
use Zend\Stdlib;
use Starter\Test\Controller\ControllerTestCase;

/**
 * Class ManagementControllerTest
 * @package CommentTest\Controller
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
        'email' => 'aaa@gmail.com',
        'password' => '123456',
    ];

    /**
     * @var array
     */
    protected $entityData = [
        'entityType' => 'default',
        'description' => 'default',
    ];

    /**
     * @var array
     */
    protected $entityDataDel = [
        'entityType' => 'defaultDel',
        'description' => 'defaultDel',
    ];

    /**
     *  Migration up
     */
    public static function setUpBeforeClass()
    {
        exec('vendor/bin/doctrine-module orm:schema-tool:update --force');
    }

    /**
     * Migration down
     */
    public static function tearDownAfterClass()
    {
        exec('vendor/bin/doctrine-module orm:schema-tool:drop --force');
    }

    /**
     * Set up
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

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/comment/management');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('comment/default');
    }


    public function testCreateActionRedirectsAfterValidPost()
    {
        $postData = array(
            'entityType' => 'another',
            'description' => 'another',
        );
        $this->dispatch('/comment/management/create', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/comment/management');
    }

    public function testEditActionCanBeAccessed()
    {
        $this->dispatch('/comment/management/edit/1');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('comment/default');
    }

    public function testDeleteActionCanBeAccessed()
    {
        $entity = $this->createEntity($this->entityData);
        $this->dispatch("/comment/management/delete/".$entity->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('comment/default');
    }

    public function testEditActionRedirectsAfterValidPost()
    {
        /**
         * @var \Comment\Entity\EntityType $entity
         */
        $entity = $this->createEntity($this->entityData);

        $postData = array(
            'entityType' => 'edited',
            'description' => 'edited',
        );
        $this->dispatch('/comment/management/edit/' . $entity->getId(), 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/comment/management');

        $this->removeEntity($entity);
    }

    public function testDeleteActionRedirectsAfterValidPost()
    {
        /**
         * @var \Comment\Entity\EntityType $entity
         */
        $entity = $this->createEntity($this->entityDataDel);

        $this->dispatch('/comment/management/delete/' . $entity->getId());
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/comment/management');
    }

    /**
     * Creates new entityType.
     *
     * @param  $entityData
     * @return \Comment\Entity\EntityType
     */
    public function createEntity($entityData)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $objectManager
         */
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = new \Comment\Entity\EntityType();
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($entityData, $entity);
        $objectManager->persist($entity);
        $objectManager->flush();
        $objectManager->getConnection()->commit();
        $objectManager->clear();

        return $entity;
    }

    /**
     * Deletes entityType.
     *
     * @param \Comment\Entity\EntityType $detachedEntity
     */
    public function removeEntity(\Comment\Entity\EntityType $detachedEntity)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $objectManager
         */
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $objectManager->merge($detachedEntity);
        $objectManager->remove($entity);
        $objectManager->flush();
    }
}
