<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11/26/14
 * Time: 2:35 PM
 */

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout('layout/dashboard/dashboard');
    }
}
