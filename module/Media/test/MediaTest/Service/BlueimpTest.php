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
 * Class BlueimpTest
 * @package MediaTest\Service
 */
class BlueimpTest extends AbstractHttpControllerTestCase
{
    /**
     * @var bool
     */
    protected $traceError=true;

    /**
     * @var \Media\Service\Blueimp
     */
    private $blueimpService;

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
        $this->blueimpService = $this->getApplicationServiceLocator()->get('Media\Service\Blueimp');
    }

    public function testDeleteFileJson()
    {
        $this->assertJson(json_encode($this->blueimpService->deleteFileJson()));
    }

    public function testDisplayUploadedFiles()
    {
        $entityManager = $this->getApplicationServiceLocator()->get('Doctrine\ORM\EntityManager');
        $files = $entityManager->getRepository('Media\Entity\File')->findByType('image');
        $mask = '/mask/mask/';
        $this->assertJson(json_encode($this->blueimpService->displayUploadedFiles($files, $mask)));
    }
}
