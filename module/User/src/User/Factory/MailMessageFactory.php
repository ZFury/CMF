<?php

namespace User\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Zend\Mail\Message;
use \Zend\Mail\Headers;

class MailMessageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $message = new Message();
        $headers = new Headers();
        $headers->addHeaders($config['mail']['message']['headers']);
        $message->setHeaders($headers)->setFrom($config['mail']['message']['from']);
        //uncomment this if you want send email around
        //$message->getHeaders()->addHeaderLine('EXTERNAL', 'true');

        return $message;
    }
}
