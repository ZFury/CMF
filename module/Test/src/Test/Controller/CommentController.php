<?php
/**
 * Created by PhpStorm.
 * Date: 17.12.14
 * Time: 15:43
 */

namespace Test\Controller;

use Comment\Grid\Comment\Grid;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;
use Comment\Entity;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class CommentController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $entity = $objectManager->getRepository('Test\Entity\Test')->findAll()[0];
        $entityType = $objectManager->getRepository('Comment\Entity\EntityType')
            ->findOneByEntity('Test\\Entity\\Test');

        $entityId = $entity->getId();
        $entityAlias = $entityType->getAlias();

        $comments = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->tree(['alias' => $entityAlias, 'id' => $entityId]);

        $addCommentForm = null;

        if ($entityType->getIsEnabled()) {
            $addCommentForm = $this->getServiceLocator()->get('Comment\Service\Comment')->getAddCommentForm(
                $this->getServiceLocator()->get('Comment\Service\Comment')->createForm(),
                $entityId,
                $entityAlias
            );
        }
        $viewModel = new ViewModel([
            'testEntity' => $entity,
            'addCommentForm' => $addCommentForm,
            'comments' => $comments
        ]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel;
    }
}
