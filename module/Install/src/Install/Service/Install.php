<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/9/15
 * Time: 3:13 PM
 */
namespace Install\Service;

use Install\Form\DbConnection;
use Install\Form\MailConfig;
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
     * @param null $newFilePath
     * @param bool $addAfter
     */
    public function replaceRowInFile($filePath, $word, $newRow, $newFilePath = null, $addAfter = false)
    {
        $reading = fopen($filePath, 'r');
        $writing = fopen("$filePath.tmp", 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            if ((true === $addAfter && !stristr($line, $newRow)) || false === $addAfter) {
                if (stristr($line, "$word")) {
                    if (false === $addAfter) {
                        $line = "$newRow\n";
                    } else {
                        $line = "$line\n$newRow\n";
                    }
                    $replaced = true;

                }fputs($writing, $line);
            }
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
            $module = array_keys($modules)[$i];
            if (Install::UNCHECKED == array_values($modules)[$i]) {
                $this->replaceRowInFile('config/application.config.php', $module, "//$module\n");
                if (file_exists(Install::MODULES . $module)) {
                    rename(Install::MODULES . $module, Install::MODULES . ".$module");
                }
            } else {
                $this->replaceRowInFile('config/application.config.php', "//$module", "'$module',\n");
                if (file_exists(Install::MODULES . ".$module")) {
                    rename(Install::MODULES . ".$module", Install::MODULES . $module);
                }
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
        $content = "<?php return ['doctrine' =>['connection' => ['orm_default' => [
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
        $config = fopen("config/autoload/doctrine.local.php", "w+");
        fwrite($config, $content);
        fclose($config);
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

    public function checkPreviousStep()
    {
        $session = new Container('progress_tracker');
        $previousStep = $this->getSteps()[array_search($session->offsetGet('current_step'), $this->getSteps())-1];
        switch ($previousStep) {
            case 'global_requirements':
                $action = 'global-requirements';
                break;
            case 'db':
                $action = 'database';
                break;
            case 'modules_requirements':
                $action = 'modules-requirements';
                break;
            default:
                $action = $previousStep;
                break;
        }

        if ($session->offsetExists($previousStep) && $session->offsetGet($previousStep) == self::DONE) {
            return null;
        } else {
            return $action;
        }
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
        return [ 'global_requirements', 'db', 'mail', 'modules', 'modules_requirements', 'finish' ];
    }

    public function checkFiles($global = self::LOCAL_REQUIREMENTS)
    {
        $checkedDirectories = [];
        $checkedFiles = [];
        if (true === $global) {
            $uncheckedFiles = $this->sm->get('Config')['installation']['files-to-check-global'];
        } else {
            $uncheckedFiles = null;
            if (array_key_exists('files-to-check', $this->sm->get('Config')['installation'])) {
                $uncheckedFiles = $this->sm->get('Config')['installation']['files-to-check'];
            }
        }

        if (null !== $uncheckedFiles) {
            for ($i=0; $i<count($uncheckedFiles); $i++) {
                $fileName = array_keys($uncheckedFiles[$i]);
                $fileName = array_shift($fileName);
                $filePath = array_values($uncheckedFiles[$i]);
                $filePath = array_shift($filePath);

                if (file_exists($filePath) && is_writable($filePath)) {
                    if (is_dir($filePath)) {
                        $message="Directory '$fileName' which path is '$filePath' exists and is writable!";
                        array_push($checkedDirectories, [$fileName => ['message' => $message, 'status' => Install::GOOD, 'path' => $filePath]]);
                    } else {
                        $message="File '$fileName' which path is '$filePath' exists and is writable!";
                        array_push($checkedFiles, [$fileName => ['message' => $message, 'status' => Install::GOOD, 'path' => $filePath]]);
                    }
                } else {
                    if (is_dir($filePath)) {
                        $message = "Directory '$fileName' which path is '$filePath' does not exist or is not writable." .
                            "Please, make it writable or create!";
                        array_push($checkedDirectories, [$fileName => ['message' => $message, 'status' => Install::BAD, 'path' => $filePath]]);
                    } else {
                        $message = "File '$fileName' which path is '$filePath' does not exist or is not writable." .
                            "Please, make it writable or create!";
                        array_push($checkedFiles, [$fileName => ['message' => $message, 'status' => Install::BAD, 'path' => $filePath]]);
                    }
                }
            }
        }

        return ['checkedFiles' => $checkedFiles, 'checkedDirectories' => $checkedDirectories];
    }

    public function checkTools()
    {
        $checkedTools = [];
        $uncheckedTools = null;
        if (array_key_exists('tools-to-check', $this->sm->get('Config')['installation'])) {
            $uncheckedTools = $this->sm->get('Config')['installation']['tools-to-check'];
        }

        if (null !== $uncheckedTools) {
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
        }
        return $checkedTools;
    }

    public function createMailConfig(MailConfig $mailForm)
    {
        for ($i=0; $i<count($mailForm->getData()); $i++) {
            $paramName = array_keys($mailForm->getData())[$i];
            $paramValue = array_values($mailForm->getData())[$i];
            if ('emails' == $paramName || 'from' == $paramName) {
                $emailsArray = [];
                for ($j = 0; $j < count($paramValue); $j++) {
                    $value = array_values($paramValue[$j]);
                    $currentEmail = array_shift($value);
                    if ('emails' == $paramName) {
                        $paramName = strtoupper($paramName);
                    }
                    array_push($emailsArray, "'$currentEmail'");
                }
                $emails = implode(',', $emailsArray);
                $this->replaceRowInFile(
                    'config/autoload/mail.local.php',
                    "'$paramName'",
                    "'$paramName'=>[$emails],"
                );
            } else {
                if ('header' == $paramName) {
                    for ($j = 0; $j < count($paramValue); $j++) {
                        $headerName = strtoupper($paramValue[$j]['header-name']);
                        $headerValue = $paramValue[$j]['header-value'];
                        $newRow = "'$headerName'=>'$headerValue',";

                        if ('PROJECT' === $headerName) {
                            $this->replaceRowInFile(
                                'config/autoload/mail.local.php',
                                "'$headerName'",
                                $newRow,
                                'config/autoload/mail.local.php'
                            );
                        } else {
                            $this->replaceRowInFile(
                                'config/autoload/mail.local.php',
                                "'EMAILS'",
                                $newRow,
                                'config/autoload/mail.local.php',
                                true
                            );
                        }
                    }
                } else {
                    $newRow = "'$paramName'=>'$paramValue',";
                    $this->replaceRowInFile(
                        'config/autoload/mail.local.php',
                        "'$paramName'",
                        $newRow,
                        'config/autoload/mail.local.php'
                    );
                }
            }
        }
    }
}
