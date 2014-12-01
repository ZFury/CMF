<?php

namespace User\Service;

use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceManager;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

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
        $text->type = \Zend\Mime\Mime::TYPE_TEXT;
        $text->charset = "UTF-8";

        $html = new MimePart($content);
        $html->type = \Zend\Mime\Mime::TYPE_HTML;
        $html->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
        $html->charset  = "UTF-8";

        $body = new MimeMessage();
        $body->setParts([$text, $html]);

        /** @var \Zend\Mail\Message $message */
        $message = $this->getServiceLocator()->get('mail.message');
        $message
            ->addTo($user->getEmail())
            ->setSubject("Sign up")
            ->setBody($body);

        return $transport->send($message);
    }
}
