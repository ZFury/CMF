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
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Install\Form\MailConfig;
use Install\Service\Install;

class IndexController extends AbstractActionController
{


    public function globalRequirementsAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('global_requirements', Install::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('global_requirements', Install::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'database'
                ]
            );
        } else {
            //PHPVERSION
            if (Install::PHP_VERSION == phpversion() || Install::PHP_VERSION <= phpversion()) {
                $phpVersion['status'] = true;
                $phpVersion['message'] = "PHP version is compatible with ZFStarter!";
            } else {
                $phpVersion['status'] = false;
                $phpVersion['message'] =
                    "PHP version is not compatible for ZFStarter! It might be " .
                    Install::PHP_VERSION .
                    " or higher";
            }

            //FILES&DIRECTORIES
            $checked = $installService->checkFiles(Install::GLOBAL_REQUIREMENTS);
            $checkedDirectories = $checked['checkedDirectories'];
            $checkedFiles = $checked['checkedFiles'];

            $continue = Install::BAD;
            if (!$installService->inArrayRecursive(Install::BAD, $checkedDirectories) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedFiles)) {
                $continue = Install::GOOD;
            }

            return new ViewModel([
                'directories' => $checkedDirectories,
                'phpVersion' => $phpVersion,
                'files' => $checkedFiles,
                'continue' => $continue
            ]);
        }
    }

    public function databaseAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
//        var_dump($sessionProgress->offsetGet('global_requirements'));die();
        $sessionProgress->offsetSet('db', Install::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $dbForm = new DbConnection();
            $dbForm->setInputFilter(new DbConnectionInputFilter($this->getServiceLocator()));
            $dbForm->setData($this->getRequest()->getPost());
            if ($dbForm->isValid()) {
                $sessionForms->offsetSet('dbForm', $dbForm->getData());
                $sessionProgress->offsetSet('db', Install::DONE);

                try {
                    $installService->checkDbConnection($dbForm);
                    $installService->createDbConfig($dbForm);
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
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('mail', Install::TODO);
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
                            $installService->replaceRowInFile(
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
                            $installService->replaceRowInFile(
                                'config/autoload/mail.local.php.php.php',
                                $paramName,
                                $newRow,
                                'config/autoload/mail.local.php.php.php'
                            );
                        }
                    }
                    $sessionProgress->offsetSet('mail', Install::DONE);
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
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules', Install::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $modulesForm = new Modules();
            $modulesForm->setInputFilter(new ModulesInputFilter($this->getServiceLocator()));
            $modulesForm->setData($this->getRequest()->getPost());
            if ($modulesForm->isValid()) {
                $sessionForms->offsetSet('modulesForm', $modulesForm->getData());
                $sessionProgress->offsetSet('modules', Install::DONE);

                try {
                    $installService->hideModules($modulesForm);
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
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules_requirements', Install::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('modules_requirements', Install::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'database'
                ]
            );
        } else {
            //TOOLS
            $checkedTools = $installService->checkTools();

            //FILES&DIRECTORIES
            $checked = $installService->checkFiles();
            $checkedDirectories = $checked['checkedDirectories'];
            $checkedFiles = $checked['checkedFiles'];

            $continue = Install::BAD;
            if (!$installService->inArrayRecursive(Install::BAD, $checkedDirectories) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedFiles)) {
                $continue = Install::GOOD;
            }

            return new ViewModel([
                'directories' => $checkedDirectories,
                'files' => $checkedFiles,
                'tools' => $checkedTools,
                'continue' => $continue
            ]);
        }
    }

    public function finishAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('finish', Install::DONE);
        $this->setProgress();
        $sessionProgress->getManager()->getStorage()->clear('progress_tracker');
        $sessionProgress->getManager()->getStorage()->clear('forms');

        exec('install.sh');

        return new ViewModel();
    }

    public function setProgress()
    {
        $doneSteps = $this->getServiceLocator()->get('Install\Service\Install')->checkProgress();

        $this->layout()->setVariable('done_steps', $doneSteps);
        foreach ($doneSteps as $step) {
            $this->layout()->setVariable(array_keys($step)[0], array_values($step)[0]);
        }
    }
}
