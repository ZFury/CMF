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
use Comment\Entity;

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
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /**
         * @var \Comment\Entity\EntityType $entity
         */
        if (!$entity = $entityManager->getRepository('Comment\Entity\EntityType')->getEntityType($data['entityType'])) {
            throw new \Exception('Unknown entity');
        }

        if (!$entity->getEnabledComment()) {
            throw new \Exception('Comment on this entity can not be');
        }

        $data['entityType'] = $entity;
        $data['entityTypeId'] = $entity->getId();

        $user = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity()->getUser();
        $data['user'] = $user;

        $comment = new \Comment\Entity\Comment();

        $form->setData($data);

        if ($form->isValid()) {
            $data = $form->getData();
            $serviceEntityType = $this->getServiceLocator()->get('Comment\Service\EntityType');
            $result = $serviceEntityType->get($data['entityType']->getAliasEntity(), $data['entityId']);
            if (!$result) {
                throw new \Exception('Unknown entity');
            }

            $entityManager->getConnection()->beginTransaction();
            try {
                $entity->getComments()->add($comment);
                $hydrator = new DoctrineHydrator($entityManager);
                $hydrator->hydrate($data, $comment);
                $entityManager->persist($comment);
                $entityManager->persist($entity);
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
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function getCommentsByEntityId(array $data)
    {

        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        if (!$entityType = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType($data['entityType'])) {
            throw new \Exception('Unknown entity');
        }

        if (!$entityType->getVisibleComment()) {
            throw new \Exception('Comments for this entity is prohibited to watch');
        }

        $serviceEntityType = $this->getServiceLocator()->get('Comment\Service\EntityType');
        $result = $serviceEntityType->get($data['entityType'], $data['entityId']);
        if (!$result) {
            throw new \Exception('Unknown entity');
        }

        $comments = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $data['entityId']));
        $arrayComments = array();

        $entityComment = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType('comment');

            foreach ($comments as $comment) {
                $arrayComments[$comment->getId()]['comment'] = $comment;
                if ($entityComment->getVisibleComment()) {
                    $data = [
                        'entityType' => 'comment',
                        'entityId' => $comment->getId()
                    ];
                    $arrayComments[$comment->getId()]['childs'] = self::getCommentsByEntityId($data);
                }
            }

        return $arrayComments;
    }

    public function getCommentsByEntityIdByArray($comments, &$rezult)
    {
        foreach ($comments as $key => $comment) {
            $rezult[$key]['comment']['text'] = $comment['comment']->getComment();
            $rezult[$key]['comment']['user'] = $comment['comment']->getUser()->getDisplayName();
            $rezult[$key]['comment']['create'] = $comment['comment']->getCreated()->format('Y-m-d H:i:s');
            $rezult[$key]['comment']['update'] = $comment['comment']->getUpdated()->format('Y-m-d H:i:s');
            foreach ($comment['childs'] as $childKey => $childComment) {
                $rezult[$key]['childs'][$childKey] = self::getCommentsByEntityIdByArray($childComment['comment'], $rezult);
            }
        }
        return $rezult;
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
        $comment = $objectManager->getRepository('\Comment\Entity\Comment')->findOneBy(['id' => $id]);


        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $entityManager->getRepository('\Comment\Entity\EntityType')->findOneBy(['id' => $comment->getEntityType()->getId()]);

        if (!$entity->getEnabledComment()) {
            throw new \Exception('Comment can not be deleted');
        }

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
     * @param \Zend\Form\Form $form
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function editCommentById(\Zend\Form\Form $form, $data)
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $entityManager->getRepository('\Comment\Entity\EntityType')->findOneBy(['id' => $form->getObject()->getEntityType()->getId()]);

        if (!$entity->getEnabledComment()) {
            throw new \Exception('Comment can not be edited');
        }

        $hydrator = new DoctrineHydrator($entityManager);
        $hydrator->hydrate($data, $form->getObject());

        if ($form->isValid()) {
            $entityManager->persist($form->getObject());
            $entityManager->flush();
            return $form->getObject();
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
}
