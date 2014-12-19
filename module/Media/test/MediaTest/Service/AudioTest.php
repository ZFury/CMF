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
 * Class ImageTest
 * @package MediaTest\Service
 */
class AudioTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError = true;
    protected $audioEntityData = [
        'extension' => 'mp3',
        'type' => 'audio',
    ];
    protected $audioId = null; //it will be converted to 000
    const DIRECTORY_NAME = 'audio';
    private $oldLocation = null;
    private $newLocation = null;

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
        $this->oldLocation = __DIR__ . '/../../testFiles/WhiteAmerica.flac';
        $this->newLocation = __DIR__ . '/../../testFiles/WhiteAmerica.mp3';
    }

    public function testAudioPath()
    {
        $audioService = $this->getApplicationServiceLocator()->get('Media\Service\Audio');
        $audioPath = $audioService->audioPath($this->audioId, $this->audioEntityData['extension']);
        $this->assertRegExp('/[a-zA-z]*\/[a-zA-z]*\/' . self::DIRECTORY_NAME . '*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/', $audioPath);
    }

    public function testAudioPathOnlyPath()
    {
        $audioService = $this->getApplicationServiceLocator()->get('Media\Service\Audio');
        $audioPath = $audioService->audioPath($this->audioId, $this->audioEntityData['extension'], \Media\Service\File::FROM_PUBLIC);
        $this->assertRegExp(
            '/[a-zA-z]*\/' .
            self::DIRECTORY_NAME .
            '*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $audioPath
        );
    }

    public function testPrepareDir()
    {
        $audioService = $this->getApplicationServiceLocator()->get('Media\Service\Audio');
        $audioPath = $audioService->audioPath($this->audioId, $this->audioEntityData['extension']);
        $this->assertTrue($audioService->prepareDir($audioPath));
    }

    public function testMoveAudio()
    {
        $audioService = $this->getApplicationServiceLocator()->get('Media\Service\Audio');
        $audioPath = $audioService->audioPath($this->audioId, $this->audioEntityData['extension']);
        $audio = [
            'name' => 'MakeItWork.mp3',
            'type' => 'audio/mpeg',
            'tmp_name' => __DIR__ . '/../../testFiles/MakeItWork.mp3',
            'error' => '0',
            'size' => '7690060'
        ];
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException');
        $audioService->moveFile($audioPath, $audio);
    }

    public function testAudioConversion()
    {
        $audioService = $this->getApplicationServiceLocator()->get('Media\Service\Audio');
        $this->assertTrue($audioService->executeConversion($this->oldLocation, $this->newLocation));
    }
}
