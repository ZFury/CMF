<?php

namespace Starter\Mvc\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Exception;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class AbstractCrudController
 * @package Starter\Mvc\Controller
 */
abstract class AbstractCrudController extends AbstractActionController
{
    /**
     * Create entity
     * @return ViewModel
     */
    public function createAction()
    {
        $form = $this->getCreateForm();
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $entity = $this->getEntity();
                $hydrator = new DoctrineHydrator($objectManager);
                $hydrator->hydrate($form->getData(), $entity);
                $objectManager->persist($entity);
                $objectManager->flush();

                //TODO: redirect where?
                $this->redirect()->toRoute(null, ['controller' => 'management']);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Edit entity
     * @return ViewModel
     */
    public function editAction()
    {
        $form = $this->getEditForm();
        $entity = $this->loadEntity();
        $form->bind($entity);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $objectManager->persist($entity);
                $objectManager->flush();

                //TODO: redirect where?
                $this->redirect()->toRoute(null, ['controller' => 'management']);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Delete entity
     * @return void
     */
    public function deleteAction()
    {
        $entity = $this->loadEntity();
        //TODO: change method to post maybe
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $objectManager->remove($entity);
        $objectManager->flush();

        //TODO: redirect where?
        $this->redirect()->toRoute(null, ['controller' => 'management']);
    }

    /**
     * Find entity by id
     *
     * @return mixed
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function loadEntity()
    {
        if (!$id = $this->params()->fromRoute('id')) {
            //TODO: fix exception
            throw new EntityNotFoundException('Bad Request');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if (!$model = $objectManager->getRepository(get_class($this->getEntity()))->find($id)) {
            throw new EntityNotFoundException('Entity not found');
        }
        return $model;
    }

    /**
     * Get CreateForm instance
     * @return mixed
     */
    abstract protected function getCreateForm();

    /**
     * Get EditForm instance
     * @return mixed
     */
    abstract protected function getEditForm();

    /**
     * Get Entity instance
     * @return mixed
     */
    abstract protected function getEntity();

}