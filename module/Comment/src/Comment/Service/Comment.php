<?php

namespace Comment\Service;

use Doctrine\ORM\EntityNotFoundException;
use Zend\ServiceManager\ServiceManager;
use Comment\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Comment\Service;
use Comment\Form\Filter;
use Zend\Form\Annotation\AnnotationBuilder;
use DoctrineModule\Validator;
use Zend\Stdlib\Hydrator;
use Comment\Entity;
use User\Entity\User;

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
     * @param $comment
     * @return bool
     */
    public function commentOwner($comment)
    {
        $identity = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        if ($comment->getUserId() === $identity->getUserId() || $identity->getUser()->getRole() === User::ROLE_ADMIN) {
            return true;
        }

        return false;
    }

    /**
     * @param \Zend\Form\Form $form
     * @param $data
     * @return Entity\Comment|null
     * @throws \Exception
     */
    public function add(\Zend\Form\Form $form, $data)
    {
        $serviceLocator = $this->getServiceLocator();
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $entityType = $entityManager->getRepository('\Comment\Entity\EntityType')->findOneByAlias($data['alias']);
        if (!$entityType) {
            throw new EntityNotFoundException();
        }
        $form->setData($data);

        if ($form->isValid()) {
            if ($entityType->getIsEnabled()) {
                $data = $form->getData();

                $comment = new Entity\Comment();
                $comment->setEntityType($entityType);
                $user = $serviceLocator->get('Zend\Authentication\AuthenticationService')->getIdentity()->getUser();
                $comment->setUser($user);
                $comment->setComment($data['comment']);

                $entityManager->getConnection()->beginTransaction();
                try {
                    $hydrator = new DoctrineHydrator($entityManager);
                    $hydrator->hydrate($data, $comment);
                    $entityType->getComments()->add($comment);
                    $entityManager->persist($comment);
                    $entityManager->persist($entityType);
                    $entityManager->flush();
                    $entityManager->getConnection()->commit();

                    return $comment;
                } catch (\Exception $e) {
                    $entityManager->getConnection()->rollback();
                    throw $e;
                }
            }
        }

        return null;
    }

    /**
     * @param $entity
     * @return array
     * @throws BadMethodCallException
     * @throws EntityNotFoundException
     */
    public function treeByEntity($entity)
    {
        if (is_object($entity) && method_exists($entity, 'getId')) {
            $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

            $class = get_class($entity);
            $entityType = $objectManager->getRepository('Comment\Entity\EntityType')->findOneByEntity($class);
            if (!$entityType) {
                throw new EntityNotFoundException();
            }

            return $this->getComments($entityType, $entity->getId());
        }
        throw new BadMethodCallException();
    }

    /**
     * @param Entity\EntityType $entityType
     * @param $entityId
     * @return array
     */
    public function getComments(Entity\EntityType $entityType, $entityId)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $arrayComments = [];
        $identity = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        if ($entityType->getIsVisible() || $identity->getUser()->getRole() === User::ROLE_ADMIN) {
            $comments = $objectManager->getRepository('Comment\Entity\Comment')
                ->findBy([
                    'entityType' => $entityType,
                    'entityId' => $entityId
                ]);

            $enabledCommentByComment = null;
            $commentEntityType = $objectManager->getRepository('Comment\Entity\EntityType')->findOneByAlias('comment');
            if ($commentEntityType && $commentEntityType->getIsEnabled()) {
                $enabledCommentByComment = true;
            }

            foreach ($comments as $comment) {
                $arrayComments[$comment->getId()]['comment'] = $comment;
                if ($commentEntityType) {
                    $arrayComments[$comment->getId()]['children'] = $this->getComments(
                        $commentEntityType,
                        $comment->getId()
                    );
                } else {
                    $arrayComments[$comment->getId()]['children'] = [];
                }
                $arrayComments[$comment->getId()]['enabledCommentByComment'] = $enabledCommentByComment;
            }
        }

        return $arrayComments;
    }

    /**
     * $alias is an alias that is used in EntityType to recognize the entity
     *
     * @param $alias
     * @param $entityId
     * @return array
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function tree($alias, $entityId)
    {
        if (!$alias || !$entityId) {
            throw new \Exception('Bad request');
        }
        $entityType = $this->serviceManager->get('Doctrine\ORM\EntityManager')
            ->getRepository('Comment\Entity\EntityType')->findOneByAlias($alias);

        if (!$entityType) {
            throw new EntityNotFoundException();
        }

        return $this->getComments($entityType, $entityId);
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $comment = $objectManager->getRepository('\Comment\Entity\Comment')->findOneBy(['id' => $id]);

        if (!$comment) {
            throw new \Exception('Comment does not exist');
        }
        if (!$this->commentOwner($comment)) {
            throw new \Exception('You do not have permission for this operation');
        }
        if (!$comment->getEntityType()->getIsEnabled()) {
            throw new \Exception('Comment can not be deleted');
        }

        $objectManager->getConnection()->beginTransaction();

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
     * @param Entity\Comment $comment
     * @param $data
     * @return Entity\Comment
     * @throws \Exception
     */
    public function edit(Entity\Comment $comment, $data)
    {
        if (!$this->commentOwner($comment)) {
            throw new \Exception('You do not have permission for this operation');
        }
        if (!$comment->getEntityType()->getIsEnabled()) {
            throw new \Exception('Comment can not be edited');
        }
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $hydrator = new DoctrineHydrator($entityManager);
        $hydrator->hydrate($data, $comment);
        $entityManager->persist($comment);
        $entityManager->flush();

        return $comment;
    }

    /**
     * @param Entity\Comment $comment
     * @return \Zend\Form\Form
     * @throws \Exception
     */
    public function createForm(Entity\Comment $comment = null)
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $builder = new AnnotationBuilder($entityManager);
        if ($comment) {
            $form = $builder->createForm($comment);
            $form->setInputFilter(new Filter\EditCommentInputFilter($this->getServiceLocator()));
            $form->setHydrator(new DoctrineHydrator($entityManager));
            $form->bind($comment);
        } else {
            $form = $builder->createForm(new Entity\Comment());
            $form->setInputFilter(new Filter\CommentInputFilter($this->getServiceLocator()));
        }

        return $form;
    }

    /**
     * @param $form
     * @param $entityId
     * @param $entity
     * @return string
     */
    public function getAddCommentForm($form, $entityId, $entity)
    {
        return $this->serviceManager->get('ViewHelperManager')
            ->get('Partial')->__invoke(
                "comment/index/add.phtml",
                ['form' => $form, 'commentService' => $this, 'id' => $entityId, 'alias' => $entity]
            );
    }

    /**
     * @param $id
     * @return mixed
     * @throws EntityNotFoundException
     */
    public function getEntityByCommentId($id)
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $comment = $entityManager->getRepository('\Comment\Entity\Comment')->findOneById($id);
        if (!$comment) {
            throw new EntityNotFoundException();
        }
        /** @var \Comment\Entity\EntityType $entityType */
        $entityType = $comment->getEntityType();
        $entityNamespace = $entityType->getEntity();

        return $entityManager->getRepository($entityNamespace)->findOneById($comment->getEntityId());
    }
}
