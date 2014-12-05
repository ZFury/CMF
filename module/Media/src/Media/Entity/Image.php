<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:13 PM
 */

namespace Media\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;

/**
 * Images
 * @ORM\Entity(repositoryClass="Media\Repository\Image")
 * @ORM\Table(name="images")
 * @Annotation\Name("image")
 */
class Image
{

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=5, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Extension:"})
     */
    private $extension;

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
     * @var ArrayCollection()
     *
     * @ORM\OneToMany(targetEntity="ObjectImage", mappedBy="image")
     */
    private $objectsImages;

    public function __construct()
    {
        $this->objectsImages = new ArrayCollection();
    }

    /**
     * @param ObjectImage $objectImage
     */
    public function addObjectImage(ObjectImage $objectImage)
    {
        $this->objectsImages[] = $objectImage;
    }

    /**
     * @param $objectsImages
     * @return Image
     */
    public function setObjectsImages($objectsImages)
    {
        $this->objectsImages = $objectsImages;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getObjectsImages()
    {
        return $this->objectsImages;
    }

    /**
     * @param $objectImage
     * @return Image
     */
    public function removeObjectImage($objectImage)
    {
        $this->objectsImages->removeElement($objectImage);

        return $this;
    }

    /**
     * @return Image
     */
    public function removeAllObjectImages()
    {
        $this->objectsImages->clear();

        return $this;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
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

    /**
     * @return mixed
     */
    public function getLocation()
    {
        $ext = $this->getExtension();

        return \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $this->id, $ext);
    }

    /**
     * @return string
     */
    public function getUrlPart()
    {
        $ext = $this->getExtension();

        return \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $this->id, $ext, true);
    }

    /**
     * Returns the part of url
     *
     * @return string
     */
    public function getThumb()
    {
        $ext = $this->getExtension();
        $imageId = $this->getId();
        $urlPart = \Media\Service\Image::imgPath(\Media\Service\Image::SMALL_THUMB, $imageId, $ext);
        if (!file_exists(\Media\Service\Image::PUBLIC_PATH . $urlPart)) {
            $originalLocation = $this->getLocation();

            $image = new \Imagick($originalLocation);
            $image->cropThumbnailImage(\Media\Service\Image::S_THUMB_WIDTH, \Media\Service\Image::S_THUMB_HEIGHT);
            \Media\Service\Image::prepareDir(\Media\Service\Image::PUBLIC_PATH . $urlPart);
            $image->writeimage(\Media\Service\Image::PUBLIC_PATH . $urlPart);
        }

        return $urlPart;
    }
}