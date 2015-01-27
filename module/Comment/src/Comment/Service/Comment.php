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

    public function commentOwner($comment)
    {
        $identity = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        if ($comment->getUserId() === $identity->getUserId() || $identity->getUser()->getRole() === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }


    public static function cutName($username)
    {
        if (strlen($username)<=6) {
            return $username;
        } else {
            return substr($username, 0, 6);
        }
    }

    /**
     * @param $aliasEntity
     * @return bool
     * @throws \Exception
     */
    public function enabledComment($aliasEntity)
    {

        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $entityType = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType($aliasEntity);
        if (!$entityType) {
            throw new \Exception('Unknown entity type');
        }
        if (!$entityType->getEnabledComment()) {
            throw new \Exception('You can not comment this entity');
        }

        return true;
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
        $form->setData($data);
        if ($form->isValid()) {
            if ($this->enabledComment($data['entity'])) {
                $data = $form->getData();
                $serviceEntityType = $serviceLocator->get('Comment\Service\EntityType');
                $entityType = $serviceEntityType->getEntity($data['entity'], $data['entityId']);
                if (!$this->enabledComment($data['entity'])) {
                    throw new \Exception('Prohibited add comments for this entity');
                }

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
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function lisComments(array $data)
    {
        if (!isset($data['entity']) || !isset($data['entityId'])) {
            throw new \Exception('Bad request');
        }

        $serviceEntityType = $this->getServiceLocator()->get('Comment\Service\EntityType');
        $entityType = $serviceEntityType->getEntity($data['entity'], $data['entityId']);
        $arrayComments = array();
        if (!$entityType) {
            return $arrayComments;
        }

        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $commentRepository = $objectManager->getRepository('Comment\Entity\Comment');
        $comments = $commentRepository->findBy(array('entityType' => $entityType, 'entityId' => $data['entityId']));

        $identity = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        if ($entityType->getVisibleComment() || $identity->getUser()->getRole() === User::ROLE_ADMIN) {
            $entityComment = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType($data['entity']);
            $enabledCommentByComment = $entityComment->getEnabledComment();

            foreach ($comments as $comment) {
                $arrayComments[$comment->getId()]['comment'] = $comment;
                if ($entityComment->getVisibleComment()) {
                    $entity = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType('comment');
                    if ($entity) {
                        $data = ['entity' => 'comment', 'entityId' => $comment->getId()];
                        $arrayComments[$comment->getId()]['children'] = $this->lisComments($data);
                    } else {
                        $arrayComments[$comment->getId()]['children'] = array();
                    }
                }
                $arrayComments[$comment->getId()]['enabledCommentByComment'] = $enabledCommentByComment;
            }
        }

        return $arrayComments;
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
        if (!$this->enabledComment($comment->getEntityType()->getAliasEntity())) {
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
     * @param \Zend\Form\Form $form
     * @param Entity\Comment $comment
     * @param $data
     * @return Entity\Comment
     * @throws \Exception
     */
    public function edit(\Zend\Form\Form $form, Entity\Comment $comment, $data)
    {
        if (!$this->commentOwner($comment)) {
            throw new \Exception('You do not have permission for this operation');
        }
        if (!$this->enabledComment($comment->getEntityType()->getAliasEntity())) {
            throw new \Exception('Comment can not be edited');
        }
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $hydrator = new DoctrineHydrator($entityManager);
        $hydrator->hydrate($data, $comment);
        if ($form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
        }

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
        $form = $builder->createForm(new Entity\Comment());
        $form->setInputFilter(new Filter\CommentInputFilter($this->getServiceLocator()));
        if ($comment) {
            $form->setInputFilter(new Filter\CommentEditInputFilter($this->getServiceLocator()));
            $form->setHydrator(new DoctrineHydrator($entityManager));
            $form->bind($comment);
        }

        return $form;
    }

    public function generateAddCommentForm($form)
    {
        echo $this->serviceManager->get('ViewHelperManager')
            ->get('Partial')->__invoke("comment/index/add.phtml", ['form' => $form, 'commentService' => $this]);
    }
}
