<?php

namespace Test\Controller;

use Fury\Mvc\Controller\AbstractCrudController;
use Test\Form;
use Test\Entity;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Test\Grid\Grid;

class ManagementController extends AbstractCrudController
{
    protected function getEntity()
    {
        return new Entity\Test();
    }

    protected function getCreateForm()
    {
        $form = new Form\CreateForm(null, ['sm' => $this->getServiceLocator()]);
        $urlHelper = $this->getUrlHelper();
        $form
            ->setAttribute('action', $urlHelper('test/default', ['controller' => 'management', 'action' => 'create']));

        return $form;
    }

    protected function getEditForm()
    {
        $form = new Form\EditForm(null, ['sm' => $this->getServiceLocator()]);
        $entity = $this->loadEntity();
        $form->bind($entity);
        $urlHelper = $this->getUrlHelper();
        $form->setAttribute(
            'action',
            $urlHelper('test/default', ['controller' => 'management', 'action' => 'edit', 'id' => $entity->getId()])
        );

        return $form;
    }

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $grid->getData();
        $viewModel = new ViewModel(['grid' => $grid]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        return $viewModel;
    }

    public function angularAction()
    {
        /* @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $grid->init();
        if ($request->isXmlHttpRequest()) {
            return new JsonModel(array(
                'data' => $grid->getData(),
                'allowedFilters' => $grid->getAllowedFilters(),
                'totalPages' => $grid->totalPages(),
                'columns' => $grid->getColumns(),
                'allowedOrders' => $grid->getAllowedOrders(),
                'defaultLimit' => $grid->getDefaultLimit(),
                'order' => $grid->getOrder(),
                'prev' => $grid->prev(),
                'next' => $grid->next(),
                'urlPrev' => $grid->getUrl(['page' => $grid->prev()]),
                'urlNext' => $grid->getUrl(['page' => $grid->next()]),
                'urlPage' => $grid->getUrl(['page' => $grid->getPage()]),
            ));
        } else {
            return new ViewModel(['grid' => $grid]);
        }
    }

    public function createWithAngularAction()
    {
        return new ViewModel();
    }

    public function editWithAngularAction()
    {
        return new ViewModel();
    }

    public function getTestAction()
    {
        $model = $this->loadEntity();
        $test = array(
            'email' => $model->getEmail(),
            'name' => $model->getName(),
            'id' => $model->getId(),
        );
        return new JsonModel(['data' => $test]);
    }
}
