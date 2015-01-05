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
        $user = $this->getEntity();
        $form = $this->getCreateForm();
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($user);
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter(new Form\Filter\CreateInputFilter($this->getServiceLocator()));
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
        $user = $this->loadEntity();
        $form = $this->getEditForm();
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($user);
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter(new Form\Filter\EditInputFilter($this->getServiceLocator()));
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();
                $authService = new Service\Auth($this->getServiceLocator());
                if ($form->get('password')->getValue()) {
                    $authService->generateEquals($user, $form->get('password')->getValue());
                }

                return $this->redirect()->toRoute(null, ['controller' => 'management']);
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
        return new Entity\User();
    }

    public function getCreateForm()
    {
        return new Form\CreateForm();
    }

    public function getEditForm()
    {
        return new Form\EditForm();
    }
}
