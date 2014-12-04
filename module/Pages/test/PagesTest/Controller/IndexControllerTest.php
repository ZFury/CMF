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
 * Class IndexControllerTest
 * @package Pages\Controller
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError = true;

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
    }

    /**
     *  remove page
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
     * test index action
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testIndexAction()
    {
        //create
        $this->createEntity();

        $editPath = '/' . $this->pageData['alias'] . '.html';
        $this->dispatch($editPath);

        $this->assertEquals(200, $this->getResponse()->getStatusCode());
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
    }
}
