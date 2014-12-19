<?php

namespace Starter\Mvc\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class AbstractCrudController
 * @package Starter\Mvc\Controller
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
        $this->layout('layout/dashboard');
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
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    //TODO: redirect where?
                    return $this->redirect()->toRoute(null, ['controller' => 'management']);
                } else {
                    return true;
                }

            }
        }
        $viewModel = $this->getViewModel();

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
        $entity = $this->loadEntity();
        $form->bind($entity);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $objectManager->persist($entity);
                $objectManager->flush();

                //TODO: redirect where?
                return $this->redirect()->toRoute(null, ['controller' => 'management']);
            }
        }
        $viewModel = $this->getViewModel();

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

        //TODO: redirect where?
        return $this->redirect()->toRoute(null, ['controller' => 'management']);
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
     * Gets CRUD view model and sets require parameters.
     *
     * @param $form
     * @param array $variables Variables that will be used in view.
     * <code>
     * 'variables' => array(
     *      '[variable name]' => [variable value]
     * )
     * </code>
     * @param array $scripts Scripts for require that will be used in view.
     * <code>
     * 'scripts' => array(
     *      '[require js module name1],
     *      '[require js module name2],
     *      ...
     * )
     * </code>
     * @param array $fileUpload Set that parameter if you want to use file upload form in your view.
     * <code>
     * 'fileUpload' => array(
     *     'imageService' => [file service instance],
     *     'module' => [upload js name],
     *     'type' => [file type],
     *      'id' => [entity id]
     * )
     * </code>
     * @return ViewModel
     */
    protected function prepareViewModel($form, array $variables = null, array $scripts = null, array $fileUpload = null)
    {
        return $this->viewModel->setVariables([
            'form' => $form,
            'variables' => $variables,
            'scripts' => $scripts,
            'fileUpload' => $fileUpload
        ]);
    }
}
