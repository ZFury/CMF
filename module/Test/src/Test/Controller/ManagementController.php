<?php

namespace Test\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Test\Form;
use Test\Entity;
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
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Test\Entity\Test');
        return new ViewModel(['data' => $repository->findAll()]);
    }
}
