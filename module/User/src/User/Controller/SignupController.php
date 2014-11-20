<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form;
use User\Service;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class SignupController extends AbstractActionController
{
    public function indexAction()
    {
        $form = new Form\SignupForm(null, $this->getServiceLocator());

        if ($this->getRequest()->isPost()) {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                /** @var \User\Entity\User $user */
                $user = $this->getServiceLocator()->get('User\Entity\User');
                $objectManager->getConnection()->beginTransaction();
                try {
                    $hydrator = new DoctrineHydrator($objectManager);
                    $hydrator->hydrate($form->getData(), $user);
                    $user->setDisplayName($user->getEmail());
                    $user->setRole($user::ROLE_USER);
                    $user->setConfirm($user->generateConfirm());
                    $user->setStatus($user::STATUS_UNCONFIRMED);

                    $objectManager->persist($user);
                    $objectManager->flush();

                    $html = $this->forward()->dispatch('User\Controller\Mail', array('action' => 'signup', 'user' => $user));

                    /** @var $authService Service\Auth */
                    $authService = $this->getServiceLocator()->get('User\Service\Auth');
                    $authService->generateEquals($user, $data['password']);

                    /** @var $userService Service\User */
                    $userService = $this->getServiceLocator()->get('User\Service\User');
                    $userService->signupMail($user, $html);

                    $objectManager->getConnection()->commit();

                    $this->flashMessenger()->addSuccessMessage('You must confirm your email address to complete registration');

                    return $this->redirect()->toRoute('home');

                } catch (\Exception $e) {
                    $objectManager->getConnection()->rollback();
                    throw $e;
                }

            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function confirmAction()
    {
        $confirm = $this->params('confirm');
        try {
            if (!$confirm) {
                //$this->getResponse()->setStatusCode(404);
                throw new \Exception('Invalid confirmation code');
            }
            /** @var \Doctrine\ORM\EntityManager $objectManager */
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $user = $objectManager
                ->getRepository('User\Entity\User')
                ->findOneBy(array('confirm' => $confirm));
            if (!$user) {
                throw new \Exception('Invalid confirmation code');
            }
            $user->activate();
            $objectManager->persist($user);
            $objectManager->flush();
            $this->flashMessenger()->addSuccessMessage("You've successfully activated your account");
        } catch (\Exception $exception) {
            $this->flashMessenger()->addErrorMessage($exception->getMessage());
        }

        return $this->redirect()->toRoute('home');
    }
}
