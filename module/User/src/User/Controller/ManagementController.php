<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 04.09.14
 * Time: 12:37
 */
namespace User\Controller;

use SebastianBergmann\Exporter\Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Service;
use User\Entity;
use User\Form;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\View\Model\JsonModel;
use Starter\Mvc\Grid\Grid;

class ManagementController extends AbstractActionController
{

//    public function indexAction()
//    {
//
//    }

//    public function createAction()
//    {
//        not implemented yet
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

    /**
     * Grid action
     *
     * @return \Zend\View\Model\ViewModel
     *
     * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
     */
    public function gridAction()
    {
        /* @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $count = null;
        $searchString = '';
        if ($request->isXmlHttpRequest()) {
            $params = $request->getPost('data');
            if (!isset($params['page']) && !isset($params['limit'])) {
                throw new Exception('Bad request');
            }
            $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $source = $em->createQueryBuilder()->select(array("u.id, u.email"))
                ->from('\User\Entity\User', 'u');
            $grid = new Grid($source);
            $grid->setPage($params['page']);
            $grid->setLimit($params['limit']);
            if (isset($params['field']) && isset($params['reverse'])) {
                $field = 'u.' . $params['field'];
                $order = $params['reverse'];
            } else {
                $field = 'u.id';
                $order = $grid::ORDER_ASC;
            }
            $grid->setOrder(['field' => $field, 'order' => $order]);
            if (isset($params['searchString']) && isset($params['searchField'])) {
                $searchField = 'u.' . $params['searchField'];
                $searchString = $params['searchString'];
                $grid->setFilter(['filterField' => $searchField, 'searchString' => $searchString]);
            }
            $data = $grid->getData();
            /* @var \User\Repository\User $usersManager */
            $usersManager = $em->getRepository('User\Entity\User');
            $count = $usersManager->countSearchUsers($searchString);
            return new JsonModel(array(
                'data' => $data,
                'count' => $count
            ));
        } else {
            return new ViewModel();
        }
    }
}
