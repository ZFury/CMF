<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/18/14
 * Time: 5:08 PM
 */
namespace MediaTest\Service;

use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class ImageTest
 * @package MediaTest\Service
 */
class BlueimpTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError=true;

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
    }

    public function testDeleteFileJson()
    {
        $blueimpService = $this->getApplicationServiceLocator()->get('Media\Service\Blueimp');
        $this->assertJson(json_encode($blueimpService->deleteFileJson(1)));
    }

    public function testdisplayUploadedFiles()
    {
        $entityManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $files = $entityManager->getRepository('Media\Entity\File')->findByType('image');
        $deleteUrls = [];
        foreach ($files as $file) {
            array_push($deleteUrls, ['id' => 0, 'deleteUrl' => 0]);
        }
        $blueimpService = $this->getApplicationServiceLocator()->get('Media\Service\Blueimp');
        $this->assertJson(json_encode($blueimpService->displayUploadedFiles($files, $deleteUrls)));
    }
}
