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
    /**
     * @return ViewModel
     */
    public function globalRequirementsAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('global-requirements', Install::TODO);
        $sessionProgress->offsetSet('current_step', 'global-requirements');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('global-requirements', Install::DONE);
            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => 'database'
            ]);
        } else {
            //PHPVERSION
            if (version_compare(Install::PHP_VERSION, phpversion(), '<=')) {
                $phpVersion['status'] = true;
                $phpVersion['message'] = "PHP version is compatible with ZFury!";
            } else {
                $phpVersion['status'] = false;
                $phpVersion['message'] = "PHP version is not compatible for ZFury! It might be " .
                    Install::PHP_VERSION . " or higher";
            }

            //FILES&DIRECTORIES&TOOLS
            $checked = $installService->checkFiles(Install::GLOBAL_REQUIREMENTS);
            $checkedDirectories = $checked['checkedDirectories'];
            $checkedFiles = $checked['checkedFiles'];
            $checkedExtensions = $installService->checkExtensions(Install::GLOBAL_REQUIREMENTS);
            $checkedTools = $installService->checkTools(Install::GLOBAL_REQUIREMENTS);

            $continue = Install::BAD;

            if (!$installService->inArrayRecursive(Install::BAD, $checkedDirectories) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedFiles) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedExtensions) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedTools) &&
                true === $phpVersion['status']) {

                $continue = Install::GOOD;
            }

            return new ViewModel([
                'directories' => $checkedDirectories,
                'phpVersion' => $phpVersion,
                'files' => $checkedFiles,
                'extensions' => $checkedExtensions,
                'tools' => $checkedTools,
                'continue' => $continue
            ]);
        }
    }

    /**
     * @return ViewModel
     */
    public function databaseAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('current_step', 'database');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => $previousStep
            ]);
        }
        $sessionProgress->offsetSet('database', Install::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $dbForm = new DbConnection();
            $dbForm->setInputFilter(new DbConnectionInputFilter($this->getServiceLocator()));
            $dbForm->setData($this->getRequest()->getPost());
            if ($dbForm->isValid()) {
                $sessionForms->offsetSet('dbForm', $dbForm->getData());
                $sessionProgress->offsetSet('database', Install::DONE);
                try {
                    $installService->checkDbConnection($dbForm);
                    $installService->createDbConfig($dbForm);

                    return $this->redirect()->toRoute('install/default', [
                        'controller' => 'index',
                        'action' => 'mail'
                    ]);
                } catch (\PDOException $e) {
                    $dbForm->get('host')->setMessages([$e->getMessage()]);
                } catch (\Exception $e) {
                    $dbForm->get('port')->setMessages([$e->getMessage()]);
                }
            }
        } else {
            $dbForm = new DbConnection();
            if (null !== $sessionForms->offsetGet('dbForm')) {
                $dbForm->setData($sessionForms->offsetGet('dbForm'));
            }
        }

        return new ViewModel(['dbForm' => $dbForm]);
    }

    /**
     * @return ViewModel
     */
    public function mailAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('current_step', 'mail');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => $previousStep
            ]);
        }

        $sessionProgress->offsetSet('mail', Install::TODO);
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $mailForm = new MailConfig();
            $mainInputFilter = new MailConfigInputFilter($this->getServiceLocator());
            $mailForm->setInputFilter($mainInputFilter);
            $mailForm->setData($this->getRequest()->getPost());
            if ($mailForm->isValid()) {
                try {
                    $sessionForms->offsetSet('mailForm', $mailForm->getData());
                    $installService->createMailConfig($mailForm);
                    $sessionProgress->offsetSet('mail', Install::DONE);

                    return $this->redirect()->toRoute('install/default', [
                        'controller' => 'index',
                        'action' => 'modules'
                    ]);
                } catch (\Exception $ex) {
                    $mailForm->get('host')->setMessages([$ex->getMessage()]);
                }
            }
        } else {
            $mailForm = new MailConfig();
            if (null !== $sessionForms->offsetGet('mailForm')) {
                $mailForm->setData($sessionForms->offsetGet('mailForm'));
            }
        }

        return new ViewModel(['mailForm' => $mailForm]);
    }

    /**
     * @return ViewModel
     */
    public function modulesAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('current_step', 'modules');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => $previousStep
            ]);
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

                try {
                    $installService->hideModules($modulesForm);
                    $sessionProgress->offsetSet('modules', Install::DONE);
                    return $this->redirect()->toRoute('install/default', [
                        'controller' => 'index',
                        'action' => 'modules-requirements'
                    ]);
                } catch (\Exception $e) {
                    $modulesForm->get('Categories')->setMessages([$e->getMessage()]);
                }
            }
        } else {
            $modulesForm = new Modules();
            if (null !== $sessionForms->offsetGet('modulesForm')) {
                $modulesForm->setData($sessionForms->offsetGet('modulesForm'));
            }
        }

        return new ViewModel(['modulesForm' => $modulesForm]);
    }

    /**
     * @return ViewModel
     */
    public function modulesRequirementsAction()
    {
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules', Install::DONE);
        $sessionProgress->offsetSet('current_step', 'modules-requirements');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => $previousStep
            ]);
        }
        $sessionProgress->offsetSet('modules-requirements', Install::TODO);
        $this->setProgress();
        if ($this->getRequest()->isPost()) {
            $sessionProgress->offsetSet('modules-requirements', Install::DONE);

            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => 'finish'
            ]);
        } else {
            $continue = Install::BAD;

            //FILES&DIRECTORIES&TOOLS
            $checked = $installService->checkFiles();
            $checkedDirectories = $checked['checkedDirectories'];
            $checkedFiles = $checked['checkedFiles'];
            $checkedTools = $installService->checkTools();
            $checkedExtensions = $installService->checkExtensions();

            if (!$installService->inArrayRecursive(Install::BAD, $checkedDirectories) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedFiles) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedExtensions) &&
                !$installService->inArrayRecursive(Install::BAD, $checkedTools) &&
                Install::GOOD === $continue) {
                $continue = Install::GOOD;
            } else {
                $continue = Install::BAD;
            }

            return new ViewModel([
                'directories' => $checkedDirectories,
                'files' => $checkedFiles,
                'tools' => $checkedTools,
                'extensions' => $checkedExtensions,
                'continue' => $continue
            ]);
        }
    }

    /**
     * @return ViewModel
     */
    public function finishAction()
    {
        $sessionProgress = new Container('progress_tracker');
        $installService = $this->getServiceLocator()->get('Install\Service\Install');
        $sessionProgress->offsetSet('current_step', 'finish');
        $previousStep = $installService->checkPreviousStep();
        if (null !== $previousStep) {
            return $this->redirect()->toRoute('install/default', [
                'controller' => 'index',
                'action' => $previousStep
            ]);
        }
        //FILES&DIRECTORIES
        $checked = $installService->checkFiles();
        $checkedDirectoriesLocal = $checked['checkedDirectories'];
        $checkedFilesLocal = $checked['checkedFiles'];
        $checked = $installService->checkFiles(Install::GLOBAL_REQUIREMENTS);
        $checkedDirectoriesGlobal = $checked['checkedDirectories'];
        $checkedFilesGlobal = $checked['checkedFiles'];
        $checkedDirectories = array_merge($checkedDirectoriesLocal, $checkedDirectoriesGlobal);
        $checkedFiles = array_merge($checkedFilesLocal, $checkedFilesGlobal);
        //DOCTRINE2
        $doctrine = [];
        exec('./vendor/bin/doctrine-module orm:schema-tool:update --force', $output, $returnUpdate);
        exec(
            'vendor/doctrine/doctrine-module/bin/doctrine-module migrations:migrate --dry-run',
            $output,
            $returnMigrate
        );
        if ((isset($returnUpdate) && 0 === $returnUpdate) && (isset($returnMigrate) && 0 === $returnMigrate)) {
            $doctrine['status'] = Install::GOOD;
            $doctrine['message'] = "Doctrine2 had successfully updated DB schema and migrated!";
        } else {
            $doctrine['status'] = Install::BAD;
            $doctrine['message'] = "Doctrine2 had not updated DB schema, update it and migrate by yourself!";
        }

        $sessionProgress->offsetSet('finish', Install::DONE);
        $this->setProgress();
        $sessionProgress->getManager()->getStorage()->clear('progress_tracker');
        $sessionProgress->getManager()->getStorage()->clear('forms');

        //HIDING INSTALL
        $installService->replaceRowInFile('config/application.config.php', "'Install'", "//'Install'");
        //UNHIDING BJY
        $installService->replaceRowInFile('config/application.config.php', "//'BjyAuthorize'", "'BjyAuthorize',");

        return new ViewModel([
            'directories' => $checkedDirectories,
            'files' => $checkedFiles,
            'doctrine' => $doctrine
        ]);
    }

    /**
     * Sets installation progress
     */
    public function setProgress()
    {
        $steps = $this->getServiceLocator()->get('Install\Service\Install')->checkProgress();
        $this->layout()->setVariable('steps', $steps);
    }
}
