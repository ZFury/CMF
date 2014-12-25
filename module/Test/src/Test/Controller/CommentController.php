<?php
/**
 * Created by PhpStorm.
 * User: Lopay
 * Date: 17.12.14
 * Time: 15:43
 */

namespace Test\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;
use Comment\Entity;

class CommentController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entities = $objectManager->getRepository('Test\Entity\Test')->findAll();
        /**
         * @var /Comment\Entity\EntityType $entityTest
         */
        if (!$entityTest = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityTypeByEntity('Test\\Entity\\Test')) {
            throw new \Exception('Comment on this entity can not be');
        }
        return new ViewModel(array('data' => $entities, 'aliasEntity' => $entityTest->getAliasEntity()));
    }
}
