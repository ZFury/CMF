<?php

namespace Comment\Controller;

use Fury\Mvc\Controller\AbstractCrudController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Comment\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Comment\Validators;
use Zend\Form;
use Comment\Entity\EntityType;
use Comment\Grid\EntityType\Grid;

class ManagementController extends AbstractCrudController
{

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $viewModel = new ViewModel(['grid' => $grid]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel;
    }

    /**
     * {@inheritDoc}
     */
    protected function getCreateForm()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $builder = new AnnotationBuilder($entityManager);
        $form = $builder->createForm($this->getEntity());
        $options = $entityManager->getRepository('Comment\Entity\EntityType')->getEntities();
        $select = $form->getElements()['entity'];
        $select->setValueOptions($options);
        $select->setOptions(array('empty_option' => 'Please choose entity'));
        $form->setInputFilter(new Filter\EntityTypeInputFilter($this->getServiceLocator()));
        $urlHelper = $this->getUrlHelper();
        $form->setAttribute(
            'action',
            $urlHelper('comment/default', ['controller' => 'management', 'action' => 'create'])
        );

        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditForm()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form = $this->getCreateForm();
        $form->setHydrator(new DoctrineHydrator($entityManager));
//        $form->setHydrator(new ClassMethods(false));
        $entity = $this->loadEntity();
        $form->bind($entity);
        $urlHelper = $this->getUrlHelper();
        $form->setAttribute(
            'action',
            $urlHelper('comment/default', ['controller' => 'management', 'action' => 'edit', 'id' => $entity->getId()])
        );

        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntity()
    {
        return new EntityType();
    }
}
