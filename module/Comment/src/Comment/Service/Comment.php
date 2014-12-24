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
        if ($comment->getUserId()===$identity->getUserId() || $identity->getUser()->getRole()===User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    /**
     * @param $aliasEntity
     * @return bool
     * @throws \Exception
     */
    public function checkOwner($aliasEntity)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        !$entityType = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType($aliasEntity);
        if (!$entityType->getEnabledComment()) {
            throw new \Exception('Comment on this entity can not be');
        }

        return true;
    }

    /**
     * @param \Zend\Form\Form $form
     * @param array $data
     * @return \Comment\Entity\Comment
     * @throws \Exception
     */
    public function add(\Zend\Form\Form $form, array $data)
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form->setData($data);
        if ($form->isValid()) {
            if ($this->checkOwner($data['entity'])) {
                $data = $form->getData();
                $serviceEntityType = $this->getServiceLocator()->get('Comment\Service\EntityType');
                $entityType = $serviceEntityType->checkEntity($data['entity'], $data['entityId']);

                $comment = new Entity\Comment();
                $comment->setEntityType($entityType);
                $user = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity()->getUser();
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
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function lisComments(array $data)
    {
        if (!isset($data['entity']) || !isset($data['entityId'])) {
            return $this->notFoundAction();
        }

        $serviceEntityType = $this->getServiceLocator()->get('Comment\Service\EntityType');
        $entityType = $serviceEntityType->checkEntity($data['entity'], $data['entityId']);

        if (!$this->checkOwner($data['entity']) || !$entityType->getVisibleComment()) {
            throw new \Exception('Comments for this entity is prohibited to display');
        }

        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $comments = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $data['entityId']));
        $arrayComments = array();

        $entityComment = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType('comment');

        foreach ($comments as $comment) {
            $arrayComments[$comment->getId()]['comment'] = $comment;
            if ($entityComment->getVisibleComment()) {
                    $data = [
                        'entity' => 'comment',
                        'entityId' => $comment->getId()
                    ];
                    $arrayComments[$comment->getId()]['children'] = self::lisComments($data);
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
            throw new \Exception('You are not authorized for this operation');
        }

        if (!$this->checkOwner($comment->getEntityType()->getAliasEntity())) {
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
            throw new \Exception('You are not authorized for this operation');
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
            if (!$this->checkOwner($comment->getEntityType()->getAliasEntity())) {
                throw new \Exception('Comment can not be edited');
            }
            $form->setHydrator(new DoctrineHydrator($entityManager));
            $form->bind($comment);
        }

        return $form;
    }
}
