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
