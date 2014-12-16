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

    }

    public function testAction()
    {
        $alias = 'mail1';

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        if (!$mailTemplate = $objectManager->getRepository('Mail\Entity\Mail')->findOneBy(['alias' => $alias])) {
            throw new EntityNotFoundException('Entity not found');
        }

        var_dump($mailTemplate);
        die();

    }
}
