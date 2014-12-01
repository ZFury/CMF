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
use Zend\View\Model\JsonModel;
use Starter\Mvc\Grid\Grid;
use Starter\Mvc\Controller\AaaController;


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

    /**
     * Grid action
     *
     * @return \Zend\View\Model\ViewModel
     *
     * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
     */
    public function gridAction()
    {
        return new ViewModel();
    }

    /**
     * Get users action
     *
     * @return \Zend\View\Model\JsonModel
     *
     * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
     */
    public function getUsersAction()
    {
        /* @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $data = array();
        $count = null;

        if ($request->isPost()) {
            $params = $request->getPost('data');
            $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $source = $em->createQueryBuilder()->select(array("u.id, u.email"))
                ->from('\User\Entity\User', 'u');
            $grid = new Grid($source);

            if (isset($params['page'])) {
                $grid->setPage($params['page']);
            }
            if (isset($params['limit'])) {
                $grid->setPage($params['limit']);
            }
//            if (isset($params['order'])) {
//                $grid->setPage($params['order']);
//            }
            $data = $grid->getData();
            /* @var \User\Repository\User $usersManager */
            $usersManager = $em->getRepository('User\Entity\User');
            $count = $usersManager->countUsers();
        }
        return new JsonModel(array(
            'data' => $data,
            'count' => $count
        ));
    }
}