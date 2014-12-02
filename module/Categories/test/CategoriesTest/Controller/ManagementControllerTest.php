<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 28.11.14
 * Time: 13:06
 */

namespace CategoriesTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class ManagementControllerTest
 * @package CategoriesTest\Controller
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
        'email' => 'aaa@gmail.com',
        'password' => '123456',
        'repeat-password' => '123456',
        'submit' => 'Sign Up'
    ];

    /**
     * @var array
     */
    protected $categoryData = [
        'name' => 'default',
        'alias' => 'default',
        'order' => '1',
        'parentId' => '',
        'submit' => 'Create'
    ];

    /**
     * @throws \User\Exception\AuthException
     */
    public function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        parent::setUp();

        $this->removeUser();

        $this->createUser();

        /** @var \User\Service\Auth $userAuth */
        $userAuth = $this->getApplicationServiceLocator()->get('\User\Service\Auth');
        $userAuth->authenticateEquals($this->userData['email'], $this->userData['password']);
    }

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
     *
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/categories/management');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/default');
    }

    /**
     *
     */
    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/categories/management/create');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/create');
    }

    /**
     *
     */
    public function testOrderActionCanBeAccessed()
    {
        $this->dispatch('/categories/management/order');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/categories/management/index');
    }

    /**
     *
     */
    public function testCreateActionRedirectsAfterValidPost()
    {
        $postData = array(
            'name' => 'another',
            'alias' => 'another',
            'parentId' => '',
            'order' => '1',
        );
        $this->dispatch('/categories/management/create', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/categories/management/index');
    }

    /**
     *
     */
    public function testEditActionCanBeAccessed()
    {
        $this->dispatch('/categories/management/edit/1');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/default');
    }

    /**
     *
     */
    public function testEditActionRedirectsAfterValidPost()
    {
        $this->createCategory($this->categoryData);

        $postData = array(
            'name' => 'edited',
            'alias' => $this->categoryData['alias'],
            'parentId' => $this->categoryData['parentId'],
            'order' => $this->categoryData['order'],
        );
        $this->dispatch('/categories/management/edit/2', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/categories/management/index');
    }

    /**
     *
     */
    public function testDeleteAction()
    {
        $this->createCategory($this->categoryData);

        $deletePath = '/categories/management/delete/3';
        $this->dispatch($deletePath);

        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertRedirectTo('/categories/management/index');
    }

    /**
     *
     */
    public function testOrderAction()
    {
        $subCategoryData1 = [
            'name' => 'default1',
            'alias' => 'default1',
            'order' => '3',
            'parentId' => '1',
            'submit' => 'Create'
        ];
//        $subCategoryData2 = [
//            'name' => 'default2',
//            'alias' => 'default2',
//            'order' => '4',
//            'parentId' => '1',
//            'submit' => 'Create'
//        ];
//
        $this->createCategory($subCategoryData1);
//        $this->createCategory($subCategoryData2);

        $json = json_encode([['item_id' => null, "parent_id" => 'none', "depth" => 0, "left" => 1, "right" => 6],
            ['item_id' => 4, "parent_id" => null, "depth" => 1, "left" => 2, "right" => 3, "order" => 1],
//            ['item_id' => 5, "parent_id" => null, "depth" => 1, "left" => 4, "right" => 5, "order" => 2],
        ]);
        $postData = [
            'tree' => $json,
            'treeParent' => 1,
        ];

        $this->dispatch('/categories/management/order', 'POST', $postData);
        $this->assertResponseStatusCode(200);
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

        /** @var $authService \User\Service\Auth */
        $authService = $this->getApplicationServiceLocator()->get('User\Service\Auth');
        $authService->generateEquals($user, $this->userData['password']);

        $objectManager->getConnection()->commit();
    }

    /**
     *
     */
    public function createCategory($categoryData)
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $category = $this->getApplicationServiceLocator()->get('Categories\Entity\Categories');
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($categoryData, $category);
        $objectManager->persist($category);
        $objectManager->flush();
        $objectManager->getConnection()->commit();
    }
}
