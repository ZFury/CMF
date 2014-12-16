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
class IndexControllerTest extends ControllerTestCase
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
    protected $commentData = [
        'comment' => 'text Comment',
        'entityType' => 'default',
        'entityId' => 1,
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
        $this->dispatch('/comment/index/index');
        $this->assertResponseStatusCode(404);

        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
    }

    public function testAddActionCanBeAccessed()
    {
        $this->dispatch('/comment/index/add');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
    }

    public function testAddActionRedirectsAfterValidPost()
    {
        $postData = array(
            'comment' => "test comment",
            'entityType' => 'another',
            'entityId' => 1,
        );
        $entityType = array(
            'entityType' =>$postData['entityType'],
            'description' =>'description',
        );
        $this->createEntityType($entityType);

        $this->dispatch('/comment/index/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
    }

    public function testEditActionCanBeAccessed()
    {
        $comment = $this->createComment($this->commentData);

        $this->dispatch('/comment/index/edit/'.$comment->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
        $this->removeEntity($comment);
    }

    public function testEditActionRedirectsAfterValidPost()
    {
        /**
         * @var \Comment\Entity\Comment $comment
         */
        $comment = $this->createComment($this->commentData);

        $postData = array(
            'comment' => 'edited'
        );
        $this->dispatch('/comment/index/edit/' . $comment->getId(), 'POST', $postData);
        $this->assertResponseStatusCode(200);

        $this->removeEntity($comment);
    }

    /**
     * Creates new entityType.
     *
     * @param  $entityData
     * @return \Comment\Entity\EntityType
     */
    public function createEntityType($entityData)
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

    /*public function testDeleteActionCanBeAccessed()
    {
        $comment = $this->createComment($this->commentData);
        $this->dispatch("/comment/index/delete/".$comment->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
    }*/

    /**
     * @param $commentData
     * @return \Comment\Entity\Comment
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function createComment($commentData)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $objectManager
         */
        $commentData['user'] = $this->getApplicationServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity()->getUser();
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $comment = new \Comment\Entity\Comment();
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($commentData, $comment);
        $objectManager->persist($comment);
        $objectManager->flush();
        $objectManager->getConnection()->commit();
        $objectManager->clear();

        return $comment;
    }

    /**
     * @param \Comment\Entity\Comment $detachedEntity
     */
    public function removeEntity(\Comment\Entity\Comment $detachedEntity)
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
