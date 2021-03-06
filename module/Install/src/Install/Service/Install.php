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
    const PHP_VERSION = '5.4.0';
    const GLOBAL_REQUIREMENTS = true;
    const LOCAL_REQUIREMENTS = false;

    protected $sm;

    /**
     * @param $sm
     */
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
            } else {
                $this->replaceRowInFile('config/application.config.php', "//'$module'", "'$module',");
            }
        }
    }

    /**
     * @param DbConnection $dbForm
     */
    public function createDbConfig(DbConnection $dbForm)
    {
        copy('config/autoload/doctrine.local.php.dist', 'config/autoload/doctrine.local.php');

        $user = $dbForm->getData()['user'];
        $password = $dbForm->getData()['password'];
        $dbname=$dbForm->getData()['dbname'];
        $host=$dbForm->getData()['host'];
        $port=$dbForm->getData()['port'];
        $content = "<?php
            return ['doctrine' =>['connection' => ['orm_default' => [
                'driverClass' => 'Doctrine\\DBAL\\Driver\\PDOMySql\\Driver',
                'params' => [
                    'host'     => " . '"' . $host       . '"' . ",
                    'port'     => " . '"' . $port       . '"' . ",
                    'user'     => " . '"' . $user       . '"' . ",
                    'password' => " . '"' . $password   . '"' . ",
                    'dbname'   => " . '"' . $dbname     . '"' . ",
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
    public function checkExtensions($global = self::LOCAL_REQUIREMENTS)
    {
        $checkedExtenstions = [];
        $uncheckedExtensions = null;

        if (self::GLOBAL_REQUIREMENTS === $global) {
            $uncheckedExtensions = $this->sm->get('Config')['installation']['extensions-to-check-global'];
        } else {
            if (array_key_exists('extensions-to-check', $this->sm->get('Config')['installation'])) {
                $uncheckedExtensions = $this->sm->get('Config')['installation']['extensions-to-check'];
            }
        }

        if (null !== $uncheckedExtensions) {
            for ($i=0; $i<count($uncheckedExtensions); $i++) {
                $name = array_keys($uncheckedExtensions[$i]);
                $name = array_shift($name);
                $extension = array_values($uncheckedExtensions[$i]);
                $extension = array_shift($extension);
                $message = "'$name' ";

                if (extension_loaded($extension)) {
                    $message .= "exists!";
                    array_push($checkedExtenstions, [$name => ['message' => $message, 'status' => Install::GOOD]]);
                } else {
                    $message .= "doesn't exist!";
                    array_push($checkedExtenstions, [$name => ['message' => $message, 'status' => Install::BAD]]);
                }
            }
        }
        return $checkedExtenstions;
    }

    /**
     * @param bool $global
     * @return array
     */
    public function checkFiles($global = self::LOCAL_REQUIREMENTS)
    {
        $checkedDirectories = [];
        $checkedFiles = [];
        if (self::GLOBAL_REQUIREMENTS === $global) {
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
                        $whereToPush = &$checkedDirectories;
                    } else {
                        $whereToPush = &$checkedFiles;
                    }
                    if (is_writable($filePath)) {
                        $message = "'$filePath' exists and is writable!";
                        $status = Install::GOOD;
                    } else {
                        $message = "'$filePath' is not writable.";
                        $status = Install::BAD;
                    }
                } else {
                    $message = "'$filePath' does not exist.";
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
    public function checkTools($global = self::LOCAL_REQUIREMENTS)
    {
        $checkedTools = [];
        $uncheckedTools = null;

        if (self::GLOBAL_REQUIREMENTS === $global) {
            $uncheckedTools = $this->sm->get('Config')['installation']['tools-to-check-global'];
        } else {
            if (array_key_exists('tools-to-check', $this->sm->get('Config')['installation'])) {
                $uncheckedTools = $this->sm->get('Config')['installation']['tools-to-check'];
            }
        }

        if (null !== $uncheckedTools) {
            for ($i=0; $i<count($uncheckedTools); $i++) {
                $toolName = array_keys($uncheckedTools[$i]);
                $toolName = array_shift($toolName);
                $versionCommand = array_values($uncheckedTools[$i]);
                $versionCommand = array_shift($versionCommand);
                $message = "'$toolName' ";
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

        $from = ['admin@zfury.com'];
        $host = '';
        $port = '';
        $headers = '';

        for ($i=0; $i<count($mailForm->getData()); $i++) {
            $paramName = array_keys($mailForm->getData())[$i];
            $paramValue = array_values($mailForm->getData())[$i];
            switch ($paramName) {
                case 'from':
                    $emailsArray = [];
                    for ($j = 0; $j < count($paramValue); $j++) {
                        $value = array_values($paramValue[$j]);
                        $currentEmail = array_shift($value);
                        if (!$currentEmail) {
                            break;
                        }
                        if ('emails' == $paramName) {
                            $paramName = strtoupper($paramName);
                        }
                        array_push($emailsArray, "'$currentEmail'");
                    }
                    $from = implode(',', $emailsArray);
                    break;
                case 'header':
                    for ($j = 0; $j < count($paramValue); $j++) {
                        $headerName = strtoupper($paramValue[$j]['header-name']);
                        $headerValue = $paramValue[$j]['header-value'];
                        if (!$headerName && !$headerValue) {
                            break;
                        }
                        $newRow = "'$headerName'=>'$headerValue',";
                        $headers .= $newRow;
                    }
                    break;
                case 'host':
                    $host = $paramValue;
                    break;
                case 'port':
                    $port = $paramValue;
                    break;
                default:
                    break;
            }
        }

        $content = "<?php
        return array(
                'mail' => array(
                    'transport' => array(
                        'options' => array(
                            'host'              => '$host',
                            'port'              => '$port',
                        ),
                    ),
                    'message' => array(
                        'headers' => array(
                            $headers
                        ),
                        'from' => [$from]
                    )
                ),
                // set true if module mail exists
                'mailOptions' => array(
                    'useModuleMail' => false
                ),
        );";

        $config = fopen("config/autoload/mail.local.php", "w");
        fwrite($config, $content);
        fclose($config);
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
     * @return string
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
