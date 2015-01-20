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

/**
 * Class VideoTest
 * @package MediaTest\Service
 */
class VideoTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError;
    protected $videoEntityData = [
        'extension' => 'mp4',
        'type' => 'video',
    ];
    protected $videoId = null; //it will be converted to 000
    const DIRECTORY_NAME = 'video';
    private $oldLocation = null;
    private $newLocation = null;
    /**
     * @var \Media\Service\Video
     */
    private $videoService;
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
        $this->oldLocation = __DIR__ . '/../../testFiles/test.mov';
        $this->newLocation = __DIR__ . '/../../testFiles/test.mp4';
        $this->videoService = $this->getApplicationServiceLocator()->get('Media\Service\Video');
    }

    public function testVideoPath()
    {
        $videoPath = $this->videoService->videoPath($this->videoId, $this->videoEntityData['extension']);
        $this->assertRegExp(
            '/[a-zA-z]*\/[a-zA-z]*\/' .
            self::DIRECTORY_NAME .
            '*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $videoPath
        );
    }

    public function testVideoPathOnlyPath()
    {
        $videoPath = $this->videoService
            ->videoPath($this->videoId, $this->videoEntityData['extension'], \Media\Service\File::FROM_PUBLIC);
        $this->assertRegExp(
            '/[a-zA-z]*\/' .
            self::DIRECTORY_NAME.
            '*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $videoPath
        );
    }

    public function testPrepareDir()
    {
        $videoPath = $this->videoService->videoPath($this->videoId, $this->videoEntityData['extension']);
        $this->assertTrue($this->videoService->prepareDir($videoPath));
    }

    public function testMoveVideo()
    {
        $videoPath = $this->videoService->videoPath($this->videoId, $this->videoEntityData['extension']);
        $video = [
            'name' => 'test.mp4',
            'type' => 'video/mp4',
            'tmp_name' => __DIR__ . '/../../testFiles/test.mp4',
            'error' => '0',
            'size' => '15288220'
        ];
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException');
        $this->videoService->moveFile($videoPath, $video);
    }

    public function testVideoConversion()
    {
        $this->assertTrue($this->videoService->executeConversion($this->oldLocation, $this->newLocation));
    }
}
