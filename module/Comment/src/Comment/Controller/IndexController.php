<?php

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {

        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        $form = new Form\AddForm(null, $this->getServiceLocator());
        $form->setEntityType($this->getRequest()->getQuery()->entityType);
        $form->setEntityId($this->getRequest()->getQuery()->entityId);

        //$type = $this->getRequest()->getPost('type');
        //$form->getElement('type')->setValue($type);
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
//                    var_dump($comment);
//                    die();
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
    }

}
