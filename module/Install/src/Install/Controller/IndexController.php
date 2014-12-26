<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/19/14
 * Time: 4:58 PM
 */

namespace Install\Controller;

use Install\Form\DbConnection;
use Install\Form\Modules;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    const DONE = 'progress-tracker-done';
    const TODO = 'progress-tracker-todo';
    const STEPS_NUMBER = 5;

    public function databaseAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('db', self::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $dbForm = new DbConnection();
            $dbForm->setInputFilter(new \Install\Form\Filter\DbConnectionInputFilter($this->getServiceLocator()));
            $dbForm->setData($this->getRequest()->getPost());
            if ($dbForm->isValid()) {
                $sessionForms = new Container('forms');
                $sessionForms->offsetSet('dbForm', $dbForm);
            }

            $sessionProgress = new Container('progress_tracker');
            $sessionProgress->offsetSet('db', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'modules'
                ]
            );
        } else {
            $dbForm = new DbConnection();

            return new ViewModel([
                'dbForm' => $dbForm,
            ]);
        }
    }

    public function modulesAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('modules', self::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $modulesForm = new Modules();
            $modulesForm->setData($this->getRequest()->getPost());
            if ($modulesForm->isValid()) {
                $sessionForms = new Container('forms');
                $sessionForms->offsetSet('modulesForm', $modulesForm);
            }

            $sessionProgress = new Container('progress_tracker');
            $sessionProgress->offsetSet('modules', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'requirements'
                ]
            );
        } else {
            $modulesForm = new Modules();

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
            $dbForm = new DbConnection();
            $dbForm->setInputFilter(new \Install\Form\Filter\DbConnectionInputFilter($this->getServiceLocator()));
            $dbForm->setData($this->getRequest()->getPost());
            if ($dbForm->isValid()) {
                $sessionForms = new Container('forms');
                $sessionForms->offsetSet('dbForm', $dbForm);
            }
            $sessionProgress = new Container('progress_tracker');
            $sessionProgress->offsetSet('requirements', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'configs'
                ]
            );
        } else {
            $requirementsForm = new DbConnection();

            return new ViewModel([
                'requirementsForm' => $requirementsForm
            ]);
        }
    }

    public function configsAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('configs', self::TODO);
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $sessionProgress = new Container('progress_tracker');
            $sessionProgress->offsetSet('configs', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'finish'
                ]
            );
        } else {
            $configsForm = new DbConnection();

            return new ViewModel([
                'configsForm' => $configsForm
            ]);
        }
    }

    public function finishAction()
    {
        $this->layout('layout/install/install');
        $sessionProgress = new Container('progress_tracker');
        $sessionProgress->offsetSet('finish', self::DONE);
        $this->setProgress();
        $sessionProgress->getManager()->getStorage()->clear('progress_tracker');



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
        return [ 'db', 'modules', 'requirements', 'configs', 'finish' ];
    }
}
