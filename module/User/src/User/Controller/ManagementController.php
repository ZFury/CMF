<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 04.09.14
 * Time: 12:37
 */
namespace User\Controller;

use SebastianBergmann\Exporter\Exception;
use Starter\Mvc\Controller\AbstractCrudController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Service;
use User\Entity;
use User\Form;
use User\Grid\Grid;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\View\Model\JsonModel;

class ManagementController extends AbstractCrudController
{

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $viewModel = new ViewModel(['grid' => $grid]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        return $viewModel;
    }

    public function createAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = new Entity\User();
        $form = new Form\CreateForm();
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($user);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();
                $authService = new Service\Auth($this->getServiceLocator());
                $authService->generateEquals($user, $form->get('password')->getValue());

                return $this->redirect()->toRoute(null, ['controller' => 'management']);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }


    public function editAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = new Entity\User();
        $builder = new AnnotationBuilder($entityManager);

        $form = $builder->createForm($user);
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($user);
        if ($this->getRequest()->isPost()) {
            //$form->setInputFilter(new Form\CreateInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $salt = md5(microtime(false) . rand(11111, 99999));
                $user->setSalt($salt);
                $user->setPassword(Service\User::encrypt($user, $user->getPassword()));
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
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
        /* @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $count = null;
        $searchString = '';
        if ($request->isXmlHttpRequest()) {
            $sm = $this->getServiceLocator();
            $grid = new Grid($sm);
            $grid->init();
            $data = $grid->getData();
            $em = $sm->get('Doctrine\ORM\EntityManager');
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

    public function getEntity()
    {
        return null;
    }

    public function getCreateForm()
    {
        return null;
    }

    public function getEditForm()
    {
        return null;
    }


}
