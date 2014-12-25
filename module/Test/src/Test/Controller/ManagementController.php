<?php

namespace Test\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
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
        /* @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $count = null;
        $searchString = '';
        if ($request->isXmlHttpRequest()) {
            $sm = $this->getServiceLocator();
            $grid = new Grid($sm);
            $grid->init();
            $data = $grid->getData();
            $em = $sm->get('Doctrine\ORM\EntityManager');
            /* @var \Test\Repository\Test $usersManager */
            $usersManager = $em->getRepository('Test\Entity\Test');
            $params = $request->getPost('data');
            $searchString = $params['searchString'];
            $count = $usersManager->countSearchTests($searchString);
            return new JsonModel(array(
                'data' => $data,
                'count' => $count
            ));
        } else {
            return new ViewModel();
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
