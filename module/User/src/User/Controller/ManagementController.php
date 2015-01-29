<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 04.09.14
 * Time: 12:37
 */
namespace User\Controller;

use Fury\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use User\Service;
use User\Entity;
use User\Form;
use User\Grid\Grid;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ManagementController extends AbstractCrudController
{

    /**
     * Index action for default users grid
     * @return ViewModel
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
     * Create action
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function createAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $this->getEntity();
        $form = $this->getCreateForm();
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($user);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter(new Form\Filter\CreateInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();
                $authService = new Service\Auth($this->getServiceLocator());
                $authService->generateEquals($user, $form->get('password')->getValue());
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    return $this->redirect()->toRoute(null, ['controller' => 'management']);
                } else {
                    return;
                }
            }
        }
        $viewModel = new ViewModel(['form' => $form]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel;
    }

    /**
     * Edit action
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\EntityNotFoundException
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
                $entityManager->persist($entity);
                $entityManager->flush();
                $authService = new Service\Auth($this->getServiceLocator());
                if ($form->get('password')->getValue()) {
                    $authService->generateEquals($entity, $form->get('password')->getValue());
                }
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    return $this->redirect()->toRoute(null, ['controller' => 'management']);
                } else {
                    return;
                }
            }
        }
        $viewModel = new ViewModel(['form' => $form]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel;
    }

    /**
     * Grid action for angular
     *
     * @return \Zend\View\Model\ViewModel
     *
     * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
     */
    public function gridAction()
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
                'columns' => $grid->getColumns(),
                'totalPages' => $grid->totalPages(),
                'allowedOrders' => $grid->getAllowedOrders(),
                'defaultLimit' => $grid->getDefaultLimit(),
                'defaultFilter' => $grid->getFilter(),
                'defaultOrder' => $grid->getOrder(),
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

    /**
     * Get entity
     *
     * @return Entity\User
     */
    public function getEntity()
    {
        return new Entity\User();
    }

    /**
     * Get create form
     *
     * @return Form\CreateForm
     */
    public function getCreateForm()
    {
        $form = new Form\CreateForm();
        $urlHelper = $this->getUrlHelper();
        $form->setAttribute(
            'action',
            $urlHelper('user/default', ['controller' => 'management', 'action' => 'create'])
        );

        return $form;
    }

    /**
     * Get edit form
     *
     * @return Form\EditForm
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getEditForm()
    {
        $form = new Form\EditForm();
        /** @var Entity\User $entity */
        $entity = $this->loadEntity();
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($entity);
        $form->setInputFilter(new Form\Filter\EditInputFilter($this->getServiceLocator()));
        $urlHelper = $this->getUrlHelper();
        $form->setAttribute(
            'action',
            $urlHelper('user/default', ['controller' => 'management', 'action' => 'edit', 'id' => $entity->getId()])
        );

        return $form;
    }
}
