<?php

namespace Comment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\ServiceManager\ServiceManager;

/**
 *
 * @ORM\Entity(repositoryClass="Comment\Repository\Comment")
 * @ORM\Table(name="comment")
 * @Annotation\Name("comment")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @author Sergey Lopay
 */
class Comment
{
    /**
     * @var int
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Exclude
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(type="text", nullable=false)
     */
    protected $comment;

    /**
     * @var int
     * @Annotation\Exclude
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     */
    protected $userId;

    /**
     * @var string
     * @Annotation\Exclude
     * @ORM\Column(type="string", nullable=false)
     */
    protected $entityType;

    /**
     * @Annotation\Exclude
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $entityId;

    /**
     * @var created
     * @Annotation\Exclude
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var updated
     * @Annotation\Exclude
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdated(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
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
        $this->id=$id;
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
     * Set comment
     *
     * @param string $comment
     *
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment=$comment;
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return void
     */
    public function setUserId($userId)
    {
        $this->userId=$userId;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set entityType.
     *
     * @param int $entityType
     *
     * @return void
     */
    public function setEntityType($entityType)
    {
        $this->entityType=$entityType;
    }

    /**
     * Get entityType.
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
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
        $this->entityId=$entityId;
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
     * @param string $created
     *
     * @return void
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created.
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated.
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated.
     *
     * @param string $updated
     *
     * @return void
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }
}
