<?php

namespace Comment\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend;
use Comment\Grid\Comment\Grid;
use Zend\Mvc\Exception;

class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        // for POST data
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
        }

        // for GET (or query string) data
        if ($this->getRequest()->getQuery('alias') && $entityId = intval($this->getRequest()->getQuery('id'))) {
            $data['alias'] = $this->getRequest()->getQuery('alias');
            $data['id'] = $this->getRequest()->getQuery('id');
        }

        if (!isset($data['alias']) || !isset($data['id'])) {
            throw new \Exception('Bad request');
        }

        $comments = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->tree($data);

        $viewModel = new ViewModel(array('comments' => $comments));

        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }

        return $viewModel;
    }

    /**
     * @return array|ViewModel
     * @throws \Exception
     */
    public function gridAction()
    {
        $this->layout('layout/dashboard/dashboard');
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $viewModel = new ViewModel(['grid' => $grid]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        return $viewModel;
    }

    /**
     * @return Zend\Http\Response
     * @throws EntityNotFoundException
     */
    public function deleteAction()
    {
        $params = array_merge($this->params()->fromPost(), $this->params()->fromRoute());
        if (empty($params['id'])) {
            $this->getResponse()->setStatusCode(400);
            throw new EntityNotFoundException('Bad Request');
        }

        $result = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->delete($params['id']);

        if ($result) {
            $flashMessenger = new FlashMessenger();
            $flashMessenger->addSuccessMessage('Comment deleted');
            if (!$this->getRequest()->isXmlHttpRequest()) {
                if ($this->getRequest()->getHeader('Referer')) {
                    return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
                } else {
                    return $this->redirect()->toUrl('/');
                }
            }
        }
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function editAction()
    {

        if (!$id = $this->params()->fromRoute('id')) {
            throw new \Exception('Bad request');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /** @var \Comment\Entity\Comment $comment */
        $comment = $objectManager->getRepository('\Comment\Entity\Comment')->findOneById($id);
        if (!$comment) {
            $this->getResponse()->setStatusCode(404);
            throw new \Exception("Invalid comment id. The comment you are trying to edit doesn't exist!");
        }

        /** @var \Comment\Entity\EntityType $entityType */
        $entityType = $objectManager->getRepository('\Comment\Entity\EntityType')
            ->findOneById($comment->getEntityTypeId());


        $form = $this->getServiceLocator()->get('Comment\Service\Comment')->createForm($comment);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $form->setData($data);
            $data = $data->toArray();
            $commentEdited = false;
            if ($form->isValid()) {
                $commentEdited = $this->getServiceLocator()
                    ->get('Comment\Service\Comment')
                    ->edit($comment, $data);
            }

            if ($commentEdited) {
                $this->flashMessenger()->addSuccessMessage('Comment edited');
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    if ($this->getRequest()->getHeader('Referer')) {
                        return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
                    } else {
                        return $this->redirect()->toUrl('/');
                    }
                }
                return;
            } else {
                $this->flashMessenger()->addErrorMessage('Comment is not changed');
                if ($this->getRequest()->getHeader('Referer')) {
                    return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
                } else {
                    return $this->redirect()->toUrl('/');
                }
            }
        }
        $viewModel = new ViewModel([
            'form' => $form,
            'title' => 'Add comment',
            'ajax' => $this->getRequest()->isXmlHttpRequest(),
            'alias' => $entityType->getAlias(),
            'id' => $comment->getEntityId()
        ]);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }

        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Comment\Service\Comment')->createForm();
        // for POST data
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            // for GET (or query string) data

            if ($this->getRequest()->getQuery('alias') && $entityId = intval($this->getRequest()->getQuery('id'))) {
                $data->set('alias', $this->getRequest()->getQuery('alias'));
                $data->set('entityId', $this->getRequest()->getQuery('id'));
            }

            if (!isset($data['alias']) || !isset($data['entityId'])) {
                $this->getResponse()->setStatusCode(400);
                throw new \Exception('Bad request');
            }
            $comment = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->add($form, $data);

            $flashMessenger = new FlashMessenger();
            if ($comment) {
                $flashMessenger->addSuccessMessage('Comment created');
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    if ($this->getRequest()->getHeader('Referer')) {
                        return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
                    } else {
                        return $this->redirect()->toUrl('/');
                    }
                }
                return;
            } else {
                $flashMessenger->addErrorMessage('Comment is not created');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'title' => 'Add comment']);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel;
    }
}
