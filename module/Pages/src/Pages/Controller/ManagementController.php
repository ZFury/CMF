<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Pages\Controller;

use Pages\Grid\Grid;
use Starter\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use Pages\Form\Create;
use Zend\Mvc\MvcEvent;
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
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    /**
     * @return mixed|\Pages\Entity\Pages
     */
    protected function getEntity()
    {
        /**
         * @var $entity = \Pages\Entity\Pages $entity
         */
        $entity = new \Pages\Entity\Pages();
        $entity->setAuthorId($this->identity()->getUser()->getId());
        return $entity;
    }

    /**
     * @return mixed|Create
     */
    protected function getCreateForm()
    {
        return new \Pages\Form\Create(null, ['serviceLocator' => $this->getServiceLocator()]);
    }

    /**
     * @return mixed|Create
     */
    protected function getEditForm()
    {
        $form = new \Pages\Form\Create(null, ['serviceLocator' => $this->getServiceLocator()]);
        $form->get('submit')->setValue('Save');
        return $form;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $grid = new Grid($sm);
        $viewModel = new ViewModel(['grid' => $grid]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        return $viewModel;
    }
}
