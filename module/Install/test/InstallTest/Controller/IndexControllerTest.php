<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/14/15
 * Time: 12:52 PM
 */
namespace InstallTest\Controller;

use Install\Service\Install;
use Zend\Http\Request;
use Zend\Session\Container;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
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
        //hide an Install module
        $reading = fopen('config/application.config.php', 'r');
        $writing = fopen("config/application.config.php.tmp", 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            if (stristr($line, "'Install'")) {
                $line = "//'Install'\n";
                $replaced = true;
            }
            fputs($writing, $line);
        }
        fclose($reading);
        fclose($writing);
        if ($replaced) {
            rename("config/application.config.php.tmp", "config/application.config.php");
        } else {
            unlink("config/application.config.php.tmp");
        }
    }

    /**
     * Set up
     */
    public function setUp()
    {
        //add Install module to an application.config.php
        $reading = fopen('config/application.config.php', 'r');
        $writing = fopen("config/application.config.php.tmp", 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            if (stristr($line, "//'Install'")) {
                $line = "'Install'\n";
                $replaced = true;
            }
            fputs($writing, $line);
        }
        fclose($reading);
        fclose($writing);
        if ($replaced) {
            rename("config/application.config.php.tmp", "config/application.config.php");
        } else {
            unlink("config/application.config.php.tmp");
        }


        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        $this->setTraceError(true);
        parent::setUp();
    }

    public function testGlobalRequirementsAction()
    {
        $this->dispatch('/install/index/global-requirements');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));
        $this->assertActionName('global-requirements');
        $this->assertResponseStatusCode(200);
        //assert that action is globalRequirements

    }

    public function testRedirectionToCurrentStep()
    {
        $this->dispatch('/install/index/database');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));
        $this->assertRedirectTo('/install/index/global-requirements');
        $this->assertResponseStatusCode(302);
    }

    public function testSubmitGlobalRequirementsAction()
    {
        $this->dispatch('/install/index/global-requirements', Request::METHOD_POST);
        $this->assertRedirectTo('/install/index/database');
        $this->assertResponseStatusCode(302);
    }

    public function testDatabaseAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('global-requirements', Install::DONE);
        $this->dispatch('/install/index/database');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));
        $this->assertActionName('database');
        $this->assertResponseStatusCode(200);
    }

//    public function testSubmitDatabaseAction()
//    {
//        $sessionProgress = new Container('progress_tracker');
//        $sessionProgress->offsetSet('global-requirements', Install::DONE);
//        $this->dispatch('/install/index/database', Request::METHOD_POST, $this->getDbParams());
//        $this->assertRedirectTo('/install/index/mail');
//        $this->assertActionName('database');
//        $this->assertResponseStatusCode(302);
//    }

    public function testMailAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('database', Install::DONE);
        $this->dispatch('/install/index/mail');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));

        $this->assertActionName('mail');
        $this->assertResponseStatusCode(200);
    }

//    public function testSubmitMailAction()
//    {
//        $sessionProgress = new Container('progress_tracker');
//        $sessionProgress->offsetSet('database', Install::DONE);
//        $this->dispatch('/install/index/mail', Request::METHOD_POST, $this->getMailParams());
//        $this->assertRedirectTo('/install/index/modules');
//        $this->assertActionName('mail');
//        $this->assertResponseStatusCode(302);
//    }

    public function testModulesAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('mail', Install::DONE);
        $this->dispatch('/install/index/modules');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));
        $this->assertActionName('modules');
        $this->assertResponseStatusCode(200);
    }

    public function testSubmitModulesAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('mail', Install::DONE);
        $this->dispatch('/install/index/modules', Request::METHOD_POST, $this->getModulesParams());
        $this->assertRedirectTo('/install/index/modules-requirements');
        $this->assertActionName('modules');
        $this->assertResponseStatusCode(302);
    }

    public function testModulesRequirementsAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules', Install::DONE);
        $this->dispatch('/install/index/modules-requirements');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));
        $this->assertActionName('modules-requirements');
        $this->assertResponseStatusCode(200);
    }

    public function testSubmitModulesRequirementsAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules', Install::DONE);
        $this->dispatch('/install/index/modules-requirements', Request::METHOD_POST);
        $this->assertRedirectTo('/install/index/finish');
        $this->assertActionName('modules-requirements');
        $this->assertResponseStatusCode(302);
    }

    public function testFinishAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules-requirements', Install::DONE);
        $this->dispatch('/install/index/finish');
        $this->assertEquals('install', $this->getRouteMatch()->getParam('module'));
        $this->assertEquals('Install\Controller\Index', $this->getRouteMatch()->getParam('controller'));
        $this->assertActionName('finish');
        $this->assertResponseStatusCode(302);
    }

    public function getRouteMatch()
    {
        return $this->getApplicationServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
    }

    public function getDbParams()
    {
        return [
          'host' => 'localhost',
          'port' => '3306',
          'user' => 'zfs_user',
          'password' => 'zfs_user',
          'dbname' => 'zf_starter_test'
        ];
    }

    public function getMailParams()
    {
        return [
            'host' => 'localhost',
            'port' => '2525',
            'header' => [
                [
                    'header-name' => 'TESTHEADER',
                    'header-value' => 'value1'
                ],
                [
                    'header-name' => 'TESTHEADER2',
                    'header-value' => 'value2'
                ]
            ],
            'emails' => [
                ['emails' => 'test3@test3.com'],
                ['emails' => 'test4@test4.com'],
                ['emails' => 'test5@test5.com']
            ],
            'from' => [
                ['from' => 'test@test.com'],
                ['from' => 'test2@test2.com']
            ]
        ];
    }

    public function getModulesParams()
    {
        return [
            'Categories' => 'good',
            'Comment' => 'good',
            'Mail' => 'good',
            'Options' => 'good',
            'Pages' => 'good',
        ];
    }
}
