<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace OptionsTest\Controller;

use Options\Controller\ManagementController;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use Zend\Stdlib;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ManagementControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/options');
        $this->assertResponseStatusCode(200);
    }

    public function testCreateActionCanBeAccessed()
    {
        $this->dispatch('/option/create');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
    }

}
