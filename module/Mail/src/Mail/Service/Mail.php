<?php

namespace Mail\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\Plugin\Url;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityNotFoundException;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use \Zend\Mime\Mime;
use Zend\Mail\AddressList;
use \User\Entity\User;

/**
 * Class Mail
 * @package Mail\Service
 */
class Mail
{
    /**
     * @var null|\Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @return null|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getObjectManager()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @param $alias
     * @return mixed
     * @throws EntityNotFoundException
     */
    public function getTemplate($alias)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        if (!$mailTemplate = $objectManager->getRepository('Mail\Entity\Mail')->findOneBy(['alias' => $alias])) {
            throw new EntityNotFoundException("Template by name '{$alias}' not found");
        }
        return $mailTemplate;
    }

    /**
     * @param \User\Entity\User $user
     * @throws EntityNotFoundException
     */
    public function signUpMail(User $user)
    {
        /** @var \Mail\Entity\Mail $template */
        $template = $this->getTemplate('sign-up');

        //prepare registration url
        $hostUrl = $this->getServiceLocator()->get('ViewHelperManager')->get('ServerUrl')->setPort(80)->__invoke();
        $url = $this->getServiceLocator()->get('ViewHelperManager')->get('Url')->__invoke(
            'user/default',
            ['controller' => 'signup', 'action' => 'confirm']
        );
        $confirmUrl = $hostUrl . $url . '/' . $user->getConfirm();

        //assign url to the template
        $this->assign($template, 'confirm', $confirmUrl);

        $message = $this->prepareMessage($template, $user);

        $this->send($message);
    }

    /**
     * @param \Mail\Entity\Mail $template
     * @param \User\Entity\User $user $user
     * @return \Zend\Mail\Message
     */
    public function prepareMessage($template, $user)
    {

        $message = $this->getServiceLocator()->get('mail.message');
        $message
            ->setFrom($template->getFromEmail(), $template->getFromName())
            ->setTo($user->getEmail())
            ->setSubject($template->getSubject())
            ->setBody($this->prepareBody($template));

        return $message;
    }

    /**
     * @param \Mail\Entity\Mail $template $template
     * @return MimeMessage
     */
    public function prepareBody($template)
    {
        $text = new MimePart($template->getBodyText());
        $text->type = Mime::TYPE_TEXT;
        $text->charset = "UTF-8";

        $html = new MimePart($template->getBodyHtml());
        $html->type = Mime::TYPE_HTML;
        $html->encoding = Mime::ENCODING_BASE64;
        $html->charset = "UTF-8";

        $body = new MimeMessage();
        $body->setParts([$text, $html]);

        return $body;
    }

    /**
     * @param $message
     * @return mixed
     */
    public function send($message)
    {
        $transport = $this->getServiceLocator()->get('mail.transport');
        return $transport->send($message);
    }

    /**
     * @param \Mail\Entity\Mail $template $template
     * @param $name
     * @param $value
     * @return mixed
     */
    public function assign($template, $name, $value)
    {
        //TODO use hydrator
        $template->setSubject(str_replace("%" . $name . "%", $value, $template->getSubject()));
        $template->setBodyHtml(str_replace("%" . $name . "%", $value, $template->getBodyHtml()));
        $template->setBodyText(str_replace("%" . $name . "%", $value, $template->getBodyText()));

        return $template;
    }
}
