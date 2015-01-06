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
    const STEPS_NUMBER = 5;
    const MODULES = 'module/';
    const CHECKED = 'good';
    const UNCHECKED = 'bad';

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
                //validate emails and froms
                //TODO: redirect if custom validation fails and cut off copycode and try to avoid this
                $validator = new EmailAddress();
                $from = [];
                for ($i=0; $i<count($this->getRequest()->getPost('from')); $i++) {
                    if (!$validator->isValid($this->getRequest()->getPost('from')[$i])) {
                        foreach ($validator->getMessages() as $message) {
                            $this->flashMessenger()->addErrorMessage($message);
                        }
                    }
                    $fr = $this->getRequest()->getPost('from')[$i];
                    array_push($from, "'$fr'");
                }
                $emails = [];
                for ($i=0; $i<count($this->getRequest()->getPost('emails')); $i++) {
                    if (!$validator->isValid($this->getRequest()->getPost('emails')[$i])) {
                        foreach ($validator->getMessages() as $message) {
                            $this->flashMessenger()->addErrorMessage($message);
                        }
                    }
                    $em = $this->getRequest()->getPost('emails')[$i];
                    array_push($emails, "'$em'");
                }
                $emails_imp = implode(',', $emails);
                $from_imp = implode(',', $from);
                $this->replaceRowInFile(
                    'config/autoload/mail.local.php.dist',
                    'emails',
                    "'emails'=>[$emails_imp]",
                    'config/autoload/mail.local.php.php.php'
                );
                $this->replaceRowInFile(
                    'config/autoload/mail.local.php.dist',
                    'from',
                    "'emails'=>[$from_imp]",
                    'config/autoload/mail.local.php.php.php'
                );

                $sessionForms->offsetSet('mailForm', $mailForm->getData());
                for ($i=0; $i<count($mailForm->getData()); $i++) {
                    $paramName = array_keys($mailForm->getData())[$i];
                    $paramValue = array_values($mailForm->getData())[$i];
                    $newRow = "'$paramValue'=>'$paramValue',";
                    $this->replaceRowInFile(
                        'config/autoload/mail.local.php.dist',
                        $paramName,
                        $newRow,
                        'config/autoload/mail.local.php.php.php'
                    );
                }
                $sessionProgress->offsetSet('mail', self::DONE);

                return $this->redirect()->toRoute(
                    'install/default',
                    [
                        'controller' => 'index',
                        'action' => 'modules'
                    ]
                );
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
                        'action' => 'requirements'
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

    public function requirementsAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('requirements', self::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('requirements', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'finish'
                ]
            );
        } else {
            $requirements = $this->getServiceLocator()->get('Config')['requirements'];
            return new ViewModel(['requirements' => $requirements]);
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
        return [ 'db', 'modules', 'requirements', 'mail', 'finish' ];
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

    public function replaceRowInFile($filePath, $word, $newRow, $newFilePath = null)
    {
        $reading = fopen($filePath, 'r');
        $writing = fopen("$filePath.tmp", 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            if (stristr($line, $word)) {
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
