<?php

namespace Comment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fury\DBAL\Entity\EntityBase;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity(repositoryClass="Comment\Repository\EntityType")
 * @ORM\Table(name="entity_type")
 * @Annotation\Name("entity_type")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 */
class EntityType extends EntityBase
{
    /**
     * @var int
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Alias entity:"})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(name="alias", type="string", unique=true,  nullable=false)
     */
    protected $alias;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Entity:"})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(name="entity", type="string", unique=true, nullable=false)
     */
    protected $entity;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Description:"})
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected $description;

    /**
     * @var boolean
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Visibility of comments:"})
     * @ORM\Column(name="is_visible", type="boolean", nullable=true)
     */
    protected $isVisible;

    /**
     * @var boolean
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Possible to comment:"})
     * @ORM\Column(name="is_enabled", type="boolean", nullable=true)
     */
    protected $isEnabled;

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
     * @Annotation\Exclude
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="entityType", cascade={"remove"})
     */
    protected $comments;

    /**
     * Initialize the comments variable.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
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
     * Set description
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set alias.
     *
     * @param int $alias
     *
     * @return void
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set entity.
     *
     * @param $entity
     *
     * @return void
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get entity.
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set visibleComment.
     *
     * @param int $visible
     *
     * @return void
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * Get visibleComment.
     *
     * @return int
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set enabledComment.
     *
     * @param int $isEnabled
     *
     * @return void
     */
    public function setEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * Get enabledComment.
     *
     * @return int
     */
    public function isEnabled()
    {
        return $this->isEnabled;
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
     * Set $updatedAt.
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
                "alias" => $this->getAlias(),
                "entity" => $this->getEntity(),
                "isVisible" => $this->isVisible(),
                "isEnabled" =>$this->isEnabled(),
                "description" => $this->getDescription(),
                "createdAt" => $this->getCreatedAt(),
                "updatedAt" => $this->getUpdatedAt(),
            );

            return $result;
        }
    }
}
