<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Pages\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class IndexController
 * @package Pages\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     * @throws EntityNotFoundException
     */
    public function indexAction()
    {
        if (!$alias = $this->params()->fromRoute('alias')) {
            //TODO: fix exception
            throw new EntityNotFoundException('Bad Request');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        if (!$page = $objectManager->getRepository('Pages\Entity\Pages')->findOneBy(['alias' => $alias])) {
            throw new EntityNotFoundException('Entity not found');
        }

        return new ViewModel(array('page' => $page));
    }
}
