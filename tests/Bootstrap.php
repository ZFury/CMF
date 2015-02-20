<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 2/20/15
 * Time: 6:00 PM
 */
use ModulesTests\ServiceManagerGrabber;

error_reporting(E_ALL | E_STRICT);
$cwd = __DIR__;
chdir(dirname(__DIR__));
$files = array(__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php');
foreach ($files as $file) {
    if (file_exists($file)) {
        $loader = require $file;
        break;
    }
}
if (! isset($loader)) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}
$loader->add("ModulesTests\\", $cwd);
$loader->register();
ServiceManagerGrabber::setServiceConfig(require_once './config/application.config.php');
ob_start();
