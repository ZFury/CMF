<?php

namespace Comment\Entity;

use Doctrine\ORM\Mapping as ORM;
use User\Entity\User;
use Zend\Form\Annotation;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Fury\DBAL\Entity\EntityBase;

/**
 *
 * @ORM\Entity(repositoryClass="Comment\Repository\Comment")
 * @ORM\Table(name="comment")
 * @Annotation\Name("comment")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 */
class Comment extends EntityBase
{
    /**
     * @var int
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Exclude
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Comment:",})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @var int
     * @Annotation\Type("Zend\Form\Element\Text")
     * @ORM\Column(name="user_id", type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @Annotation\Required(true)
     * @ORM\ManyToOne(targetEntity="Comment\Entity\EntityType")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $entityType;

    /**
     * @var int
     * @Annotation\Type("Zend\Form\Element\Text")
     * @ORM\Column(name="entity_type_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $entityTypeId;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @ORM\Column(name="entity_id", type="integer", nullable=false)
     */
    protected $entityId;

    /**
     * @var \Datetime
     * @Annotation\Exclude
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \Datetime
     * @Annotation\Exclude
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\PreRemove
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function deleteChildren(LifecycleEventArgs $args)
    {
        $objectManager = $args->getObjectManager();
        $entityType = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType('comment');
        $commentRepository = $objectManager->getRepository('Comment\Entity\Comment');
        $comments = $commentRepository->findBy(array('entityType' => $entityType, 'entityId' => $this->getId()));
        foreach ($comments as $comment) {
            $objectManager->remove($comment);
        }
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param EntityType $entityType
     */
    public function setEntityType(EntityType $entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Set entityTypeId.
     *
     * @param int $entityTypeId
     *
     * @return void
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->entityTypeId = $entityTypeId;
    }

    /**
     * Get entityTypeId.
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->entityTypeId;
    }

    /**
     * Set entityId.
     *
     * @param int $entityId
     *
     * @return void
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * Get entityId.
     *
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set created.
     *
     * @param string $createdAt
     *
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updated.
     *
     * @param string $updatedAt
     *
     * @return void
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        {
            $result = array(
                "id" => $this->getId(),
                "comment" => $this->getComment(),
                "entityType" => $this->getEntityType(),
                "entityTypeId" => $this->getEntityTypeId(),
                "entityId" => $this->getEntityId(),
                "user" => $this->getUser(),
                "createdAt" => $this->getCreatedAt(),
                "updatedAt" => $this->getUpdatedAt(),
            );
            return $result;
        }
    }
}
