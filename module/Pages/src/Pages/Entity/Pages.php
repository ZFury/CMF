<?php

namespace Pages\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\ServiceManager\ServiceManager;
use Zend\InputFilter\InputFilter;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;

/**
 * @ORM\Entity
 * @Annotation\Name("pages")
 * @ORM\Table(name="pages")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 */
class Pages
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
     * @ORM\Column(type="text")
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique = true)
     */
    protected $alias;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $keywords;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
     */
    protected $authorId;

    /**
     * @var string
     */
    protected $inputFilter;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->namespace = 'default';
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param $authorId
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * @return array
     */
    public function toArray()
    {
//        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
//        $jsonContent = $serializer->serialize($this, 'json');
//        return $jsonContent;

        $result = array(
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "alias" => $this->getAlias(),
            "content" => $this->getContent(),
            "keywords" => $this->getKeywords(),
            "description" => $this->getDescription(),
            "created" => $this->getCreated(),
            "updated" => $this->getUpdated(),
            "authorId" => $this->getAuthorId(),
        );

        return $result;
    }
}
