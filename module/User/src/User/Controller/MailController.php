<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 15.08.14
 * Time: 16:18
 */
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MailController extends AbstractActionController
{
    public function signupAction()
    {
        $user = $this->params('user');
        $model = new ViewModel(['user' => $user]);
        $model->setTemplate('user/mail/signup');
        $html = $this->getServiceLocator()->get('ViewRenderer')->render($model);

        return $html;
    }
}