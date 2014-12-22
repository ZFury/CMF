<?php

namespace User\Service;

use User\Form\SetNewPasswordForm;
use Zend\ServiceManager\ServiceManager;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use \Zend\Mime\Mime;
use User\Form\SignupForm;
use User\Form\ForgotPasswordForm;
use User\Entity;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 14.08.14
 * Time: 15:27
 */
class User
{
    /**
     * @var null|\Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @return null|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @param \User\Entity\User $user
     * @param $content
     */
    public function signupMail(\User\Entity\User $user, $content)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');

        $text = new MimePart($content);
        $text->type = Mime::TYPE_TEXT;
        $text->charset = "UTF-8";

        $html = new MimePart($content);
        $html->type = Mime::TYPE_HTML;
        $html->encoding = Mime::ENCODING_BASE64;
        $html->charset = "UTF-8";

        $body = new MimeMessage();
        $body->setParts([$text, $html]);

        /**
         * @var \Zend\Mail\Message $message
         */
        $message = $this->getServiceLocator()->get('mail.message');
        $message
            ->addTo($user->getEmail())
            ->setSubject("Sign up")
            ->setBody($body);

        return $transport->send($message);
    }

    /**
     * @param Entity\User $user
     * @param $content
     * @return mixed
     */
    public function forgotPasswordpMail(\User\Entity\User $user, $content)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');

        $text = new MimePart($content);
        $text->type = Mime::TYPE_TEXT;
        $text->charset = "UTF-8";

        $html = new MimePart($content);
        $html->type = Mime::TYPE_HTML;
        $html->encoding = Mime::ENCODING_BASE64;
        $html->charset = "UTF-8";

        $body = new MimeMessage();
        $body->setParts([$text, $html]);

        /**
         * @var \Zend\Mail\Message $message
         */
        $message = $this->getServiceLocator()->get('mail.message');
        $message
            ->addTo($user->getEmail())
            ->setSubject("Password recovery")
            ->setBody($body);

        return $transport->send($message);
    }

    /**
     * @param SignupForm $form
     * @return Entity\User
     * @throws \Exception
     */
    public function create(SignupForm $form)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $data = $form->getData();
        $user = new Entity\User();
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

            /**
             * @var $authService \User\Service\Auth
             */
            $authService = $this->getServiceLocator()->get('User\Service\Auth');
            $authService->generateEquals($user, $data['password']);

            //use module mail for user registration
            $config = $this->getServiceLocator()->get('config');
            if (isset($config['mailOptions'])) {
                $mailOptions = $this->getServiceLocator()->get('config')['mailOptions'];
            }
            if (isset($mailOptions['useModuleMail']) && $mailOptions['useModuleMail'] = true) {
                $mailService = $this->getServiceLocator()->get('Mail\Service\Mail');
                $mailService->signUpMail($user);
            } else {
                $html = $this->getServiceLocator()->get('ControllerPluginManager')->get('forward')
                    ->dispatch('User\Controller\Mail', array('action' => 'signup', 'user' => $user));
                $this->signupMail($user, $html);
            }

            $objectManager->getConnection()->commit();
        } catch (\Exception $exception) {
            $objectManager->getConnection()->rollback();
            throw $exception;
        }

        return $user;
    }

    /**
     * @param ForgotPasswordForm $form
     */
    public function forgotPassword(ForgotPasswordForm $form)
    {
        $data = $form->getData();
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /** @var Entity\User $user */
        $user = $objectManager
            ->getRepository('User\Entity\User')
            ->findOneBy(array('email' => $data['email']));
        $user->setConfirm($user->generateConfirm());
        $objectManager->persist($user);
        $objectManager->flush();

        //use module mail for user registration
        $config = $this->getServiceLocator()->get('config');
        if (isset($config['mailOptions'])) {
            $mailOptions = $this->getServiceLocator()->get('config')['mailOptions'];
        }
        if (isset($mailOptions['useModuleMail']) && $mailOptions['useModuleMail'] = true) {
            /** @var \Mail\Service\Mail $mailService */
            $mailService = $this->getServiceLocator()->get('Mail\Service\Mail');
            $mailService->forgotPassword($user);
        } else {
            $html = $this->getServiceLocator()->get('ControllerPluginManager')->get('forward')
                ->dispatch('User\Controller\Mail', array('action' => 'forgot-password', 'user' => $user));

            $this->forgotPasswordpMail($user, $html);
        }
    }

    /**
     * @param SetNewPasswordForm $form
     * @return Entity\User
     * @throws \Exception
     */
    public function changePassword(\User\Entity\User $user, SetNewPasswordForm $form)
    {
        /** @var \User\Service\Auth $userAuth */
        $userAuth = $this->getServiceLocator()->get('\User\Service\Auth');
        $userAuth->generateEquals($user, $form->getData()['password']);

        return $user;
    }
}
