<?php

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        if (isset($this->getRequest()->getQuery()->entity_type) && isset($this->getRequest()->getQuery()->entity_id)) {
            $comments = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->getCommentsByEntityId($this->getRequest()->getQuery()->entity_type, $this->getRequest()->getQuery()->entity_id);

            return new ViewModel(array('comments' => $comments));
        }
        if (isset($this->getRequest()->getQuery()->user_id)) {
            $comments = $this->getServiceLocator()
                ->get('Comment\Service\Comment')
                ->getCommentsByUserId($this->identity()->getUser()->getId());

            return new ViewModel(array('comments' => $comments));
        }
        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if (!(int)$this->params()->fromRoute('id')) {
            return $this->redirect()->toRoute('home');
        }

        $result = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->deleteCommentById((int)$this->params()->fromRoute('id'));

        if ($result) {
            $this->flashMessenger()->addSuccessMessage('Comment deleted');
            $url = $this->getRequest()->getHeader('Referer')->getUri();
            return $this->redirect()->toUrl($url);

        } else {
            $this->flashMessenger()->addErrorMessage('Comment has been deleted');
            return $this->redirect()->toRoute('home');
        }
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        if (isset($this->getRequest()->getQuery()->entityType) && isset($this->getRequest()->getQuery()->entityId)) {
            $et = $this->getServiceLocator()->get('Comment\Service\EntityType');
            $entityType = $et->getEntityType($this->getRequest()->getQuery()->entityType);
            if ($entityType) {
                $form = new Form\AddForm(null, $this->getServiceLocator());
                $form->setEntityType($entityType->getEntityType());
                $form->setEntityId($this->getRequest()->getQuery()->entityId);

                if ($this->getRequest()->isPost()) {
                    $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                    $form->setData($this->getRequest()->getPost());
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $data['userId'] = $this->identity()->getUser()->getId();
                        $comment = new \Comment\Entity\Comment();

                        $objectManager->getConnection()->beginTransaction();

                        try {
                            $hydrator = new DoctrineHydrator($objectManager);
                            $hydrator->hydrate($data, $comment);
                            $comment->updatedTimestamps();

                            $objectManager->persist($comment);
                            $objectManager->flush();

                            $objectManager->getConnection()->commit();

                            $this->flashMessenger()->addSuccessMessage('Comment added');

                            return $this->redirect()->toRoute('home');

                        } catch (\Exception $e) {
                            $objectManager->getConnection()->rollback();
                            throw $e;
                        }
                    }
                }

                return new ViewModel(['form' => $form]);
            } else {
                $this->flashMessenger()->addErrorMessage('Еhis entity can not comment');
                return $this->redirect()->toRoute('home');
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Wrong query string');
            return $this->redirect()->toRoute('home');
        }
    }
}
