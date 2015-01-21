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
use Zend\Db\Adapter\Adapter;
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

    /**
     * @param $search
     * @param $array
     * @param bool $strict
     * @return bool
     */
    public function inArrayRecursive($search, $array, $strict = false)
    {
        $result = false;
        if (in_array($search, $array)) {
            return true;
        }
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = $this->inArrayRecursive($search, $value);
                if ($result) {
                    return true;
                }
            } else {
                return ($strict) ? ($search === $value) : ($search == $value);
            }
        }
        return $result;
    }

    /**
     * @param $filePath
     * @param $word
     * @param $newRow
     * @param null $options
     */
    public function replaceRowInFile($filePath, $word, $newRow, $options = null)
    {
        $newFilePath = null;
        $afterLine = false;
        if (is_array($options)) {
            if (array_key_exists('newFilePath', $options)) {
                $newFilePath = $options['newFilePath'];
            }
            if (array_key_exists('afterLine', $options)) {
                $afterLine = $options['afterLine'];
            }
        }
        $reading = file_get_contents($filePath);
        if (false === $afterLine) {
            $replaced = preg_replace("#$word.*\n#", "$newRow\n", $reading);
        } else {
            $replaced = preg_replace("#$word.*\\s+\\K.*#", "$newRow\n", $reading);
        }
        if (null === $newFilePath) {
            file_put_contents($filePath, $replaced);
        } else {
            file_put_contents($newFilePath, $replaced);
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
                $this->replaceRowInFile('config/application.config.php', "'$module'", "//'$module'");
                if (file_exists(Install::MODULES . $module)) {
                    rename(Install::MODULES . $module, Install::MODULES . ".$module");
                }
            } else {
                $this->replaceRowInFile('config/application.config.php', "//'$module'", "'$module',");
                if (file_exists(Install::MODULES . ".$module")) {
                    rename(Install::MODULES . ".$module", Install::MODULES . $module);
                }
            }
        }
    }

    /**
     * @param DbConnection $dbForm
     */
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
        $config = fopen("config/autoload/doctrine.local.php", "w");
        fwrite($config, $content);
        fclose($config);
    }

    /**
     * @param DbConnection $dbForm
     */
    public function checkDbConnection(DbConnection $dbForm)
    {
        $dbname=$dbForm->getData()['dbname'];
        $host=$dbForm->getData()['host'];
        $port=$dbForm->getData()['port'];
        $dsn = "mysql:dbname=$dbname;host=$host;port=$port";
        $user = $dbForm->getData()['user'];
        $password = $dbForm->getData()['password'];
        $connection = new \PDO($dsn, $user, $password);
    }

    /**
     * @return array
     */
    public function checkProgress()
    {
        $session = new Container('progress_tracker');
        $doneSteps = [];
        foreach ($this->getSteps() as $step) {
            $doneSteps[$step] = $session->offsetGet($step) ? $session->offsetGet($step) : Install::TODO;
        }

        return $doneSteps;
    }

    /**
     * @param bool $global
     * @return array
     */
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

                if (file_exists($filePath)) {
                    if (is_dir($filePath)) {
                        $message = 'Directory ';
                        $whereToPush = &$checkedDirectories;
                    } else {
                        $message = 'File ';
                        $whereToPush = &$checkedFiles;
                    }
                    if (is_writable($filePath)) {
                        $message .= "'$fileName' which path is '$filePath' exists and is writable!";
                        $status = Install::GOOD;
                    } else {
                        $message .= "'$fileName' which path is '$filePath' is not writable."
                            . "Please, make it writable!";
                        $status = Install::BAD;
                    }
                } else {
                    $message = "'$fileName' which path is '$filePath' does not exist."
                        . "Please, create it!";
                    $status = Install::BAD;
                }
                array_push($whereToPush, [$fileName => [
                            'message' => $message,
                            'status' => $status,
                            'path' => $filePath]
                ]);
            }
        }

        return ['checkedFiles' => $checkedFiles, 'checkedDirectories' => $checkedDirectories];
    }

    /**
     * @return array
     */
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
                $message = "Tool '$toolName' which version command is '$versionCommand' ";
                $output = [];
                exec($versionCommand, $output, $return);
                if (isset($return) && 0 === $return) {
                    $message .= "exists!";
                    array_push($checkedTools, [$toolName => ['message' => $message, 'status' => Install::GOOD]]);
                } else {
                    $message .= "doesn't exist!";
                    array_push($checkedTools, [$toolName => ['message' => $message, 'status' => Install::BAD]]);
                }
            }
        }
        return $checkedTools;
    }

    /**
     * @param MailConfig $mailForm
     */
    public function createMailConfig(MailConfig $mailForm)
    {
        copy('config/autoload/mail.local.php.dist', 'config/autoload/mail.local.php');

        for ($i=0; $i<count($mailForm->getData()); $i++) {
            $paramName = array_keys($mailForm->getData())[$i];
            $paramValue = array_values($mailForm->getData())[$i];
            switch ($paramName) {
                case 'from':
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
                    break;
                case 'header':
                    for ($j = 0; $j < count($paramValue); $j++) {
                        $headerName = strtoupper($paramValue[$j]['header-name']);
                        $headerValue = $paramValue[$j]['header-value'];
                        $newRow = "'$headerName'=>'$headerValue',";
                        if ('PROJECT' === $headerName) {
                            $this->replaceRowInFile(
                                'config/autoload/mail.local.php',
                                "'$headerName'",
                                $newRow
                            );
                        } else {
                            $this->replaceRowInFile(
                                'config/autoload/mail.local.php',
                                "'EMAILS'", //this means, that a new row will be inserted after matched one
                                $newRow,
                                ['afterLine' => true]
                            );
                        }
                    }
                    break;
                case 'emails':
                    $emails = "'";
                    for ($j = 0; $j < count($paramValue); $j++) {
                        $value = array_values($paramValue[$j]);
                        $currentEmail = array_shift($value);
                        if ('emails' == $paramName) {
                            $paramName = strtoupper($paramName);
                        }
                        if (count($paramValue)>1) {
                            $emails .= "$currentEmail,";
                        } else {
                            $emails .= "$currentEmail";
                        }
                    }
                    $this->replaceRowInFile(
                        'config/autoload/mail.local.php',
                        "'$paramName'",
                        "'$paramName'=>$emails',"
                    );
                    break;
                default:
                    $newRow = "'$paramName'=>'$paramValue',";
                    $this->replaceRowInFile(
                        'config/autoload/mail.local.php',
                        "'$paramName'",
                        "$newRow"
                    );
                    break;
            }
        }
    }

    /**
     * @return null|string
     */
    public function checkPreviousStep()
    {
        $session = new Container('progress_tracker');
        $previousStep = $this->getSteps()[array_search($session->offsetGet('current_step'), $this->getSteps())-1];
        if ($session->offsetExists($previousStep) && $session->offsetGet($previousStep) == self::DONE) {
            return null;
        } else {
            return $previousStep;
        }
    }

    /**
     * @return mixed
     */
    public static function getCurrentStep()
    {
        $session = new Container('progress_tracker');
        $currentStep = $session->offsetGet('current_step');
        if (null === $currentStep) {
            $currentStep = 'global-requirements';
        }

        return self::getSteps()[array_search($currentStep, self::getSteps())];
    }

    /**
     * @return array
     */
    public static function getSteps()
    {
        return ['global-requirements', 'database', 'mail', 'modules', 'modules-requirements', 'finish'];
    }
}
