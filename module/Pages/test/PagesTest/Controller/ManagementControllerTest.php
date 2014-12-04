<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace Pages\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use PHPUnit_Framework_TestCase;

/**
 * Class ManagementControllerTest
 * @package PagesTest\Controller
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
//        'security' => 'e801af97d7724909d619fa44b43ea61f-ecda9ef74bf39983d75c4020e3b560de',
        'submit' => 'Sign Up'
    ];

    /**
     * @var array
     */
    protected $pageData = [
        'title' => 'Title',
        'alias' => 'default-page',
        'content' => 'Hello and welcome
            Lorem ipsum dolor sit amet, consectetur adipisicing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
        'keywords' => 'keyword',
        'description' => 'description',
        'id' => '',
        'userId' => '',
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
     *  remove entity
     */
    public function tearDown()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $objectManager->getRepository('Pages\Entity\Pages')
            ->findOneBy(array('alias' => $this->pageData['alias']));
        if ($entity) {
            $objectManager->remove($entity);
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
        $this->dispatch('/pages');
        $this->assertResponseStatusCode(200);
    }

    /**
     * create action access
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/pages/management/create');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
    }

    /**
     * test create action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateAction()
    {
        $parameters = new Stdlib\Parameters($this->pageData);

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $this->dispatch('/pages/management/create');
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/pages/management');
    }

    /**
     * test edit action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testEditAction()
    {
        //create
        $this->createEntity();

        //get
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $objectManager->getRepository('Pages\Entity\Pages')
            ->findOneBy(array('alias' => $this->pageData['alias']));

        //dispatch edit + post data
        $parameters = new Stdlib\Parameters([
            'title' => $entity->getTitle(),
            'alias' => $entity->getAlias(),
            'content' => $entity->getContent(),
            'keywords' => $entity->getKeywords(),
            'description' => $entity->getDescription(),
            'id' => $entity->getId(),
            'userId' => $entity->getUserId(),
            'submit' => 'Edit'
        ]);

        $this->getRequest()->setMethod('POST')
            ->setPost($parameters);

        $editPath = '/pages/management/edit/' . $entity->getId();
        $this->dispatch($editPath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/pages/management');
    }

    /**
     * test delete action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testDeleteAction()
    {
        //create
        $entity = $this->createEntity();

        // remove
        $deletePath = '/pages/management/delete/' . $entity->getId();
        $this->dispatch($deletePath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/pages/management');
    }

    /**
     *  create entity
     */
    public function createEntity()
    {
        $entity = new \Pages\Entity\Pages();
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($this->pageData, $entity);
        $objectManager->persist($entity);
        $objectManager->flush();
        $objectManager->getConnection()->commit();

        return $entity;
    }

    /**
     * remove user
     */
    public function removeUser()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $objectManager->getRepository('User\Entity\User')
            ->findOneBy(array('email' => $this->userData['email']));
        if ($user) {
            $objectManager->remove($user);
            $objectManager->flush();
        }
    }

    /**
     *  create user
     */
    public function createUser()
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = new \User\Entity\User();
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

        /** @var $authService \User\Service\Auth */
        $authService = $this->getApplicationServiceLocator()->get('User\Service\Auth');
        $authService->generateEquals($user, $this->userData['password']);

        $objectManager->getConnection()->commit();
    }
}
