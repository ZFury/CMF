<?php

namespace Categories\Service;

use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceManager;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

/**
 * Class Categories
 * @package Categories\Service
 */
class Categories
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

//    /**
//     * @param \User\Entity\User $user
//     * @param $content
//     */
//    public function signupMail(\User\Entity\User $user, $content)
//    {
//        $transport = $this->getServiceLocator()->get('mail.transport');
//
//        $text = new MimePart($content);
//        $text->type = \Zend\Mime\Mime::TYPE_TEXT;
//
////        $html = new MimePart($content);
////        $html->type = \Zend\Mime\Mime::TYPE_HTML;
//
//        $body = new MimeMessage();
//        $body->setParts([$text]);
//
//        /** @var \Zend\Mail\Message $message */
//        $message = $this->getServiceLocator()->get('mail.message');
//        $message
//            ->addTo($user->getEmail())
//            ->setSubject("Sign up")
//            ->setBody($body);
//
//        return $transport->send($message);
//    }
}
