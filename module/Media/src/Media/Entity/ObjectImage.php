<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:48 PM
 */

namespace Media\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Images
 * @ORM\Entity(repositoryClass="Media\Repository\ObjectImage")
 * @ORM\Table(name="objects_images")
 * @Annotation\Name("object_image")
 */
class ObjectImage
{

    /**
     * @var string
     *
     * @ORM\Column(name="entity_name", type="string", length=50, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Entity name:"})
     */
    private $entityName;

    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @Annotation\Options({"label":"Object ID:"})
     */
    private $objectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="image_id", type="integer", nullable=false)
     * @Annotation\Options({"label":"Image ID:"})
     */
    private $imageId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="objectsImages")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * Set image
     *
     * @param string $image
     * @return ObjectImage
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set imageId
     *
     * @param string $imageId
     * @return ObjectImage
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Set entityName
     *
     * @param string $entityName
     * @return ObjectImage
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set objectId
     *
     * @param string $objectId
     * @return ObjectImage
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}