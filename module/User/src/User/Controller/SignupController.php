<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form;
use User\Service;

class SignupController extends AbstractActionController
{
    /**
     * Sign Up action
     *
     * @return array|\Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $form = new Form\SignupForm('create-user', ['serviceLocator' => $this->getServiceLocator()]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $userService = new Service\User($this->getServiceLocator());
                try {
                    $user = $userService->create($form);
                    $this->flashMessenger()->addSuccessMessage(
                        'You must confirm your email address to complete registration'
                    );

                    return $this->redirect()->toRoute('home');
                } catch (\Exception $exception) {
                    throw $exception;
                }
            }
        }

        return new ViewModel(['form' => $form, 'serviceLocator' => $this->getServiceLocator()]);
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function forgotPasswordAction()
    {
        $form = new Form\ForgotPasswordForm('forgot-password', ['serviceLocator' => $this->getServiceLocator()]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $userService = new Service\User($this->getServiceLocator());
                try {
                    $userService->forgotPassword($form);
                    $this->flashMessenger()->addSuccessMessage(
                        'The confirmation email to reset your password is sent. Please check your email.'
                    );

                    return $this->redirect()->toRoute('home');
                } catch (\Exception $exception) {
                    throw $exception;
                }
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Confirm email
     *
     * @return \Zend\Http\Response
     */
    public function confirmAction()
    {
        $confirm = $this->params('confirm');
        try {
            if (!$confirm) {
                //$this->getResponse()->setStatusCode(404);
                throw new \Exception('Invalid confirmation code');
            }
            /**
             * @var \Doctrine\ORM\EntityManager $objectManager
             */
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
