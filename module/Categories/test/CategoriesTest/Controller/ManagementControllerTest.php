<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 28.11.14
 * Time: 13:06
 */

namespace CategoriesTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use User\Service\Auth;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Starter\Test\PHPUnit\Controller\AbstractAuthControllerTestCase;

/**
 * Class ManagementControllerTest
 * @package CategoriesTest\Controller
 */
class ManagementControllerTest extends AbstractAuthControllerTestCase//AbstractHttpControllerTestCase
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
    protected $categoryData = [
        'name' => 'default',
        'alias' => 'default',
        'order' => '1',
        'parentId' => '',
    ];

    /**
     *  Migration up
     */
    public static function setUpBeforeClass()
    {
        exec('vendor/doctrine/doctrine-module/bin/doctrine-module orm:schema-tool:update --force');
    }

    /**
     * Migration down
     */
    public static function tearDownAfterClass()
    {
        exec('vendor/doctrine/doctrine-module/bin/doctrine-module orm:schema-tool:drop --force');
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
        $this->dispatch('/categories/management');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/default');
    }

    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/categories/management/create');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/create');
    }

    public function testOrderActionCanBeAccessed()
    {
        $this->dispatch('/categories/management/order');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/categories/management/index');
    }

    public function testCreateActionRedirectsAfterValidPost()
    {
        $postData = array(
            'name' => 'another',
            'alias' => 'another',
            'submit' => 'Save',
        );
        $this->dispatch('/categories/management/create', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/categories/management/index');
    }

    public function testEditActionCanBeAccessed()
    {
        $this->dispatch('/categories/management/edit/1');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Categories');
        $this->assertControllerName('Categories\Controller\Management');
        $this->assertControllerClass('ManagementController');
        $this->assertMatchedRouteName('categories/default');
    }

    public function testEditActionRedirectsAfterValidPost()
    {
        /** @var \Categories\Entity\Categories $category */
        $category = $this->createCategory($this->categoryData);

        $postData = array(
            'name' => 'edited',
            'alias' => $category->getAlias(),
            'submit' => 'Save',
        );
        $this->dispatch('/categories/management/edit/' . $category->getId(), 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/categories/management/index');

        $this->removeCategory($category);
    }

    public function testOrderAction()
    {
        $subCategoryData1 = [
            'name' => 'default1',
            'alias' => 'default1',
            'order' => '3',
            'parentId' => '',
        ];
        $category1 = $this->createCategory($subCategoryData1);

//        $subCategoryData2 = [
//            'name' => 'default2',
//            'alias' => 'default2',
//            'order' => '4',
//            'parentId' => $category1->getId(),//$category1->getId()
//        ];
//        $category2 = $this->createCategory($subCategoryData2);

//        $categories = $this->createSubCategory();
        $json = json_encode([['item_id' => null, "parent_id" => 'none', "depth" => 0, "left" => 1, "right" => 4],
//            ['item_id' => $category2->getId(), "parent_id" => null, "depth" => 1, "left" => 2, "right" => 3, "order" => 1],
//            ['item_id' => $categories[1]->getId(), "parent_id" => null, "depth" => 1, "left" => 4, "right" => 5, "order" => 2],
        ]);
        $postData = [
            'tree' => $json,
            'treeParent' => 1,
        ];

        $this->dispatch('/categories/management/order', 'POST', $postData);

        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        //assertJson($this->getResponse());
    }

    /**
     * Creates new category.
     *
     * @param $categoryData
     * @return \Categories\Entity\Categories
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

        return $category;
    }

    /**
     * Deletes category.
     *
     * @param \Categories\Entity\Categories $category
     */
    public function removeCategory(\Categories\Entity\Categories $category)
    {
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $category = $objectManager->getRepository('Categories\Entity\Categories')
            ->find($category->getId());
        if ($category) {
            $objectManager->remove($category);
            $objectManager->flush();
        }
    }
}
