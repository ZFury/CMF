<?php

namespace Fury\Mvc\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class AbstractCrudController
 * @package Fury\Mvc\Controller
 */
abstract class AbstractCrudController extends AbstractActionController
{
    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
//        $this->layout('layout/dashboard/dashboard');
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }
        $action = $routeMatch->getParam('action', 'not-found');

        $this->viewModel = new ViewModel();
        if ($action == 'create' || $action == 'edit') {
            $this->viewModel->setTemplate('crud/' . $action);
        }

        parent::onDispatch($e);
        $this->layout('layout/dashboard/dashboard');
    }

    /**
     * Create entity
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function createAction()
    {
        /**
         * @var $form \Zend\Form\Form
         */
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
                $controller = explode('/', $this->getRequest()->getUri()->getPath())[2];
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    return $this->redirect()->toRoute(null, ['controller' => $controller]);
                }
                return;
            }
        }
        $viewModel = $this->getViewModel();
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel->setVariables(['form' => $form]);
    }

    /**
     * Edit entity
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws EntityNotFoundException
     */
    public function editAction()
    {
        $form = $this->getEditForm();
        $entity = $form->getObject();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $hydrator = new DoctrineHydrator($entityManager);
            $hydrator->hydrate($data, $entity);

            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $objectManager->persist($entity);
                $objectManager->flush();
                $controller = explode('/', $this->getRequest()->getUri()->getPath())[2];
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    return $this->redirect()->toRoute(null, ['controller' => $controller]);
                }
                return;
            } else {
                $this->getResponse()->setStatusCode(400);
                return;
            }
        }
        $viewModel = $this->getViewModel();
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel->setVariables(['form' => $form]);
    }

    /**
     * Delete entity
     *
     * @return \Zend\Http\Response
     * @throws EntityNotFoundException
     */
    public function deleteAction()
    {
        $entity = $this->loadEntity();
        //TODO: change method to post maybe
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $objectManager->remove($entity);
        $objectManager->flush();

        $controller = explode('/', $this->getRequest()->getUri()->getPath())[2];

        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->redirect()->toRoute(null, ['controller' => $controller]);
        }
        return;
    }

    /**
     * Find entity by id
     *
     * @return mixed
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function loadEntity()
    {
        $params = array_merge($this->params()->fromPost(), $this->params()->fromRoute());
        if (empty($params['id'])) {
            throw new EntityNotFoundException('Bad Request');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if (!$model = $objectManager->getRepository(get_class($this->getEntity()))->find($params['id'])) {
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

    /**
     * Return CRUD view model.
     *
     * @return ViewModel
     */
    protected function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * Get UrlHelper.
     *
     * @return \Zend\View\Helper\Url
     */
    protected function getUrlHelper()
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get('url');
    }
}
