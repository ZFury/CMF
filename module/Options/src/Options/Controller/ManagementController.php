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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Options\Form\Create;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

use Doctrine\ORM\EntityManager;

class ManagementController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $options = $objectManager->getRepository('Options\Entity\Options')->findAll();

        return new ViewModel(array(
            'options' => $options
        ));
    }

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
            ->findOneBy(array('namespace' => $namespace, 'key' => $key));

        return new ViewModel(
            array('option' => $option)
        );
    }

    public function createAction()
    {
        $form = new Create($this->getServiceLocator());

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

        return new ViewModel( array(
                'form' => $form
            ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        $namespace = $this->params()->fromRoute('namespace');
        $key = $this->params()->fromRoute('key');

        if (!$namespace || !$key) {
            return $this->notFoundAction();
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->findOneBy(array('namespace' => $namespace, 'key' => $key));

        $form = new Edit($this->getServiceLocator(), $option);

        if ($this->getRequest()->isPost()) {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
//                $data = $form->getData();

                $objectManager->getConnection()->beginTransaction();
                try {
                    $hydrator = new DoctrineHydrator($objectManager);
                    $hydrator->hydrate($form->getData(), $option);

//                    $option->setNamespace($data['namespace']);
//                    $option->setKey($data['key']);
//                    $option->setValue($data['value']);
//                    $option->setDescription($data['description']);
                    $option->setUpdated(new \DateTime(date('Y-m-d H:i:s')));

                    $objectManager->persist($option);
                    $objectManager->flush();

                    $objectManager->getConnection()->commit();

                    $this->flashMessenger()->addSuccessMessage('Option was successfully updated');

                    return $this->redirect()->toRoute('options');

                } catch (\Exception $e) {
                    $objectManager->getConnection()->rollback();
                    throw $e;
                }

            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function deleteAction()
    {
        $namespace = $this->params()->fromRoute('namespace');
        $key = $this->params()->fromRoute('key');

        if (!$namespace || !$key) {
            return $this->redirect()->toRoute('options');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->findOneBy(array('namespace' => $namespace, 'key' => $key));

        $objectManager->remove($option);
        $objectManager->flush($option);

        $this->flashMessenger()->addSuccessMessage('Option was successfully deleted');

        return $this->redirect()->toRoute('options');
    }
}
