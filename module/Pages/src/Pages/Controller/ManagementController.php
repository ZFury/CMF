<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Pages\Controller;

use Starter\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use Pages\Form\Create;
use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ManagementController
 * @package Pages\Controller
 */
class ManagementController extends AbstractCrudController
{
    /**
     * @return mixed|\Pages\Entity\Pages
     */
    protected function getEntity()
    {
        return new \Pages\Entity\Pages();
    }

    /**
     * @return mixed|Create
     */
    protected function getCreateForm()
    {
        return new \Pages\Form\Create(null, ['serviceLocator' =>$this->getServiceLocator()]);
    }

    /**
     * @return mixed|Create
     */
    protected function getEditForm()
    {
        $form = new \Pages\Form\Create(null, ['serviceLocator' =>$this->getServiceLocator()]);
        $form->get('submit')->setValue('Save');
        return $form;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $pages = $objectManager->getRepository('Pages\Entity\Pages')->findAll();

        return new ViewModel(
            array(
                'pages' => $pages
            )
        );
    }

    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        return new ViewModel(array('page' => $this->loadEntity()));
    }
}
