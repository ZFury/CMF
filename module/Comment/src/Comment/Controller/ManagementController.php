<?php

namespace Comment\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Comment\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Comment\Validators;

class ManagementController extends AbstractCrudController
{

    public function indexAction()
    {
        $entityManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $entities = $entityManager->getRepository('Comment\Entity\EntityType')->findAll();

        return new ViewModel(['entities' => $entities]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getCreateForm()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $builder = new AnnotationBuilder($entityManager);

        return $builder->createForm($this->getEntity());
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditForm()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $builder = new AnnotationBuilder($entityManager);
        $comment = $this->loadEntity();


        $form = $builder->createForm($this->getEntity());
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($comment);

        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntity()
    {
        return new \Comment\Entity\EntityType();
    }
}
