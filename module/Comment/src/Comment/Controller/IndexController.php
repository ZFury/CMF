<?php

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;

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

        $data = null;

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
        }

        $form = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->editCommentById($id, $data);

        return new ViewModel(['form' => $form]);
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

        $user = $this->identity()->getUser();
        $data = null;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
        }
        $form = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->addComment($data, $entityType, $entityId, $user);
        return new ViewModel(['form' => $form]);
    }
}
