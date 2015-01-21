<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mail\Controller;

use Mail\Grid\Grid;
use Fury\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use Mail\Form\Create;
use Zend\Mvc\MvcEvent;
use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ManagementController
 * @package Mail\Controller
 */
class ManagementController extends AbstractCrudController
{
    /**
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    /**
     * @return \Mail\Entity\Mail|mixed
     */
    protected function getEntity()
    {
        $entity = new \Mail\Entity\Mail();
        return $entity;
    }

    /**
     * @return Create|mixed
     */
    protected function getCreateForm()
    {
        return new \Mail\Form\Create('create', ['serviceLocator' => $this->getServiceLocator()]);
    }

    /**
     * @return Create|mixed
     */
    protected function getEditForm()
    {
        $form = new \Mail\Form\Create('edit', ['serviceLocator' => $this->getServiceLocator()]);
        $form->get('submit')->setValue('Save');
        return $form;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $viewModel = new ViewModel(['grid' => $grid]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        return $viewModel;
    }

    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        return new ViewModel(array('page' => $this->loadEntity()));
    }
}
