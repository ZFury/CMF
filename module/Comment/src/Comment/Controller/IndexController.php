<?php

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;

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
        /*if (isset($this->getRequest()->getQuery()->user_id)) {
            $comments = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->getCommentsByUserId($this->identity()->getUser()->getId());

            return new ViewModel(array('comments' => $comments));
        }*/
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
            $url = $this->getRequest()->getHeader('Referer')->getUri();
            return $this->redirect()->toUrl($url);

        }
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        /*$form = new Form\AddForm(null, $this->getServiceLocator());
        $result = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->editCommentById($form,(int)$this->params()->fromRoute('id'));*/
        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        if (!($this->getRequest()->getQuery()->entity) || !($entityId = intval($this->getRequest()->getQuery()->id))) {
            throw new \Exception('Wrong query string');
        }

        $et = $this->getServiceLocator()->get('Comment\Service\EntityType');
        $entityType = $et->get($this->getRequest()->getQuery()->entity);
        if (!$entityType) {
            throw new \Exception('Unknown entity');
        }

        $form = new Form\Add(null, $this->getServiceLocator());
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $user = $this->identity()->getUser();

            $comment = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->addComment($form, $data, $entityType, $entityId, $user);
        }

        return new ViewModel(['form' => $form]);
    }
}
