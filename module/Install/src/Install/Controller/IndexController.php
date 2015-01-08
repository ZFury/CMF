<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/19/14
 * Time: 4:58 PM
 */

namespace Install\Controller;

use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Install\Form\DbConnection;
use Install\Form\Filter\DbConnectionInputFilter;
use Install\Form\Filter\MailConfigInputFilter;
use Install\Form\Filter\ModulesInputFilter;
use Install\Form\Modules;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\EmailAddress;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Install\Form\MailConfig;

class IndexController extends AbstractActionController
{
    const DONE = 'progress-tracker-done';
    const TODO = 'progress-tracker-todo';
    const STEPS_NUMBER = 6;
    const MODULES = 'module/';
    const CHECKED = 'good';
    const UNCHECKED = 'bad';
    const GOOD = true;
    const BAD = false;

    public function globalRequirementsAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('global_requirements', self::TODO);
        $this->setProgress();

        $uncheckedDirectories = $this->getDirectories();
        $checkedDirectories = [];

        for ($i=0; $i<count($uncheckedDirectories); $i++) {
            $directoryName = array_keys($uncheckedDirectories[$i]);
            $directoryName = array_shift($directoryName);
            $directoryPath = array_values($uncheckedDirectories[$i]);
            $directoryPath = array_shift($directoryPath);
            $message = "Directory $directoryName which path is '$directoryPath' ";
            if (file_exists($directoryPath) && is_writable($directoryPath)) {
                $message .= 'exists. And is writable!';
                array_push($checkedDirectories, [
                   $directoryName => [
                       'message' => $message,
                       'status' => self::GOOD
                   ]
                ]);
            } else {
                $message .= 'does not exist or is not writable. Please, make it writable!';
                array_push($checkedDirectories, [
                    $directoryName => [
                        'message' => $message,
                        'status' => self::BAD
                    ]
                ]);
            }
        }

