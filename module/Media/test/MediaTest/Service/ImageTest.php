<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/5/14
 * Time: 11:15 AM
 */

namespace MediaTest\Service;

use Media\Form\ImageUpload;
use Starter\Test\Controller\ControllerTestCase;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Media\Service;

/**
 * Class ImageTest
 * @package MediaTest\Service
 */
class ImageTest extends ControllerTestCase
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
     * Set up
     */
    public function setUp()
    {
        $this->setApplicationConfig(include 'config/application.config.php');
        $this->setTraceError(true);
        $this->imageService = $this->getApplicationServiceLocator()->get('Media\Service\Image');
        parent::setUp();

    }

    /**
     * Tests path generator from root
     * @throws \Exception
     */
    public function testImgPathOriginal()
    {
        $imgPath = $this->imageService
            ->imgPath(\Media\Service\Image::ORIGINAL, $this->imageId, $this->imageEntityData['extension']);
        $this->assertRegExp(
            '/[a-zA-z]*\/[a-zA-z]*\/' .
            self::DIRECTORY_NAME .
            '\/[' .
            self::SIZE_ORIGINAL .
            ']*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $imgPath
        );
    }

    /**
     * Tests path generator from public
     * @throws \Exception
     */
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

    /**
     * Tests preparation of a given directory
     * @throws \Exception
     */
    public function testPrepareDirOriginal()
    {
        $imgPath = $this->imageService->imgPath(
            \Media\Service\Image::ORIGINAL,
            $this->imageId,
            $this->imageEntityData['extension']
        );
        $this->assertTrue($this->imageService->prepareDir($imgPath));
    }

    /**
     * Tests image upload
     */
    public function testUploadImage()
    {
        $formMock = $this->getMockBuilder('Media\Form\ImageUpload')
            ->disableOriginalConstructor()
            ->getMock();

        $formMock->expects($this->atLeastOnce())
            ->method('getData')
            ->will($this->returnValue($this->getImageData()));

        $formMock->expects($this->atLeastOnce())
            ->method('getFileType')
            ->will($this->returnValue('image'));

        $filterMock = $this->getMockBuilder('Zend\Filter\File\RenameUpload')
            ->disableOriginalConstructor()
            ->getMock();

        $filterMock->expects($this->atLeastOnce())
            ->method('filter')
            ->will($this->returnValue(true));

        $filterMock->expects($this->atLeastOnce())
            ->method('setTarget')
            ->will($this->returnSelf());

        $doctrineMock = $this->getDoctrineMock();

        $doctrineMock->expects($this->atLeastOnce())
            ->method('persist')
            ->will($this->returnValue(true));

        $doctrineMock->expects($this->atLeastOnce())
            ->method('flush')
            ->will($this->returnValue(true));

        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('Zend\Filter\File\RenameUpload', $filterMock);
        $this->getApplicationServiceLocator()->setService('doctrine.entitymanager.orm_default', $doctrineMock);

        $file = $this->getApplicationServiceLocator()->get('Media\Service\Image')->writeFile($formMock);

        $this->assertNotEmpty($file);
    }

    /**
     * @return array
     */
    public function getImageData()
    {
        return [
            'image' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => __DIR__ . '/../../testFiles/test.jpg',
                'error' => '0',
                'size' => '29487'
            ]
        ];
    }
}
