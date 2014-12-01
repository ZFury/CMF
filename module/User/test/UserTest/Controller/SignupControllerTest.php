<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 22.05.14
 * Time: 16:43
 */

namespace UserTest\Controller;

use SebastianBergmann\Exporter\Exception;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class SignupControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include './config/application.config.php'
        );

//        $this->setTraceError(true);
        parent::setUp();
    }

    /**
     * Set up database
     */
    public static function setUpBeforeClass()
    {
        exec('./vendor/bin/doctrine-module orm:schema-tool:update --force');
    }

    /**
     * Tear down database
     */
    public static function tearDownAfterClass()
    {
        exec('./vendor/bin/doctrine-module orm:schema-tool:drop --force');
    }

    /**
     */
    public function testIndex()
    {
        $this->dispatch('/user/signup/index');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Signup');
        $this->assertControllerClass('SignupController');
        $this->assertMatchedRouteName('user/default');
    }


    public function testFormIndex()
    {
//        $objectManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getRepository', 'getConnection', 'getClassMetadata', 'persist', 'flush'), array(), '', false)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $this->getApplication()->getServiceManager()->setAllowOverride(true)
//            ->setService('\Doctrine\ORM\EntityManager', $objectManager);

        $this->getRequest()->setMethod('POST');
        $this->dispatch('/user/signup/index');
        $this->assertNotRedirect();
        $this->assertResponseStatusCode(200);

//        $mockEM = $this->getMock('\Doctrine\ORM\EntityManager',
//            array('getRepository', 'getConnection', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
//
//        $this->getApplication()->getServiceManager()->set('Doctrine\ORM\EntityManager', $objectManager);
//        $objectManager->expects($this->once())
//            ->method('getConnection');
//        $objectManager = $this->getMockBuilder($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'))
//            ->disableOriginalConstructor()
//            ->getMock();
//        $objectManager->expects($this->once())
//            ->method('getConnection');
//
//        $this->assertResponseStatusCode(200);
    }
}
