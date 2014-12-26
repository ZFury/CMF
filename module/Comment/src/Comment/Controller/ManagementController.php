<?php

namespace Comment\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Comment\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Comment\Validators;
use Zend\Form;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
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

        $options = $entityManager
            ->getRepository('Comment\Entity\EntityType')->getEntities();
        $select = $form->getElements()['entity'];
        $select->setValueOptions($options);
        $select->setOptions(array('empty_option' => 'Please choose entity'));
        $form->setInputFilter(new Filter\EntityTypeInputFilter($this->getServiceLocator()));

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
        $form->bind($this->loadEntity());
//        var_dump($form);die();
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
