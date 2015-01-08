<?php

namespace CommenTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Response;
use Zend\Stdlib;
use Starter\Test\Controller\ControllerTestCase;
use \Comment\Entity\EntityType;
use \Comment\Entity\Comment;

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

    protected $user;

    protected $entityType;

    protected $anotherUser = [
        'name' => 'Admin1',
        'email' => 'admin1@nix.com',
        'password' => '123456'
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

        $this->user = $this->createUser($this->anotherUser);
        $entityType = array(
            'aliasEntity' =>'user',
            'entity' =>'User\Entity\User',
            'enabledComment' => true,
            'visibleComment' => true,
            'description' =>'description',
        );
        $this->entityType = $this->createEntityType($entityType);
    }

    public function tearDown()
    {
        $this->removeUser($this->anotherUser);
        $this->dropComments();
        $this->removeEntityType($this->entityType);
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
            'entity' => $this->entityType->getAliasEntity(),
            'entityId' => $this->user->getId(),
        );
        $this->dispatch('/comment/index/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
    }

    public function testEditActionCanBeAccessed()
    {
        $comment = $this->createComment('testComment');

        $this->dispatch('/comment/index/edit/'.$comment->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
    }

    public function testEditActionRedirectsAfterValidPost()
    {
        $comment = $this->createComment('Comment for edited');

        $postData = array(
            'comment' => 'edited'
        );
        $this->dispatch('/comment/index/edit/' . $comment->getId(), 'POST', $postData);
        $this->assertResponseStatusCode(302);
    }

    public function testDeleteActionCanBeAccessed()
    {
        $comment = $this->createComment('Comment for deleted');
        $this->dispatch("/comment/index/delete/".$comment->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
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
     * @param $commentText
     * @return \Comment\Entity\Comment
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    protected function createComment($commentText)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $objectManager
         */
        $commentData = array(
            'comment' => $commentText,
            'entityType' => $this->entityType,
            'entityId' => $this->user->getId(),
            'user' => $this->getApplicationServiceLocator()->get('Zend\Authentication\AuthenticationService')
                ->getIdentity()->getUser(),
        );
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $comment = new Comment();
        $objectManager->getConnection()->beginTransaction();
        try {
            $hydrator = new DoctrineHydrator($objectManager);
            $hydrator->hydrate($commentData, $comment);
            $objectManager->persist($comment);
            $objectManager->flush();
            $objectManager->getConnection()->commit();
            $objectManager->clear();
        } catch (\Exception $e) {
            $objectManager->getConnection()->rollback();
            throw $e;
        }

        return $comment;
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

    /**
     * @throws \Exception
     */
    protected function dropComments()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $sql = 'TRUNCATE TABLE comment';
        $objectManager->getConnection()->beginTransaction();
        try {
            $connection = $objectManager->getConnection();
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $objectManager->getConnection()->commit();
            $objectManager->clear();
        } catch (\Exception $e) {
            $objectManager->getConnection()->rollback();
            throw $e;
        }
    }
}
