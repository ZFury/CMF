<?php

namespace Test\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Test\Form;
use Test\Entity;
use Test\Grid\Grid;
use Zend\View\Model\ViewModel;

class ManagementController extends AbstractCrudController
{
    protected function getEntity()
    {
        return new Entity\Test();
    }

    protected function getCreateForm()
    {
        return new Form\CreateForm(null, ['sm' => $this->getServiceLocator()]);
    }

    protected function getEditForm()
    {
        return new Form\EditForm(null, ['sm' => $this->getServiceLocator()]);
    }

    public function indexAction()
    {
//        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
//        $repository = $objectManager->getRepository('Test\Entity\Test');
//        return new ViewModel(['data' => $repository->findAll()]);


        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        //$viewModel->setVariable('grid', $grid);
        return $viewModel;
    }

    public function gridAction()
    {
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $grid->init();
        return new ViewModel(['grid' => $grid]);
    }
}
