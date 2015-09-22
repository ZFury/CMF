<?php

namespace CommenTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib;
use Fury\Test\Controller\ControllerTestCase;
use \Comment\Entity\EntityType;
use Zend\Http\Client;

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
     * @var \Comment\Service\Comment
     */
    private $commentService;

    /**
     * @var array
     */
    protected $anotherUser = [
        'name' => 'adminTest1',
        'email' => 'aaa@gmail.com',
        'password' => '123456',
    ];

    /**
     * @var \User\Entity\User
     */
    protected $user;

    /**
     * @var array
     */
    protected $entityData = array(
        'alias' => 'comment',
        'entity' => 'Comment\Entity\Comment',
        'isEnabled' => 1,
        'isVisible' => 1,
        'description' => 'another',
    );

    /**
     *  Migration up
     */
    public static function setUpBeforeClass()
    {
        exec('vendor/bin/doctrine-module orm:schema-tool:drop --force');
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

        $this->user = $this->createUser($this->anotherUser);
        $this->commentService = $this->getApplicationServiceLocator()->get('Comment\Service\Comment');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        $this->removeUser($this->anotherUser);
    }

    /**
     * Index action can be accessed
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/comment/entity-type');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\EntityType');
        $this->assertControllerClass('EntityTypeController');
        $this->assertMatchedRouteName('comment/default');
    }

    /**
     * Index action can not be accessed (Permission denied)
     */
    public function testIndexActionNoPermission()
    {
        $this->setupUser();
        $this->dispatch('/comment/entity-type');
        $this->assertResponseStatusCode(403);
    }

    /**
     *
     * Create action valid post data
     */
    public function testCreateActionValidPost()
    {
        $postData = array(
            'alias' => 'user',
            'entity' => 'User\Entity\User',
            'isEnabled' => 1,
            'isVisible' => 1,
            'description' => 'another',
        );
        $this->dispatch('/comment/entity-type/create', 'POST', $postData);
        $this->assertResponseStatusCode(302);
    }

    /**
     * Edit action can be accessed
     *
     * @throws \Exception
     */
    public function testEditActionCanBeAccessed()
    {
        $entity = $this->createEntityType($this->entityData);
        $this->dispatch('/comment/entity-type/edit/'.$entity->getId());
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\EntityType');
        $this->assertControllerClass('EntityTypeController');
        $this->assertMatchedRouteName('comment/default');
        $this->removeEntityType($entity);
    }

    /**
     * Edit action valid post data
     *
     * @throws \Exception
     */
    public function testEditActionValidPost()
    {
        $entity = $this->createEntityType($this->entityData);
        $postData = array(
            'alias' => 'userEdited',
            'entity' => 'Test\Entity\Test',
            'isEnabled' => 1,
            'isVisible' => 1,
            'description' => 'another',
        );
        $this->dispatch('/comment/entity-type/edit/' . $entity->getId(), Request::METHOD_POST, $postData);
        $this->assertResponseStatusCode(302);
        $this->removeEntityType($entity);
    }

    /**
     * Delete action can be accessed
     *
     * @throws \Exception
     */
    public function testDeleteActionCanBeAccessed()
    {
        $entity = $this->createEntityType($this->entityData);
        $this->dispatch("/comment/entity-type/delete/".$entity->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\EntityType');
        $this->assertControllerClass('EntityTypeController');
        $this->assertMatchedRouteName('comment/default');
    }

    /**
     * Delete action can not be accessed  (Permission denied)
     *
     * @throws \Exception
     */
    public function testDeleteActionNoPermission()
    {
        $entity = $this->createEntityType($this->entityData);
        $this->setupUser();
        $this->setExpectedException('Exception');
        $this->commentService->delete($entity->getId());
        $this->assertResponseStatusCode(403);
    }

    /**
     * Delete non-existing entity
     */
    public function testDeleteActionNoExistEntity()
    {
        $this->dispatch('/comment/entity-type/delete/100');
        $this->assertApplicationException('\Doctrine\ORM\EntityNotFoundException');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @param $entityData
     * @return \Comment\Entity\EntityType
     * @throws \Exception
     */
    protected function createEntityType($entityData)
    {
        $entity = new EntityType();
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $objectManager->getConnection()->beginTransaction();
        try {
            $hydrator = new DoctrineHydrator($objectManager);
            $hydrator->hydrate($entityData, $entity);
            $objectManager->persist($entity);
            $objectManager->flush();
            $objectManager->getConnection()->commit();
        } catch (\Exception $e) {
            $objectManager->getConnection()->rollback();
            throw $e;
        }

        return $entity;
    }

    /**
     * @param \Comment\Entity\EntityType $detachedEntity
     */
    protected function removeEntityType(EntityType $detachedEntity)
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
