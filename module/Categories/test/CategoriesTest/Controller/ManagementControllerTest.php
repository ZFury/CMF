<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 28.11.14
 * Time: 13:06
 */

namespace CategoriesTest\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Http\Response;
use Zend\Stdlib;
use Fury\Test\Controller\ControllerTestCase;

/**
 * Class ManagementControllerTest
 * @package CategoriesTest\Controller
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
        /**
         * @var \Categories\Entity\Categories $category
         */
        $category = $this->createCategory($this->categoryData);

        $postData = array(
            'name' => 'edited',
            'alias' => $category->getAlias(),
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

        $subCategoryData2 = [
            'name' => 'default2',
            'alias' => 'default2',
            'order' => '4',
            'parentId' => $category1->getId(),
        ];
        $category2 = $this->createCategory($subCategoryData2);

        $json = json_encode(
            [['item_id' => null, "parent_id" => 'none', "depth" => 0, "left" => 1, "right" => 4],
                ['item_id' => $category2->getId(),
                    "parent_id" => null,
                    "depth" => 1,
                    "left" => 2,
                    "right" => 3,
                    "order" => 1
                ],
            ]
        );
        $postData = [
            'tree' => $json,
            'treeParent' => 1,
        ];

        $this->dispatch('/categories/management/order', 'POST', $postData);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $this->assertJson($this->getResponse()->getContent());
    }

    /**
     * Creates new category.
     *
     * @param  $categoryData
     * @return \Categories\Entity\Categories
     */
    public function createCategory($categoryData)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $objectManager
         */
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        //        $category = $this->getApplicationServiceLocator()->get('Categories\Entity\Categories');
        $category = new \Categories\Entity\Categories();
        $objectManager->getConnection()->beginTransaction();
        $hydrator = new DoctrineHydrator($objectManager);
        $hydrator->hydrate($categoryData, $category);
        $objectManager->persist($category);
        $objectManager->flush();
        $objectManager->getConnection()->commit();
        $objectManager->clear();

        return $category;
    }

    /**
     * Deletes category.
     *
     * @param \Categories\Entity\Categories $detachedEntity
     */
    public function removeCategory(\Categories\Entity\Categories $detachedEntity)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $objectManager
         */
        $objectManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $category = $objectManager->merge($detachedEntity);
        $objectManager->remove($category);
        $objectManager->flush();
    }
}
