<?php

namespace Comment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Starter\DBAL\Entity\EntityBase;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity(repositoryClass="Comment\Repository\EntityType")
 * @ORM\Table(name="entity_type")
 * @Annotation\Name("entity_type")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 * @author Sergey Lopay
 */
class EntityType extends EntityBase
{
    /**
     * @var int
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Alias entity:"})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(type="string", unique=true,  nullable=false)
     */
    protected $aliasEntity;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Entity:"})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    protected $entity;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Description:"})
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @var boolean
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Visibility of comments:"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $visibleComment;

    /**
     * @var boolean
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Possible to comment:"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabledComment;

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
     * Set aliasEntity.
     *
     * @param int $aliasEntity
     *
     * @return void
     */
    public function setAliasEntity($aliasEntity)
    {
        $this->aliasEntity = $aliasEntity;
    }

    /**
     * Get aliasEntity.
     *
     * @return string
     */
    public function getAliasEntity()
    {
        return $this->aliasEntity;
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
     * @param int $visibleComment
     *
     * @return void
     */
    public function setVisibleComment($visibleComment)
    {
        $this->visibleComment = $visibleComment;
    }

    /**
     * Get visibleComment.
     *
     * @return int
     */
    public function getVisibleComment()
    {
        return $this->visibleComment;
    }

    /**
     * Set enabledComment.
     *
     * @param int $enabledComment
     *
     * @return void
     */
    public function setEnabledComment($enabledComment)
    {
        $this->enabledComment = $enabledComment;
    }

    /**
     * Get enabledComment.
     *
     * @return int
     */
    public function getEnabledComment()
    {
        return $this->enabledComment;
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

    /**
     * @return array
     */
    public function toArray()
    {
        {
            $result = array(
                "id" => $this->getId(),
                "aliasEntity" => $this->getAliasEntity(),
                "entity" => $this->getEntity(),
                "VisibleComment" => $this->getVisibleComment(),
                "enabledComment" =>$this->getEnabledComment(),
                "description" => $this->getDescription(),
                "created" => $this->getCreated(),
                "updated" => $this->getUpdated(),
            );

            return $result;
        }
    }
}
