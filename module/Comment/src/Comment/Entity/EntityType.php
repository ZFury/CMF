<?php

namespace Comment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\ServiceManager\ServiceManager;

/**
 *
 * @ORM\Entity(repositoryClass="Comment\Repository\EntityType")
 * @ORM\Table(name="entityType")
 * @Annotation\Name("comment")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @author Sergey Lopay
 */
class EntityType
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
}
