<?php

namespace Comment\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Comment\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Comment\Validators;
use Zend\Form\Form;

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
                $entityValid = new Validators\NoObjectExists($objectManager->getRepository('Comment\Entity\EntityType'));
                if (!$entityValid->isValid(['aliasEntity' => $form->get('aliasEntity')->getValue()], $this->params()->fromRoute('id'))) {
                    $form->get('aliasEntity')->setMessages(
                        array(
                            'errorMessageKey' => 'AliasEntity must be unique in its category!'
                        )
                    );
                    $viewModel = $this->getViewModel();
                    return $viewModel->setVariables(['form' => $form]);
                }
                if (!$entityValid->isValid(['entity' => $form->get('entity')->getValue()], $this->params()->fromRoute('id'))) {
                    $form->get('entity')->setMessages(
                        array(
                            'errorMessageKey' => 'Entity must be unique in its category!'
                        )
                    );
                    $viewModel = $this->getViewModel();
                    return $viewModel->setVariables(['form' => $form]);
                }

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
     * Edit entity
     * @return ViewModel
     */
    public function editAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form = $this->getEditForm();
        $entity = $this->loadEntity();
        $form->bind($entity);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
           // $form->setBindOnValidate(Form::BIND_MANUAL);
            if ($form->isValid()) {
                $entityValid = new Validators\NoObjectExists($objectManager->getRepository('Comment\Entity\EntityType'));
                if (!$entityValid->isValid(['aliasEntity' => $form->get('aliasEntity')->getValue()], $this->params()->fromRoute('id'))) {
                    $form->get('aliasEntity')->setMessages(
                        array(
                            'errorMessageKey' => 'AliasEntity must be unique in its category!'
                        )
                    );
                    $viewModel = $this->getViewModel();
                    return $viewModel->setVariables(['form' => $form]);
                }
                if (!$entityValid->isValid(['entity' => $form->get('entity')->getValue()], $this->params()->fromRoute('id'))) {
                    $form->get('entity')->setMessages(
                        array(
                            'errorMessageKey' => 'Entity must be unique in its category!'
                        )
                    );
                    $viewModel = $this->getViewModel();
                    return $viewModel->setVariables(['form' => $form]);
                }

                    $objectManager->persist($entity);
                    $objectManager->flush();
                    $this->flashMessenger()->addSuccessMessage('Category has been successfully edited!');
                    //TODO: redirect where?
                    return $this->redirect()->toRoute(null, ['controller' => 'management']);

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


        $form = $this->getCreateForm();//$builder->createForm($this->getEntity());

        $entityType = $this->loadEntity();
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
}
