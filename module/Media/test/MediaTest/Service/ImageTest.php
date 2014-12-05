<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/5/14
 * Time: 11:15 AM
 */

namespace MediaTest\Service;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Media\Service;

/**
 * Class ImageTest
 * @package MediaTest\Service
 */
class ImageTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError;

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
    }


    public function testImgPathOriginal()
    {
        $imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
        $imgPath = $imageService->imgPath($imageService::ORIGINAL, 1, 'jpeg');
        $this->assertRegExp('/[a-zA-z]*\/[a-zA-z]*\/[a-zA-z]*\/[a-zA-z0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/', $imgPath);
    }

    public function testImgPathThumbLil()
    {
        $imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
        $imgPath = $imageService->imgPath($imageService::SMALL_THUMB, 1, 'jpeg');
        $this->assertRegExp('/[a-zA-z]*\/[a-zA-z]*\/[a-zA-z]*\/[a-zA-z0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/', $imgPath);
    }

    public function testImgPathOnlyPath()
    {
        $imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
        $imgPath = $imageService->imgPath($imageService::ORIGINAL, 1, 'jpeg', true);
        $this->assertRegExp('/[a-zA-z]*\/[a-zA-z]*\/[a-zA-z0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/', $imgPath);
    }

    public function testPrepareDir()
    {
        $imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
        $imgPath = $imageService->imgPath($imageService::ORIGINAL, 1, 'jpeg');
        $this->assertTrue($imageService->prepareDir($imgPath));
    }

    public function testMoveImage()
    {
        $imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
        $imgPath = $imageService->imgPath($imageService::ORIGINAL, 1, 'jpeg');
        $image = [
            'name' => 'me.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => __DIR__ . '/../../me.jpg',
            'error' => '0',
            'size' => '29487'
        ];
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException');
        $imageService->moveImage($imgPath, $image);
    }
}