<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mail\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class IndexController
 * @package Mail\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     * @throws EntityNotFoundException
     */
    public function indexAction()
    {
        $text = 'sfsdf sdfsdf sdfsdf sdf sdf %some%';
        $name =  'some';
        $value = '77777777777';
        $result = str_replace("%" . $name . "%", $value, $text);

        var_dump($result);
        die();
    }

    public function testAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toUrl('/login');
        }
        $id = $this->identity()->getUser()->getId();
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        if (!$user = $objectManager->getRepository('User\Entity\User')->find($id)) {
            throw new EntityNotFoundException('Entity not found');
        }

        /** @var \Mail\Service\Mail $mailService */
        $mailService = $this->getServiceLocator()->get('Mail\Service\Mail');
        var_dump($mailService->signUpMail($user));
        die();
    }
}
