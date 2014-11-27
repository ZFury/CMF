<?php

namespace Categories\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\ServiceManager\ServiceManager;

/**
 * Categories\Entity\Categories
 *
 * @ORM\Entity(repositoryClass="Categories\Repository\Categories")
 * @ORM\Table(name="categories")
 * @Annotation\Name("categories")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 */
class Categories
{
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
     * @Annotation\Required(true)
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Options({"label":"Name:"})
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $name;

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
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="children")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parentId;
//, onDelete="CASCADE"
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

    private $pathString = array();

    /**
     *
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

//    /**
//     * @return ArrayCollection
//     */
//    public function getAuths()
//    {
//        return $this->auths;
//    }

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
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function buildPath()
    {
//        array_push($this->pathString, $this->getAlias());
//        if ($this->getParentId() == null) {
//            $string = implode('/', $this->pathString);
//            $this->setPath($string);
////            return $this->getPath();
//        } else {
//            return $this->getParentId()->buildPath();
//        }
        $pathArray = array();
        $pathString = implode('/', array_reverse($this->recursivePath($pathArray)));
        $this->setPath($pathString);
    }

    public function recursivePath($pathArray)
    {
//        $path .= $this->getAlias();
        array_push($pathArray, $this->getAlias());
        if ($this->getParentId() == null) {

//            $string = implode('/', $this->pathString);
//            $this->setPath($string);
            return $pathArray;
        } else {
            return $this->getParentId()->recursivePath($pathArray);
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
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

//    /**
//     * Get root id.
//     *
//     * @return int
//     */
//    public function getRootId()
//    {
//        return $this->rootId;
//    }

//    /**
//     * Set root id.
//     *
//     * @param int $rootId
//     *
//     * @return void
//     */
//    public function setRootId($rootId)
//    {
//        $this->rootId = (int)$rootId;
//    }
//
//    public function getRootId()
//    {
//        if ($this->getParentId() == null) {
//            return $this->getId();
//        } else {
//            return $this->getParentId()->getRootId();
//        }
//
//    }
}
