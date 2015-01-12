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
        $sessionProgress->offsetSet('current_step', 'global_requirements');

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
        $sessionProgress->offsetSet('current_step', 'db');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => $previousStep
                ]
            );
        }
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

                    return $this->redirect()->toRoute(
                        'install/default',
                        [
                            'controller' => 'index',
                            'action' => 'mail'
                        ]
                    );
                } catch (\PDOException $e) {
                } catch (\Exception $e) {
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
        $sessionProgress->offsetSet('current_step', 'mail');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => $previousStep
                ]
            );
        }

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
                            for ($j = 0; $j < count($paramValue); $j++) {
                                $value = array_values($paramValue[$j]);
                                $currentEmail = array_shift($value);
                                if ('emails' == $paramName) {
                                    $paramName = strtoupper($paramName);
                                }
                                array_push($emailsArray, "'$currentEmail'");
                            }
                            $emails = implode(',', $emailsArray);
                            $installService->replaceRowInFile(
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
                                        $installService->replaceRowInFile(
                                            'config/autoload/mail.local.php',
                                            "'$headerName'",
                                            $newRow,
                                            'config/autoload/mail.local.php'
                                        );
                                    } else {
                                        $installService->replaceRowInFile(
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
                                $installService->replaceRowInFile(
                                    'config/autoload/mail.local.php',
                                    "'$paramName'",
                                    $newRow,
                                    'config/autoload/mail.local.php'
                                );
                            }
                        }
                    }
                    $sessionProgress->offsetSet('mail', Install::DONE);

                    return $this->redirect()->toRoute(
                        'install/default',
                        [
                            'controller' => 'index',
                            'action' => 'modules'
                        ]
                    );
                } catch (\Exception $ex) {
                    return $this->redirect()->toRoute(
                        'install/default',
                        [
                            'controller' => 'index',
                            'action' => 'mail',
                            'mailForm' => $mailForm
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
        $sessionProgress->offsetSet('current_step', 'modules');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => $previousStep
                ]
            );
        }

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
                } catch (\Exception $e) {
                }

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
        $sessionProgress->offsetSet('current_step', 'modules_requirements');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => $previousStep
                ]
            );
        }


        $sessionProgress->offsetSet('modules_requirements', Install::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('modules_requirements', Install::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'finish'
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
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress->offsetSet('current_step', 'finish');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => $previousStep
                ]
            );
        }

        $sessionProgress->offsetSet('finish', Install::DONE);
        $this->setProgress();
        $sessionProgress->getManager()->getStorage()->clear('progress_tracker');
        $sessionProgress->getManager()->getStorage()->clear('forms');

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
