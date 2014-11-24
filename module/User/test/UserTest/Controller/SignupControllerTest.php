<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 22.05.14
 * Time: 16:43
 */

namespace ImageTest\Controller;

use SebastianBergmann\Exporter\Exception;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
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
     */
    public function testIndexActionCanBeAccessed()
    {
//        $imageTableMock = $this->getMockBuilder('Image\Model\AlbumTable')
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $imageTableMock->expects($this->once())
//            ->method('select')
//            ->will($this->returnValue(array()));
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Image\Model\AlbumTable', $imageTableMock);
//        $query = $this->getRequest()->getQuery();
//        $query['token'] = 'asdasdasd';
//        $this->setExpectedException('\User\Exception\AuthException');
        $this->dispatch('/user/signup/index');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\User');
        $this->assertControllerClass('SignupController');
        $this->assertMatchedRouteName('user');
    }

//    public function testPortraitActionCanBeAccessed()
//    {
//        $this->dispatch('/image/portrait');
//        $this->assertResponseStatusCode(200);
//
//        $this->assertModuleName('Image');
//        $this->assertControllerName('Image\Controller\Index');
//        $this->assertControllerClass('IndexController');
//        $this->assertMatchedRouteName('image');
//    }
}
