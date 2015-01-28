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
        $testEntity = $objectManager->getRepository('Test\Entity\Test')->findOneByName('testuser');
        /**
         * @var Entity\EntityType $entityType
         */
        if (!$entityType = $objectManager->getRepository('Comment\Entity\EntityType')
            ->getEntityTypeByEntity('Test\\Entity\\Test')
        ) {
            $this->flashMessenger()
                ->addErrorMessage('You cannot comment this entity! Create it in Dashboard-Comment-CreateEntity');
        }
        $comments = $this->getServiceLocator()
            ->get('Comment\Service\Comment')
            ->listComments($testEntity->toArray());

        $addCommentForm = null;
        if ($entityType->isEnabled() !== 0) {
            $addCommentForm = $this->getServiceLocator()->get('Comment\Service\Comment')->getAddCommentForm(
                $this->getServiceLocator()->get('Comment\Service\Comment')->createForm(),
                $testEntity->getId(),
                $testEntity->getAlias()
            );
        }
        $ViewModel = new ViewModel(array('testEntity' => $testEntity));

        if ($entityType) {
            $ViewModel->setVariables([
                'isVisible' => $entityType->isVisible(),
                'addCommentForm' => $addCommentForm,
                'comments' => $comments
            ]);
        }
        return $ViewModel;
    }
}
