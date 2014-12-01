<?php

namespace Categories\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Categories\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Categories\Validators;

class ManagementController extends AbstractActionController
{

    public function indexAction()
    {
        $entityManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $repository = $entityManager->getRepository('Categories\Entity\Categories');

        $escapeHtml = $this->getServiceLocator()->get('ViewHelperManager')->get('escapeHtml');
        $currentRootCategory = null;
        if ($id = $escapeHtml($this->params('id'))) {
            $currentRootCategory = $entityManager->getRepository('Categories\Entity\Categories')->findOneBy(['parentId' => null, 'id' => $id]);
        }
        $rootCategories = $entityManager->getRepository('Categories\Entity\Categories')->findBy(['parentId' => null]);

        if (!$currentRootCategory) {
            $currentRootCategory = $rootCategories[0];
        }
        $categories = $repository->findBy(['parentId' => $currentRootCategory->getId()], ['order' => 'ASC']);

        return new ViewModel(['categories' => $categories, 'rootTree' => $rootCategories, 'currentRoot' => $currentRootCategory]);

    }

    /**
     * @return ViewModel
     */
    public function createAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $category = new \Categories\Entity\Categories();
        $repository = $entityManager->getRepository('Categories\Entity\Categories');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $escapeHtml = $viewHelperManager->get('escapeHtml');
        $parentId = $escapeHtml($this->params('parentId'));

        $values = array();
        if (!$parent = $repository->findBy(['parentId' => $parentId])) {
            $parent = $repository->findBy(['parentId' => null]);
        }
        foreach ($parent as $value) {
            $values[] = $value->getOrder();
        }

        $order = max($values) + 1;

        $category->setParentId($parentId);
        $category->setOrder($order);
        $builder = new AnnotationBuilder($entityManager);

        $form = $builder->createForm($category);
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($category);

        if ($this->getRequest()->isPost()) {

            $form->setInputFilter(new Filter\CreateInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            $aliasValid = new Validator\NoObjectExists(['object_repository' => $repository, 'fields' => ['alias', 'parentId']]);

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
            }
        }
        $viewModel = new ViewModel(['form' => $form, 'title' => 'Create category']);
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

        if ($category->getParentId()) {
            $parentId = $category->getParentId()->getId();
            $category->setParentId($parentId);
        }

        $builder = new AnnotationBuilder($entityManager);

        $form = $builder->createForm($category);
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($category);

        if ($this->getRequest()->isPost()) {

            $form->setInputFilter(new Filter\CreateInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            $aliasValid = new Validators\NoObjectExists($repository);

            if ($form->isValid()) {
                if ($aliasValid->isValid(['alias' => $this->getRequest()->getPost('alias'),
                    'parentId' => $this->getRequest()->getPost('parentId')], $id)
                ) {
                    $entityManager->persist($category);
                    $entityManager->flush();

                    $categoryService = $this->getServiceLocator()->get('Categories\Service\Categories');
                    $categoryService->updateChildrenPath($category);

                    $this->flashMessenger()->addSuccessMessage('Category has been successfully edited!');
                    return $this->redirect()->toRoute('categories/default', array('controller' => 'management', 'action' => 'index'));
                }
                $form->get('alias')->setMessages(array(
                    'errorMessageKey' => 'Alias must be unique in its category!'
                ));
            }
        }
        $viewModel = new ViewModel(['form' => $form, 'title' => 'Edit category']);
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

    /**
     *
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function orderAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $entityManager->getRepository('Categories\Entity\Categories');

        $entityManager->getConnection()->beginTransaction();

        if ($this->getRequest()->isPost()) {
            $tree = $this->getRequest()->getPost('tree');
            $treeParent = $this->getRequest()->getPost('treeParent');
            try {
                $categories = json_decode($tree);
                if (!$categories) {
                    throw new \Exception('Categories tree is broken');
                }
                foreach ($categories as $node) {
                    if (isset($node->item_id)) {
                        $dbNode = $repository->findOneBy(['id' => $node->item_id]);

                        if (!$node->parent_id) {
                            $node->parent_id = $treeParent;
                        }

                        $parentId = $dbNode->getParentId()->getId();
                        if ($parentId != $node->parent_id && $node->parent_id) {

                            $dbNode->setParentId($repository->findOneBy(['id' => $node->parent_id]));
                        }

                        if ($dbNode->getOrder() != $node->order && $node->order) {
                            $dbNode->setOrder($node->order);
                        }
                        $entityManager->persist($dbNode);
                        $entityManager->flush();

                        $aliasValid = new Validators\NoObjectExists($repository);
                        if (!$aliasValid->isValid(['alias' => $dbNode->getAlias(),
                            'parentId' => $dbNode->getParentId()], $node->item_id)
                        ) {
                            throw new \Exception('Order has been failed!');
                        }

                    }
                }

                $entityManager->getConnection()->commit();

                $this->flashMessenger()->addSuccessMessage('Order has been successfully saved!');
                $returnJson = json_encode(['result' => 'success']);
            } catch (\Exception $e) {
                $entityManager->getConnection()->rollback();

                $this->flashMessenger()->addErrorMessage('Order has been failed!');
                $returnJson = json_encode(['result' => 'fail']);

            }
            echo $returnJson;
            return $this->response;
        }
    }

}
