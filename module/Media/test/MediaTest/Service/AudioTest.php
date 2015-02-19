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
 * Class AudioTest
 * @package MediaTest\Service
 */
class AudioTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError = true;

    /**
     * @var array
     */
    protected $audioEntityData = [
        'extension' => 'mp3',
        'type' => 'audio',
    ];
    protected $audioId = null; //it will be converted to 000
    const DIRECTORY_NAME = 'audio';
    private $oldLocation = null;
    private $newLocation = null;

    /**
     * @var \Media\Service\Audio
     */
    private $audioService;

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
        $this->oldLocation = __DIR__ . '/../../testFiles/test.aac';
        $this->newLocation = __DIR__ . '/../../testFiles/test.mp3';
        $this->audioService = $this->getApplicationServiceLocator()->get('Media\Service\Audio');
    }

    /**
     * Tests path generator from root
     * @throws \Exception
     */
    public function testAudioPath()
    {
        $audioPath = $this->audioService->audioPath($this->audioId, $this->audioEntityData['extension']);
        $this->assertRegExp(
            '/[a-zA-z]*\/[a-zA-z]*\/' .
            self::DIRECTORY_NAME .
            '*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $audioPath
        );
    }

    /**
     * Tests path generator from public
     * @throws \Exception
     */
    public function testAudioPathOnlyPath()
    {
        $audioPath = $this->audioService
            ->audioPath($this->audioId, $this->audioEntityData['extension'], \Media\Service\File::FROM_PUBLIC);
        $this->assertRegExp(
            '/[a-zA-z]*\/' .
            self::DIRECTORY_NAME .
            '*\/[0-9]*\/[0-9]*\/[0-9]*\/[0-9]*\.[a-zA-Z]*/',
            $audioPath
        );
    }

    /**
     * Tests path generator from public
     * @throws \Exception
     */
    public function testPrepareDir()
    {
        $audioPath = $this->audioService->audioPath($this->audioId, $this->audioEntityData['extension']);
        $this->assertTrue($this->audioService->prepareDir($audioPath));
    }

//    /**
//     * Tests audio conversion to mp3
//     */
//    public function testAudioConversion()
//    {
//        $this->assertTrue($this->audioService->executeConversion($this->oldLocation, $this->newLocation));
//    }
}
