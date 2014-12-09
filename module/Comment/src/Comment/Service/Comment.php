<?php

namespace Comment\Service;

use Zend\ServiceManager\ServiceManager;
use Comment\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Comment\Service;
use Comment\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Zend\Stdlib\Hydrator;

class Comment
{
    /**
     * @var null|\Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @return null|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @param \Zend\Form\Form $form
     * @param array $data
     * @return \Comment\Entity\Comment
     * @throws \Exception
     */
    public function addComment(\Zend\Form\Form $form, array $data)
    {
        $user = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity()->getUser();
        $data['user'] = $user;

        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $comment = new \Comment\Entity\Comment();
        $form->setData($data);

        if ($form->isValid()) {

            $data = $form->getData();

            $et = $this->getServiceLocator()->get('Comment\Service\EntityType');
            $entityType = $et->get($data['entityType']);
            if (!$entityType) {
                throw new \Exception('Unknown entity');
            }

            $entityManager->getConnection()->beginTransaction();
            try {
                $hydrator = new DoctrineHydrator($entityManager);
                $hydrator->hydrate($data, $comment);
                //$comment->setUser($user);
                $entityManager->persist($comment);
                $entityManager->flush();
                $entityManager->getConnection()->commit();

                return $comment;

            } catch (\Exception $e) {
                $entityManager->getConnection()->rollback();
                throw $e;
            }
        }
    }

    /**
     * @return \Zend\Form\Form
     */
    public function createForm()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $builder = new AnnotationBuilder($entityManager);
        $form = $builder->createForm(new \Comment\Entity\Comment());

        return $form;
    }

    /**
     * @param $entityType
     * @param $entityId
     * @param null $userId
     * @return array
     */
    public function getCommentsByEntityId($entityType, $entityId, $userId = null)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        if (isset($userId)) {
            $comments = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $entityId, 'userId' => $userId));
        } else {
            $comments = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $entityId));
        }

        $arrayComments = array();
        foreach ($comments as $comment) {
            $arrayComments[$comment->getId()]['comment'] = $comment;
            $arrayComments[$comment->getId()]['childs'] = self::getCommentsByEntityId('comment', $comment->getId());
        }
        return $arrayComments;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteCommentById($id)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $objectManager->getConnection()->beginTransaction();
        $comment = $objectManager->find('Comment\Entity\Comment', $id);

        if (!$comment) {
            throw new \Exception("Attempt to remove comments that do not exist");
        }
        try {
            $objectManager->remove($comment);
            $objectManager->flush();
            $objectManager->getConnection()->commit();
        } catch (\Exception $e) {
            $objectManager->getConnection()->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * @param $id
     * @return \Zend\Form\Form
     * @throws \Exception
     */
    public function createEditForm($id)
    {
        $form = $this->createForm();
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        if (!$model = $entityManager->getRepository('\Comment\Entity\Comment')->find($id)) {
            throw new \Exception('Comment not found');
        }
        $form->setHydrator(new DoctrineHydrator($entityManager));
        $form->bind($model);
        return $form;
    }

    /**
     * @param \Zend\Form\Form $form
     * @param $data
     * @return mixed
     */
    public function editCommentById(\Zend\Form\Form $form, $data)
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $hydrator = new DoctrineHydrator($entityManager);
        $hydrator->hydrate($data, $form->getObject());

        if ($form->isValid()) {
            $entityManager->persist($form->getObject());
            $entityManager->flush();
            return $form->getObject();
        }
    }
}
