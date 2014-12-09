<?php

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        if (isset($this->getRequest()->getQuery()->entity) && isset($this->getRequest()->getQuery()->id)) {
            $comments = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->getCommentsByEntityId($this->getRequest()->getQuery()->entity, $this->getRequest()->getQuery()->id);

            return new ViewModel(array('comments' => $comments));
        }
        return new ViewModel();
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
            if($comment) {
                $flashMessenger->addSuccessMessage('Comment edited');
            } else {
                $flashMessenger->addErrorMessage('Comment is not changed');
            }
        }
        return new ViewModel(['form' => $form]);
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
            $data->set('entityType', $this->getRequest()->getQuery()->entity); // for GET (or query string) data
            /*for GET (or query string) data*/
            if (!($this->getRequest()->getQuery()->entity) || !($entityId = intval($this->getRequest()->getQuery()->id))) {
                throw new \Exception('Wrong query string');
            }
            $data->set('entityId', $this->getRequest()->getQuery()->id);
            $data = $data->toArray();

            $comment = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->addComment($form, $data);

            $flashMessenger = new FlashMessenger();
            if($comment) {
                $flashMessenger->addSuccessMessage('Comment created');
            } else {
                $flashMessenger->addErrorMessage('Comment is not created');
            }
        }

        return new ViewModel(['form' => $form, 'title' => 'Add comment']);
    }
}
