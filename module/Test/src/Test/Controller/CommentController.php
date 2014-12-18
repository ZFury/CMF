<?php
/**
 * Created by PhpStorm.
 * User: Lopay
 * Date: 17.12.14
 * Time: 15:43
 */

namespace Test\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\View\Model\ViewModel;
use Comment\Form;
use Comment\Service;
use Comment\Form\Filter;
use DoctrineModule\Validator;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class CommentController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entities = $objectManager->getRepository('Test\Entity\Test')->findAll();

        return new ViewModel(array('data' => $entities));
    }
}
