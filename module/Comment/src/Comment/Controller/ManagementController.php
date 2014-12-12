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

        $form = $builder->createForm($this->getEntity());
        $form->setInputFilter(new Filter\CreateInputFilter($this->getServiceLocator()));

        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditForm()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $builder = new AnnotationBuilder($entityManager);
        $entityType = $this->loadEntity();


        $form = $builder->createForm($this->getEntity());
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($entityType);

        $form->setInputFilter(new Filter\CreateInputFilter($this->getServiceLocator()));

        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntity()
    {
        return new \Comment\Entity\EntityType();
    }

    public function deleteAction()
    {
        $entity = $this->loadEntity();

        //TODO: change method to post maybe
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $objectManager->remove($entity);
        $objectManager->flush();

        //TODO: redirect where?
//        $this->redirect()->toRoute(null, ['controller' => 'management']);
    }
}
