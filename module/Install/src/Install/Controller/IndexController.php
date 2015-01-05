<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/19/14
 * Time: 4:58 PM
 */

namespace Install\Controller;

use Install\Form\DbConnection;
use Install\Form\Filter\DbConnectionInputFilter;
use Install\Form\Filter\MailConfigInputFilter;
use Install\Form\Modules;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Install\Form\MailConfig;

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
        $sessionForms = new Container('forms');
        $this->setProgress();

        if ($this->getRequest()->isPost()) {
            $dbForm = new DbConnection();
            $dbForm->setInputFilter(new DbConnectionInputFilter($this->getServiceLocator()));
            $dbForm->setData($this->getRequest()->getPost());
            var_dump($dbForm->isValid());
            if ($dbForm->isValid()) {
                $sessionForms->offsetSet('dbForm', $dbForm);
                var_dump($sessionForms->offsetGet('dbForm'));
            }

            $sessionProgress->offsetSet('db', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'mail'
                ]
            );
        } else {
            var_dump($sessionForms->offsetGet('dbForm'));
            if (null !== $sessionForms->offsetGet('dbForm')) {
                var_dump('aaa');
                $dbForm = $sessionForms->offsetGet('dbForm');
            } else {
                var_dump('bbb');
                $dbForm = new DbConnection();
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
                $sessionForms->offsetSet('mailForm', $mailForm);
            }

            $sessionProgress = new Container('progress_tracker');
            $sessionProgress->offsetSet('mail', self::DONE);

            return $this->redirect()->toRoute(
                'install/default',
                [
                    'controller' => 'index',
                    'action' => 'modules'
                ]
            );
        } else {
            if (null !== $sessionForms->offsetGet('mailForm')) {
                $mailForm = $sessionForms->offsetGet('mailForm');
            } else {
                $mailForm = new MailConfig();
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
            $modulesForm->setData($this->getRequest()->getPost());
            if ($modulesForm->isValid()) {
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
            if (null !== $sessionForms->offsetGet('modulesForm')) {
                $modulesForm = $sessionForms->offsetGet('modulesForm');
            } else {
                $modulesForm = new Modules();
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
}
