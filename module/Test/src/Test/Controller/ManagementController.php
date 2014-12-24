<?php

namespace Test\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Test\Form;
use Test\Entity;
use Test\Grid\Grid;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

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

    public function testAction()
    {
        $form = new \Pages\Form\Create('test', ['serviceLocator' => $this->getServiceLocator()]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $entity = $this->getEntity();
                $hydrator = new DoctrineHydrator($objectManager);
                $hydrator->hydrate($form->getData(), $entity);
                $objectManager->persist($entity);
                $objectManager->flush();
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
