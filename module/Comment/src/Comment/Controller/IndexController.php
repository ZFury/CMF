<?php

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend;

class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {

        // for POST data
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
        }

        // for GET (or query string) data
        if ($this->getRequest()->getQuery()->entity && $entityId = intval($this->getRequest()->getQuery()->id)) {
            $data = array();
            $data['entityType'] = $this->getRequest()->getQuery()->entity;
            $data['entityId'] = $this->getRequest()->getQuery()->id;
        }

        if (!isset($data['entityType']) || !isset($data['entityId'])) {
            return $this->notFoundAction();
        }

        $comments = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->getCommentsByEntityId($data);

        $viewModel = new ViewModel(array('comments' => $comments));

        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }

        return $viewModel;
    }

    /**
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function deleteAction()
    {
        if (!($id = $this->params()->fromRoute('id'))) {
            throw new \Exception("No number comments that removed");
        }

        $result = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->deleteCommentById($id);

        if ($result) {
            $this->flashMessenger()->addSuccessMessage('Comment deleted');

            //TODO: redirect where?
            $url = $this->getRequest()->getHeader('Referer')->getUri();
            return $this->redirect()->toUrl($url);
        }
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        if (!$id = $this->params()->fromRoute('id')) {
            throw new \Exception('Bad Request');
        }

        $form = $this->getServiceLocator()
            ->get('Comment\Service\Comment')->createEditForm($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $data = $data->toArray();

            $comment = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->editCommentById($form, $data);

            $flashMessenger = new FlashMessenger();
            if ($comment) {
                $flashMessenger->addSuccessMessage('Comment edited');
                $url = $this->getRequest()->getHeader('Referer')->getUri();
                return $this->redirect()->toUrl($url);
            } else {
                $flashMessenger->addErrorMessage('Comment is not changed');
            }
        }
        $viewModel = new ViewModel(['form' => $form, 'path' => $this->getRequest()->getUri()->getPath()]);

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
        $form = $this->getServiceLocator()
            ->get('Comment\Service\Comment')->createForm();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();  // for POST data

            // for GET (or query string) data
            if ($this->getRequest()->getQuery()->entity && $entityId = intval($this->getRequest()->getQuery()->id)) {
                $data->set('entityType', $this->getRequest()->getQuery()->entity);
                $data->set('entityId', $this->getRequest()->getQuery()->id);
            }

            $data = $data->toArray();
            if (!isset($data['entityType']) || !isset($data['entityId'])) {
                return $this->notFoundAction();
            }
            $comment = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->addComment($form, $data);

            $flashMessenger = new FlashMessenger();
            if ($comment) {
                $flashMessenger->addSuccessMessage('Comment created');
                //TODO: redirect where?
                $url = $this->getRequest()->getHeader('Referer')->getUri();
                return $this->redirect()->toUrl($url);
            } else {
                $flashMessenger->addErrorMessage('Comment is not created');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'title' => 'Add comment', 'path' => $this->getRequest()->getUri()->getPath().'?'.$this->getRequest()->getUri()->getQuery()]);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }

        return $viewModel;
    }
}
