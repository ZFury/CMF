<?php

namespace CommenTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
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
     * @var array
     */
    protected $anotherUser = [
        'name' => 'adminTest1',
        'email' => 'aaa@gmail.com',
        'password' => '123456',
    ];

    protected $user;

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
    }

    public function tearDown()
    {
        $this->removeUser($this->anotherUser);
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

    public function testIndexActionNoPermission()
    {
        $this->setupUser();
        $this->dispatch('/comment/management');
        $this->assertResponseStatusCode(403);
    }

    public function testCreateActionValidPost()
    {
        $postData = array(
            'aliasEntity' => 'user',
            'entity' => 'User\Entity\User',
            'isEnabled' => 1,
            'isVisible' => 1,
            'description' => 'another',
        );
        $this->dispatch('/comment/management/create', 'POST', $postData);
        $this->assertResponseStatusCode(200);
    }

    public function testEditActionCanBeAccessed()
    {
        $entity = $this->createEntityType($this->entityData);
        $this->dispatch('/comment/management/edit/'.$entity->getId());
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('comment/default');
        $this->removeEntityType($entity);
    }

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
        $this->dispatch('/comment/management/edit/' . $entity->getId(), 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->removeEntityType($entity);
    }

    public function testDeleteActionCanBeAccessed()
    {
        $entity = $this->createEntityType($this->entityData);
        $this->dispatch("/comment/management/delete/".$entity->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('comment/default');
    }

    public function testDeleteActionNoPermission()
    {
        $entity = $this->createEntityType($this->entityData);
        $this->setupUser();
        $this->setExpectedException('Exception');
        $this->commentService->delete($entity->getId());
    }


    public function testDeleteActionNoExistEntity()
    {
        $this->dispatch('/comment/management/delete/1');
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
