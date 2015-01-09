<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/9/15
 * Time: 3:13 PM
 */
namespace Install\Service;

use Install\Form\DbConnection;
use Install\Form\Modules;
use Zend\Session\Container;

class Install
{
    const DONE = 'progress-tracker-done';
    const TODO = 'progress-tracker-todo';
    const STEPS_NUMBER = 6;
    const MODULES = 'module/';
    const CHECKED = 'good';
    const UNCHECKED = 'bad';
    const GOOD = true;
    const BAD = false;
    const PHP_VERSION = '5.4.35-1+deb.sury.org~precise+1';
    const GLOBAL_REQUIREMENTS = true;
    const LOCAL_REQUIREMENTS = false;

    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    public function inArrayRecursive($search, $array, $strict = false)
    {
        $result = false;
        if (in_array($search, $array)) {
            return true;
        }//if
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = $this->inArrayRecursive($search, $value);
                if ($result) {
                    return true;
                }//if
            } else {
                return ($strict) ? ($search === $value) : ($search == $value);
            }//if
        }//foreach
        return $result;
    }

    /**
     * @param string $filePath
     * @param string $word
     * @param string $newRow
     * @param null/string $newFilePath
     */
    public function replaceRowInFile($filePath, $word, $newRow, $newFilePath = null)
    {
        $reading = fopen($filePath, 'r');
        $writing = fopen("$filePath.tmp", 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            if (stristr($line, "'$word'")) {
                $line = "$newRow\n";
                $replaced = true;
            }
            fputs($writing, $line);
        }
        fclose($reading);
        fclose($writing);
        if ($replaced) {
            if (null === $newFilePath) {
                rename("$filePath.tmp", "$filePath");
            } else {
                rename("$filePath.tmp", "$newFilePath");
            }

        } else {
            unlink("$filePath.tmp");
        }
    }

    /**
     * @param Modules $modulesForm
     */
    public function hideModules(Modules $modulesForm)
    {
        $modules = $modulesForm->getData();
        for ($i=0; $i<count($modules); $i++) {
            if (Install::UNCHECKED == array_values($modules)[$i]) {
                $reading = fopen('config/application.config.php', 'r');
                $writing = fopen('config/application.config.tmp', 'w');
                $replaced = false;
                while (!feof($reading)) {
                    $line = fgets($reading);
                    if (stristr($line, array_keys($modules)[$i])) {
                        $line = "//". array_keys($modules)[$i] . "\n";
                        $replaced = true;
                    }
                    fputs($writing, $line);
                }
                fclose($reading);
                fclose($writing);
                if ($replaced) {
                    rename('config/application.config.tmp', 'config/application.config.php');
                } else {
                    unlink('config/application.config.tmp');
                }
                rename(Install::MODULES . array_keys($modules)[$i], Install::MODULES . '.' . array_keys($modules)[$i]);
            }
        }
    }

    public function createDbConfig(DbConnection $dbForm)
    {
        $user = $dbForm->getData()['user'];
        $password = $dbForm->getData()['password'];
        $dbname=$dbForm->getData()['dbname'];
        $host=$dbForm->getData()['host'];
        $port=$dbForm->getData()['port'];
        $content = "return ['doctrine' =>['connection' => ['orm_default' => [
                    'driverClass' => 'Doctrine\\DBAL\\Driver\\PDOMySql\\Driver',
                    'params' => [
                        'host'     => '$host',
                        'port'     => '$port',
                        'user'     => '$user',
                        'password' => '$password',
                        'dbname'   => '$dbname',
                    ],
                    'doctrine_type_mappings' => ['enum' => 'string'],
                    ]]]];";
        $fp = fopen("config/autoload/doctrine.locaaaaal.php", "w+");
        fwrite($fp, $content);
        fclose($fp);
    }

    public function checkDbConnection(DbConnection $dbForm)
    {

        //check db connection
        //FIXME: try to do it with zend db adapter or doctrine, but not native PDO
//                $adapter = new Adapter([
//                    'driver' => 'pdo',
//                    'database' => $dbForm->getData()['dbname'],
//                    'username' => $dbForm->getData()['user'],
//                    'password' => $dbForm->getData()['password'],
//                    'host' => $dbForm->getData()['host'],
//                    'port' => $dbForm->getData()['port']
//                ]);
//                $isConnected = $adapter->getDriver()->getConnection()->isConnected();
        $dbname=$dbForm->getData()['dbname'];
        $host=$dbForm->getData()['host'];
        $port=$dbForm->getData()['port'];
        $dsn = "mysql:dbname=$dbname;host=$host;port=$port";
        $user = $dbForm->getData()['user'];
        $password = $dbForm->getData()['password'];
        $dbh = new \PDO($dsn, $user, $password);
    }

    public function checkProgress()
    {
        $session = new Container('progress_tracker');
        $doneSteps = [];
        $steps = $this->getSteps();
        for ($i=0; $i<Install::STEPS_NUMBER; $i++) {
            if ($session->offsetExists($steps[$i])) {
                array_push($doneSteps, [ $steps[$i] => $session->offsetGet($steps[$i])]);
            } else {
                $session->offsetSet($steps[$i], Install::TODO);
                array_push($doneSteps, [ $steps[$i] => $session->offsetGet($steps[$i])]);
            }
        }

        return $doneSteps;
    }

    public static function getSteps()
    {
        return [ 'global_requirements', 'db', 'modules', 'modules_requirements', 'mail', 'finish' ];
    }

    public function checkFiles($global = self::LOCAL_REQUIREMENTS)
    {
        $checkedDirectories = [];
        $checkedFiles = [];
        if (true === $global) {
            $uncheckedFiles = $this->sm->get('Config')['installation']['files-to-check-global'];
        } else {
            $uncheckedFiles = $this->sm->get('Config')['installation']['files-to-check'];
        }


        for ($i=0; $i<count($uncheckedFiles); $i++) {
            $fileName = array_keys($uncheckedFiles[$i]);
            $fileName = array_shift($fileName);
            $filePath = array_values($uncheckedFiles[$i]);
            $filePath = array_shift($filePath);
            $message = "$fileName which path is '$filePath' ";
            if (file_exists($filePath) && is_writable($filePath)) {
                $message .= 'exists. And is writable!';
                if (is_dir($filePath)) {
                    array_push($checkedDirectories, [$fileName => ['message' => $message, 'status' => Install::GOOD]]);
                } else {
                    array_push($checkedFiles, [$fileName => ['message' => $message, 'status' => Install::GOOD]]);
                }
            } else {
                if (true === $global && 'install' == $fileName) {
                    if (is_executable($filePath)) {
                        $message .= 'is executable. Everything is ok!';
                        array_push($checkedFiles, [$fileName => ['message' => $message, 'status' => Install::GOOD]]);
                    } else {
                        $message .= '  install.sh must be executable to continue installation!';
                        array_push($checkedFiles, [$fileName => ['message' => $message, 'status' => Install::BAD]]);
                    }
                } else {
                    $message .= 'does not exist or is not writable. Please, make it writable or create!';
                    if (is_dir($filePath)) {
                        array_push($checkedDirectories, [$fileName => ['message' => $message, 'status' => Install::BAD]]);
                    } else {
                        array_push($checkedFiles, [$fileName => ['message' => $message, 'status' => Install::BAD]]);
                    }
                }
            }
        }

        return ['checkedFiles' => $checkedFiles, 'checkedDirectories' => $checkedDirectories];
    }

    public function checkTools()
    {
        $checkedTools = [];
        $uncheckedTools = $this->sm->get('Config')['installation']['tools-to-check'];

        for ($i=0; $i<count($uncheckedTools); $i++) {
            $toolName = array_keys($uncheckedTools[$i]);
            $toolName = array_shift($toolName);
            $versionCommand = array_values($uncheckedTools[$i]);
            $versionCommand = array_shift($versionCommand);
            $message = "$toolName which version command is '$versionCommand' ";

            $output = [];
            exec($versionCommand, $output, $return);
            if (isset($return) && 0 === $return) {
                $message .= "exists";
                array_push($checkedTools, [$toolName => ['message' => $message, 'status' => Install::GOOD]]);
            } else {
                $message .= "doesn't exist";
                array_push($checkedTools, [$toolName => ['message' => $message, 'status' => Install::BAD]]);
            }
        }
        return $checkedTools;
    }
}
