<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 04.09.14
 * Time: 12:37
 */
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Service;
use User\Entity;
use User\Form;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ManagementController extends AbstractActionController
{

    public function createAction()
    {
        //not implemented yet
//        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
//        $user = new Entity\User();
//        $builder = new AnnotationBuilder($entityManager);
//
//        $form = $builder->createForm($user);
//        $form->setHydrator(new DoctrineHydrator($entityManager));
//        $form->bind($user);
//        if ($this->getRequest()->isPost()) {
//            //$form->setInputFilter(new Form\CreateInputFilter($this->getServiceLocator()));
//            $form->setData($this->getRequest()->getPost());
//            if ($form->isValid()) {
//                $salt = md5(microtime(false) . rand(11111, 99999));
//                $user->setSalt($salt);
//                $user->setPassword(Service\User::encrypt($user, $user->getPassword()));
//                $entityManager->persist($user);
//                $entityManager->flush();
//            }
//        }
//
//        return new ViewModel([
//            'form' => $form
//        ]);
    }
}