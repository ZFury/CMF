<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Options\Controller;

use Options\Form\Edit;
use Starter\Mvc\Controller\AbstractCrudController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Options\Form\Create;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityNotFoundException;

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
        return new \Options\Form\Create(null, ['serviceLocator' =>$this->getServiceLocator()]);
    }

    /**
     * @return mixed|Create
     */
    protected function getEditForm()
    {
        return new \Options\Form\Create(null, ['serviceLocator' =>$this->getServiceLocator()]);
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

        if (!$model = $objectManager->getRepository(get_class($this->getEntity()))
            ->find(['namespace' => $namespace, 'key' => $key])) {
            throw new EntityNotFoundException('Entity not found');
        }
        return $model;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $options = $objectManager->getRepository('Options\Entity\Options')->findAll();

        return new ViewModel(
            array(
                'options' => $options
            )
        );
    }

    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $namespace = $this->params()->fromRoute('namespace');
        $key = $this->params()->fromRoute('key');
        //        $namespace = $this->params()->fromQuery('namespace');
        //        $key = $this->params()->fromQuery('key');

        if (!$namespace || !$key) {
            return $this->notFoundAction();
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->find(array('namespace' => $namespace, 'key' => $key));

        return new ViewModel(
            array('option' => $option)
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function createAction()
    {
        $form = new Create('create', ['serviceLocator' => $this->getServiceLocator()]);
        $form->get('namespace')->setValue(\Options\Entity\Options::NAMESPACE_DEFAULT);

        if ($this->getRequest()->isPost()) {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                /** @var \Options\Entity\Options $option */
                $option = $this->getServiceLocator()->get('Options\Entity\Options');
                $objectManager->getConnection()->beginTransaction();
                try {
                    $hydrator = new DoctrineHydrator($objectManager);

                    $hydrator->hydrate($form->getData(), $option);

                    $option->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $option->setUpdated(new \DateTime(date('Y-m-d H:i:s')));

                    $form->bind($option);

                    $objectManager->persist($option);
                    $objectManager->flush();

                    $objectManager->getConnection()->commit();

                    $this->flashMessenger()->addSuccessMessage('Option was successfully created');

                    return $this->redirect()->toRoute('options');

                } catch (\Exception $e) {
                    $objectManager->getConnection()->rollback();
                    throw $e;
                }

            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }
}
