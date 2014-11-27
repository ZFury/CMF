<?php

namespace Categories\Controller;

use Categories\Repository\Categories;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Categories\Form;
use Categories\Entity\RecursiveCategoryIterator;
use Zend\Form\Annotation\AnnotationBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Categories\Validators;

class ManagementController extends AbstractActionController
{

    public function indexAction()
    {
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        $rootCategories = $objectManager->getRepository('Categories\Entity\Categories')->findBy(array('parentId' => null));

        $collection = new ArrayCollection($rootCategories);
        $categoryIterator = new RecursiveCategoryIterator($collection);
        $recursiveIterator = new \RecursiveIteratorIterator($categoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

        return new ViewModel(array('categories' => $recursiveIterator));

//        $categories = $objectManager
//            ->getRepository('Categories\Entity\Categories')->findAll();
//
//        return new ViewModel(array('categories' => $categories));
    }

    /**
     * @return ViewModel
     */
    public function createAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $category = new \Categories\Entity\Categories();

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $escapeHtml = $viewHelperManager->get('escapeHtml');
        $parentId = $escapeHtml($this->params('parentId'));

        $category->setParentId($parentId);
        $builder = new AnnotationBuilder($entityManager);

        $form = $builder->createForm($category);
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($category);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter(new Form\CreateInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            $aliasValid = new \DoctrineModule\Validator\NoObjectExists(['object_repository' => $entityManager->getRepository('Categories\Entity\Categories'), 'fields' => ['alias', 'parentId']]);
            if ($form->isValid()) {
                $parentId = $this->getRequest()->getPost('parentId');
                if (empty($parentId)) {
                    $parentId = null;
                }
                if ($aliasValid->isValid(['alias' => $this->getRequest()->getPost('alias'),
                    'parentId' => $parentId])
                ) {
                    $entityManager->persist($category);
                    $entityManager->flush();

                    $this->flashMessenger()->addSuccessMessage('Category has been successfully added!');
                    return $this->redirect()->toRoute('categories/default', array('controller' => 'management', 'action' => 'index'));
                }
                $form->get('alias')->setMessages(array(
                    'errorMessageKey' => 'Alias must be unique in his category!'
                ));
//                $this->flashMessenger()->addErrorMessage('Such category alias is already exists!');
            }
        }
        $viewModel = new ViewModel(['form' => $form, 'title' => 'Create category']);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        }
        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $entityManager->getRepository('Categories\Entity\Categories');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $escapeHtml = $viewHelperManager->get('escapeHtml');
        $id = $escapeHtml($this->params('id'));
        $category = $repository->find($id);

        $parentId = $category->getParentId()->getId();

        $category->setParentId($parentId);
        $builder = new AnnotationBuilder($entityManager);

        $form = $builder->createForm($category);
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($category);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter(new Form\CreateInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            $aliasValid = new Validators\NoObjectExists($repository);
            if ($form->isValid()) {
                if ($aliasValid->isValid(['alias' => $this->getRequest()->getPost('alias'),
                    'parentId' => $this->getRequest()->getPost('parentId')], $id)
                ) {
//                    $entityManager->persist($category);
                    $entityManager->flush();

                    $this->flashMessenger()->addSuccessMessage('Category has been successfully edited!');
                    return $this->redirect()->toRoute('categories/default', array('controller' => 'management', 'action' => 'index'));
                }
                $form->get('alias')->setMessages(array(
                    'errorMessageKey' => 'Alias must be unique in its category!'
                ));
            }
        }
        $viewModel = new ViewModel(['form' => $form, 'title' => 'Edit category']);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        }
        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $escapeHtml = $viewHelperManager->get('escapeHtml');
        $id = $escapeHtml($this->params('id'));
        $category = $entityManager->find('Categories\Entity\Categories', $id);

        $entityManager->remove($category);

        $entityManager->flush();
        $this->flashMessenger()->addSuccessMessage('Category has been successfully deleted!');
        return $this->redirect()->toRoute('categories/default', array('controller' => 'management', 'action' => 'index'));
    }

}
