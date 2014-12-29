<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/5/14
 * Time: 11:15 AM
 */

namespace MediaTest\Service;

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

    protected $imageEntityData = [
        'extension' => 'jpg',
        'type' => 'image',
    ];
    protected $imageId = null; //it will be converted to 000
    const DIRECTORY_NAME = 'images';
    const SIZE_ORIGINAL = 'original';

    /**
     * @var \Media\Service\Image
     */
    private $imageService;
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
        $this->setApplicationConfig(include 'config/application.config.php');
        $this->setTraceError(true);
        parent::setUp();
        $this->imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
    }

    public function testImgPathOriginal()
    {

        $imgPath = $this->imageService->imgPath(\Media\Service\Image::ORIGINAL, $this->imageId, $this->imageEntityData['extension']);
        $this->assertRegExp(
            '/[a-zA-z]*\/[a-zA-z]*\/' .
            self::DIRECTORY_NAME .
            '\/[' .
            self::SIZE_ORIGINAL .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    public function testImgPathThumbLil()
    {
        $imgPath = $this->imageService->imgPath(\Media\Service\Image::SMALL_THUMB, $this->imageId, $this->imageEntityData['extension']);
        $this->assertRegExp(
            '/[a-zA-z]*\/[a-zA-z]*\/' .
            self::DIRECTORY_NAME.
            '\/[' .
            \Media\Service\Image::S_THUMB_WIDTH .
            'x' .
            \Media\Service\Image::S_THUMB_HEIGHT .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    public function testImgPathThumbBig()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::BIG_THUMB,
            $this->imageId,
            $this->imageEntityData['extension']
        );
        $this->assertRegExp(
            '/[a-zA-z]*\/[a-zA-z]*\/' .
            self::DIRECTORY_NAME.
            '\/[' .
            \Media\Service\Image::B_THUMB_WIDTH .
            'x' .
            \Media\Service\Image::B_THUMB_HEIGHT .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    public function testImgPathOnlyPathOriginal()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::ORIGINAL,
            $this->imageId,
            $this->imageEntityData['extension'],
            \Media\Service\File::FROM_PUBLIC
        );
        $this->assertRegExp(
            '/[a-zA-z]*\/' .
            self::DIRECTORY_NAME.
            '\/[' .
            self::SIZE_ORIGINAL .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    public function testImgPathOnlyPathThumbLil()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::SMALL_THUMB,
            $this->imageId,
            $this->imageEntityData['extension'],
            \Media\Service\File::FROM_PUBLIC
        );
        $this->assertRegExp(
            '/[a-zA-z]*\/' .
            self::DIRECTORY_NAME.
            '\/[' .
            \Media\Service\Image::S_THUMB_WIDTH .
            'x' .
            \Media\Service\Image::S_THUMB_HEIGHT .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    public function testImgPathOnlyPathThumbBig()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::BIG_THUMB,
            $this->imageId,
            $this->imageEntityData['extension'],
            \Media\Service\File::FROM_PUBLIC
        );
        $this->assertRegExp(
            '/[a-zA-z]*\/' .
            self::DIRECTORY_NAME.
            '\/[' .
            \Media\Service\Image::B_THUMB_WIDTH .
            'x' .
            \Media\Service\Image::B_THUMB_HEIGHT .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    public function testPrepareDirOriginal()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::ORIGINAL,
            $this->imageId,
            $this->imageEntityData['extension']
        );
        $this->assertTrue($this->imageService->prepareDir($imgPath));
    }

    public function testPrepareDirThumbLil()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::SMALL_THUMB,
            $this->imageId,
            $this->imageEntityData['extension']
        );
        $this->assertTrue($this->imageService->prepareDir($imgPath));
    }

    public function testPrepareDirThumbBig()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::BIG_THUMB,
            $this->imageId,
            $this->imageEntityData['extension']
        );
        $this->assertTrue($this->imageService->prepareDir($imgPath));
    }

    public function testMoveImage()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::ORIGINAL,
            $this->imageId,
            $this->imageEntityData['extension']
        );
        $image = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => __DIR__ . '/../../testFiles/test.jpg',
            'error' => '0',
            'size' => '29487'
        ];
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException');
        $this->imageService->moveFile($imgPath, $image);
    }
}
