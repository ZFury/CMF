<?php

namespace Test\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Test\Form;
use Test\Entity;
use Zend\View\Model\JsonModel;
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
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Test\Entity\Test');
        return new ViewModel(['data' => $repository->findAll()]);
    }

    public function angularAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Test\Entity\Test');
        return new ViewModel(['data' => $repository->findAll()]);
    }



    public function createWithAngularAction()
    {
        return new ViewModel();
    }

    public function editWithAngularAction()
    {
        $id = $this->params()->fromRoute('id');
//        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
//        $model = $objectManager->getRepository(get_class($this->getEntity()))->find($id);
        return new ViewModel(['id' => $id]);
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
