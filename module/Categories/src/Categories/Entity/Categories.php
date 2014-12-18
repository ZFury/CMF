<?php

namespace Categories\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Starter\DBAL\Entity\EntityBase;

/**
 * Categories\Entity\Categories
 *
 * @ORM\Entity(repositoryClass="Categories\Repository\Categories")
 * @ORM\Table(name="categories")
 * @Annotation\Name("categories")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 */
class Categories extends EntityBase
{
    use \Starter\Media\File;

    /**
     * @var integer
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Exclude
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Options({"label":"Name:"})
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $name;//     * @Annotation\Required(true)

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Options({"label":"Alias:"})
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $alias;

    /**
     * @var integer
     * @Annotation\Exclude
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="children")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parentId;

    /**
     * @Annotation\Exclude
     * @ORM\OneToMany(targetEntity="Categories", mappedBy="parentId")
     **/
    private $children;

    /**
     * @var string
     * @Annotation\Exclude
     * @ORM\Column(type="text")
     */
    protected $path;

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
     * @var integer
     * @Annotation\Exclude
     * @ORM\Column(name="`order`", type="integer")
     */
    protected $order;

    private $lifecycleArgs;

    /**
     *
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestampsAndPath()
    {
        $this->setUpdated(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }

        $pathArray = array();
        $pathString = implode('/', array_reverse($this->recursiveBuildPath($pathArray)));
        $this->setPath($pathString);
    }

    /**
     * Recursively generates path for category.
     *
     * @param  $pathArray array
     * @return mixed
     */
    public function recursiveBuildPath($pathArray)
    {
        array_push($pathArray, $this->getAlias());
        if ($this->getParentId() == null) {
            return $pathArray;
        } else {
            return $this->getParentId()->recursiveBuildPath($pathArray);
        }
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
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set alias.
     *
     * @param string $alias
     *
     * @return void
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get order.
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order.
     *
     * @param string $order
     *
     * @return void
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get parent id.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set parent id.
     *
     * @param int $parentId
     *
     * @return void
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get children.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
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
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param  string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @ORM\PostLoad
     */
    public function setLifecycleArgs(LifecycleEventArgs $args)
    {
        $this->lifecycleArgs = $args;
    }

    public function getEntityName()
    {
        return 'Categories';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "alias" => $this->getAlias(),
            "parentId" => $this->getParentId(),
            "children" => $this->getChildren(),
            "path" => $this->getPath(),
            "created" => $this->getCreated(),
            "updated" => $this->getUpdated(),
            "order" => $this->getOrder(),
        );

        return $result;
    }
}
