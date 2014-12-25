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
        $viewModel = new ViewModel();

        return $viewModel->setVariables(['form' => $form]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        if (!$id = $this->params()->fromRoute('id')) {
            throw new \Exception('Bad Request');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entityType = $objectManager->getRepository('\Comment\Entity\EntityType')->findOneBy(['id' => $id]);

        $form = $this->getEditForm();
        $form->bind($entityType);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();

            $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $hydrator = new DoctrineHydrator($entityManager);
            $hydrator->hydrate($data, $entityType);
            $flashMessenger = new FlashMessenger();
            if ($form->isValid()) {
                $entityManager->persist($entityType);
                $entityManager->flush();
                $this->flashMessenger()->addSuccessMessage('Entity type has been successfully edited!');
                //TODO: redirect where?
                return $this->redirect()->toRoute(null, ['controller' => 'management']);
            } else {
                $flashMessenger->addErrorMessage('Entity type is not changed');
            }
        }
        $viewModel = new ViewModel();

        return $viewModel->setVariables(['form' => $form]);
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