        return new ViewModel([
            'directories' => $checkedDirectories
        ]);
    }

    public function getDirectories()
    {
        return [
            ['config' => 'config'],
            ['config-autoload' => 'config/autoload']
        ];
    }

    public function databaseAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('db', self::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $dbForm = new DbConnection();
            $dbForm->setInputFilter(new DbConnectionInputFilter($this->getServiceLocator()));
            $dbForm->setData($this->getRequest()->getPost());
            if ($dbForm->isValid()) {
                $sessionForms->offsetSet('dbForm', $dbForm->getData());
                $sessionProgress->offsetSet('db', self::DONE);

                try {
                    $this->checkDbConnection($dbForm);
                    $this->createDbConfig($dbForm);
                    $this->flashMessenger()->addSuccessMessage('Connection established and config file created!');

                    return $this->redirect()->toRoute(
                        'install/default',
                        [
                            'controller' => 'index',
                            'action' => 'mail'
                        ]
                    );
                } catch (\PDOException $e) {
                    $this->flashMessenger()->addErrorMessage('Connection can not be established! ' . $e->getMessage());
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Config file can not be created! ' . $e->getMessage());
                }

                return $this->redirect()->toRoute(
                    'install/default',
                    [
                        'controller' => 'index',
                        'action' => 'database',
                        'dbForm' => $dbForm
                    ]
                );

            } else {
                return  new ViewModel([
                    'dbForm' => $dbForm,
                ]);
            }
        } else {
            $dbForm = new DbConnection();
            if (null !== $sessionForms->offsetGet('dbForm')) {
                $dbForm->setData($sessionForms->offsetGet('dbForm'));
            }

            return new ViewModel([
                'dbForm' => $dbForm,
            ]);
        }
    }

    public function mailAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('mail', self::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $mailForm = new MailConfig();
            $mailForm->setInputFilter(new MailConfigInputFilter($this->getServiceLocator()));
            $mailForm->setData($this->getRequest()->getPost());

            if ($mailForm->isValid()) {
                try {
                    $sessionForms->offsetSet('mailForm', $mailForm->getData());
                    for ($i=0; $i<count($mailForm->getData()); $i++) {
                        $paramName = array_keys($mailForm->getData())[$i];
                        $paramValue = array_values($mailForm->getData())[$i];

                        if ('emails' == $paramName || 'from' == $paramName) {
                            $emailsArray = [];
                            for ($j=0; $j<count($paramValue); $j++) {
                                $value = array_values($paramValue[$j]);
                                $currentEmail = array_shift($value);
                                if ('emails' == $paramName) {
                                    $paramName = strtoupper($paramName);
                                }
                                array_push($emailsArray, "'$currentEmail'");
                            }
                            $emails = implode(',', $emailsArray);
                            $this->replaceRowInFile(
                                'config/autoload/mail.local.php.php.php',
                                $paramName,
                                "'$paramName'=>[$emails],",
                                'config/autoload/mail.local.php.php.php'
                            );
                        } else {
                            if ('project' == $paramName) {
                                $paramName = strtoupper($paramName);
                            }
                            $newRow = "'$paramName'=>'$paramValue',";
                            $this->replaceRowInFile(
                                'config/autoload/mail.local.php.php.php',
                                $paramName,
                                $newRow,
                                'config/autoload/mail.local.php.php.php'
                            );
                        }
                    }
                    $sessionProgress->offsetSet('mail', self::DONE);
                    $this->flashMessenger()->addSuccessMessage('Mail config file created!');

                    return $this->redirect()->toRoute(
                        'install/default',
                        [
                            'controller' => 'index',
                            'action' => 'modules'
                        ]
                    );
                } catch (\Exception $ex) {
                    $this->flashMessenger()->addErrorMessage('Mail config file is not created! ' . $ex->getMessage());
                    return $this->redirect()->toRoute(
                        'install/default',
                        [
                            'controller' => 'index',
                            'action' => 'mail'
                        ]
                    );
                }
            } else {
                return  new ViewModel([
                    'mailForm' => $mailForm,
                ]);
            }
        } else {
            $mailForm = new MailConfig();
            if (null !== $sessionForms->offsetGet('mailForm')) {
                $mailForm->setData($sessionForms->offsetGet('mailForm'));
            }

            return new ViewModel([
                'mailForm' => $mailForm,
            ]);
        }
    }

    public function modulesAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules', self::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $modulesForm = new Modules();
            $modulesForm->setInputFilter(new ModulesInputFilter($this->getServiceLocator()));
            $modulesForm->setData($this->getRequest()->getPost());
            if ($modulesForm->isValid()) {
                $sessionForms->offsetSet('modulesForm', $modulesForm->getData());
                $sessionProgress->offsetSet('modules', self::DONE);

                try {
                    $this->hideModules($modulesForm);
                    $this->flashMessenger()->addSuccessMessage('Unnecessary modules are hidden now!');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Can not hide module! ' . $e->getMessage());
                }

                //TODO: autoLoadConfig

                return $this->redirect()->toRoute(
                    'install/default',
                    [
                        'controller' => 'index',
                        'action' => 'modules-requirements'
                    ]
                );
            } else {
                return  new ViewModel([
                    'modulesForm' => $modulesForm,
                ]);
            }
        } else {
            $modulesForm = new Modules();
            if (null !== $sessionForms->offsetGet('modulesForm')) {
                $modulesForm->setData($sessionForms->offsetGet('modulesForm'));
            }

            return new ViewModel([
                'modulesForm' => $modulesForm
            ]);
        }
    }

    public function modulesRequirementsAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules_requirements', self::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('modules_requirements', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'finish'
                ]
            );
        } else {
            $requirements = $this->getServiceLocator()->get('Config')['requirements'];
            return new ViewModel(['modules_requirements' => $requirements]);
        }
    }

    public function finishAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('finish', self::DONE);
        $this->setProgress();
        $sessionProgress->getManager()->getStorage()->clear('progress_tracker');
        $sessionProgress = new Container('forms');
        $sessionProgress->getManager()->getStorage()->clear('forms');

        return new ViewModel();
    }

    public function checkProgress()
    {
        $session = new Container('progress_tracker');
        $doneSteps = [];
        $steps = $this->getSteps();
        for ($i=0; $i<self::STEPS_NUMBER; $i++) {
            if ($session->offsetExists($steps[$i])) {
                array_push($doneSteps, [ $steps[$i] => $session->offsetGet($steps[$i])]);
            } else {
                $session->offsetSet($steps[$i], self::TODO);
                array_push($doneSteps, [ $steps[$i] => $session->offsetGet($steps[$i])]);
            }
        }

        return $doneSteps;
    }

    public function setProgress()
    {
        $doneSteps = $this->checkProgress();
        $this->layout()->setVariable('done_steps', $doneSteps);
        foreach ($doneSteps as $step) {
            $this->layout()->setVariable(array_keys($step)[0], array_values($step)[0]);
        }

    }

    public static function getSteps()
    {
        return [ 'global_requirements', 'db', 'modules', 'modules_requirements', 'mail', 'finish' ];
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
        $user = 'zfs_user';
        $password = 'zfs_user';
        $dbh = new \PDO($dsn, $user, $password);
    }

    public function createDbConfig(DbConnection $dbForm)
    {
        $user = 'zfs_user';
        $password = 'zfs_user';
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

    /**
     * @param Modules $modulesForm
     */
    public function hideModules(Modules $modulesForm)
    {
        $modules = $modulesForm->getData();
        for ($i=0; $i<count($modules); $i++) {
            if (self::UNCHECKED == array_values($modules)[$i]) {
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
                rename(self::MODULES . array_keys($modules)[$i], self::MODULES . '.' . array_keys($modules)[$i]);
            }
        }
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
}
