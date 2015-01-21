<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Options\Controller;

use Options\Grid\Grid;
use Fury\Mvc\Controller\AbstractCrudController;
use Zend\View\Model\ViewModel;
use Options\Form\Create;
use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ManagementController
 * @package Options\Controller
 */
class ManagementController extends AbstractCrudController
{
    /**
     * @return mixed|\Options\Entity\Options
     */
    protected function getEntity()
    {
        return new \Options\Entity\Options();
    }

    /**
     * @return mixed|Create
     */
    protected function getCreateForm()
    {
        return new \Options\Form\Create(null, ['serviceLocator' => $this->getServiceLocator()]);
    }

    /**
     * @return mixed|Create
     */
    protected function getEditForm()
    {
        $form = new \Options\Form\Create(null, ['serviceLocator' => $this->getServiceLocator()]);
        $form->get('submit')->setValue('Save');
        return $form;
    }

    /**
     * @return mixed
     * @throws EntityNotFoundException
     */
    protected function loadEntity()
    {
        $namespace = $this->params()->fromRoute('namespace');
        $key = $this->params()->fromRoute('key');

        if (!$namespace || !$key) {
            throw new EntityNotFoundException('Bad Request');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if (
        !$model = $objectManager
            ->getRepository(get_class($this->getEntity()))->find(['namespace' => $namespace, 'key' => $key])
        ) {
            throw new EntityNotFoundException('Entity not found');
        }
        return $model;
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

    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        return new ViewModel(
            array('option' => $this->loadEntity())
        );
    }
}
