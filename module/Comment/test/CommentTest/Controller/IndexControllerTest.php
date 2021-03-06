<?php

namespace CommenTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib;
use Fury\Test\Controller\ControllerTestCase;
use \Comment\Entity\EntityType;
use \Comment\Entity\Comment;

/**
 * Class ManagementControllerTest
 * @package CommentTest\Controller
 */
class IndexControllerTest extends ControllerTestCase
{
    /**
     * @var \Comment\Service\Comment
     */
    private $commentService;

    /**
     * @var bool
     */
    protected $traceError = true;

    /**
     * @var \User\Entity\User
     */
    protected $user;

    /**
     * @var \Comment\Entity\EntityType
     */
    protected $entityType;

    /**
     * @var array
     */
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
        $entityType = array(
            'alias' =>'user',
            'entity' =>'User\Entity\User',
            'isEnabled' => 1,
            'isVisible' => 1,
            'description' =>'description',
        );
        $this->entityType = $this->createEntityType($entityType);
        $this->commentService = $this->getApplicationServiceLocator()->get('Comment\Service\Comment');
    }

    /**
     * Tear down
     *
     * @throws \Exception
     */
    public function tearDown()
    {
        $this->removeUser($this->anotherUser);
        $this->dropComments();
        $this->removeEntityType($this->entityType);
    }

    /**
     * Add action bad request
     */
    public function testAddActionBadRequest()
    {
        $this->dispatch('/comment/index/add', Request::METHOD_POST, ['aa'=>'bb']);
        $this->assertResponseStatusCode(400);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
    }

    /**
     * Add action redirection after valid post
     */
    public function testAddActionRedirectsAfterValidPost()
    {
        $postData = array(
            'comment' => "test comment",
            'alias' => $this->entityType->getAlias(),
            'id' => $this->user->getId(),
        );

        $this->dispatch(
            '/comment/index/add?alias=' .
            $this->entityType->getAlias() .
            '&id=' . $this->user->getId(),
            'POST',
            $postData
        );

        $this->assertResponseStatusCode(302);
    }

    /**
     * Add action un-enabled entity
     *
     * @throws \Exception
     */
    public function testAddActionNoEnabledEntity()
    {
        $form = $this->commentService->createForm();
        $postData = array(
            'comment' => "test comment",
            'alias' => $this->entityType->getAlias(),
            'id' => $this->user->getId(),
        );
        $this->entityType->setIsEnabled(0);
        $this->assertNull($this->commentService->add($form, $postData));
    }

    /**
     * Comment service add invalid entity
     *
     * @throws \Exception
     */
    public function testAddInvalidEntity()
    {
        $form = $this->commentService->createForm();
        $postData = array(
            'comment' => "test comment",
            'alias' => "some",
            'id' => $this->user->getId(),
        );
        $this->setExpectedException('Doctrine\ORM\EntityNotFoundException');
        $this->commentService->add($form, $postData);
    }

    /**
     * Edit action can be accessed
     *
     * @throws \Exception
     */
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

    /**
     * Edit action redirection after valid post
     *
     * @throws \Exception
     */
    public function testEditActionRedirectsAfterValidPost()
    {
        $comment = $this->createComment('Comment for editing');

        $postData = array(
            'comment' => 'edited'
        );
        $this->dispatch('/comment/index/edit/' . $comment->getId(), 'POST', $postData);
        $this->assertResponseStatusCode(302);
    }

    /**
     * Editing a non-existing comment
     */
    public function testEditActionCommentDoesntExist()
    {
        $postData = array(
            'comment' => 'edited'
        );
        $this->dispatch('/comment/index/edit/1', 'POST', $postData);
        $this->assertResponseStatusCode(404);
    }

    /**
     * Edit action can not be accessed (Permission denied)
     *
     * @throws \Exception
     */
    public function testEditActionNoPermission()
    {
        $form = $this->commentService->createForm();
        $comment = $this->createComment('Comment');
        $this->setupUser();
        $postData = array(
            'comment' => 'edited'
        );
        $this->setExpectedException('Exception');
        $this->commentService->edit($form, $comment, $postData);
    }

    /**
     * Delete action can be accessed
     *
     * @throws \Exception
     */
    public function testDeleteActionCanBeAccessed()
    {
        $comment = $this->createComment('Comment for deleting');
        $this->dispatch("/comment/index/delete/".$comment->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('comment');
        $this->assertControllerName('Comment\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('comment/default');
    }

    /**
     * Delete action can not be accessed (Permission denied)
     *
     * @throws \Exception
     */
    public function testDeleteActionNoPermission()
    {
        $comment = $this->createComment('Comment for deleting');
        $this->setupUser();
        $this->setExpectedException('Exception');
        $this->commentService->delete($comment->getId());
    }

    /**
     * Deleting of non-existing comment
     */
    public function testDeleteActionCommentDoesntExist()
    {
        $this->dispatch('/comment/index/delete/777777777');
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
