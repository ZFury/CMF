<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/12/14
 * Time: 1:24 PM
 */

namespace Media\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;

/**
 * Files
 *
 * @ORM\Entity(repositoryClass="Media\Repository\File")
 * @ORM\Table(name="files")
 * @Annotation\Name("file")
 */
class File
{
    const IMAGE_FILETYPE = 'image';
    const AUDIO_FILETYPE = 'audio';
    const VIDEO_FILETYPE = 'video';

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=5, nullable=true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Extension:"})
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=5, nullable=true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Type:"})
     */
    private $type;

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
     * @ORM\OneToMany(targetEntity="ObjectFile", mappedBy="file")
     */
    private $objectsFiles;

    public function __construct()
    {
        $this->objectsFiles = new ArrayCollection();
    }

    /**
     * @param ObjectFile $objectFile
     */
    public function addObjectImage(ObjectFile $objectFile)
    {
        $this->objectsFiles[] = $objectFile;
    }

    /**
     * @param $objectsFiles
     * @return File
     */
    public function setObjectsFiles($objectsFiles)
    {
        $this->objectsFiles = $objectsFiles;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getObjectsFiles()
    {
        return $this->objectsFiles;
    }

    /**
     * @param $objectFile
     * @return File
     */
    public function removeObjectImage($objectFile)
    {
        $this->objectsFiles->removeElement($objectFile);

        return $this;
    }

    /**
     * @return File
     */
    public function removeAllObjectFiles()
    {
        $this->objectsFiles->clear();

        return $this;
    }

    /**
     * Set extension
     *
     * @param  string $extension
     * @return File
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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        $ext = $this->getExtension();

        switch ($this->type) {
            case self::IMAGE_FILETYPE:
                return \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $this->id, $ext);
            case self::AUDIO_FILETYPE:
                return \Media\Service\Audio::audioPath($this->id, $ext);
            case self::VIDEO_FILETYPE:
                return \Media\Service\Video::videoPath($this->id, $ext);
            default:
        }


    }

    /**
     * @return string
     */
    public function getUrlPart()
    {
        $ext = $this->getExtension();

        switch ($this->type) {
            case self::IMAGE_FILETYPE:
                return \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $this->id, $ext, true);
            case self::AUDIO_FILETYPE:
                return \Media\Service\Audio::audioPath($this->id, $ext, true);
            case self::VIDEO_FILETYPE:
                return \Media\Service\Video::videoPath($this->id, $ext, true);
            default:
        }

        return null;
    }

    /**
     * @param int $thumbSize
     * @return null|string
     * @throws \Exception
     */
    public function getThumb($thumbSize = \Media\Service\Image::SMALL_THUMB)
    {
        switch ($this->type) {
            case self::IMAGE_FILETYPE:
                $ext = $this->getExtension();
                $imageId = $this->getId();
                $urlPart = \Media\Service\Image::imgPath($thumbSize, $imageId, $ext);
                if (!file_exists(\Media\Service\File::PUBLIC_PATH . $urlPart)) {
                    $originalLocation = $this->getLocation();
                    $image = new \Imagick($originalLocation);
                    $image->cropThumbnailImage(\Media\Service\Image::S_THUMB_WIDTH, \Media\Service\Image::S_THUMB_HEIGHT);
                    \Media\Service\File::prepareDir(\Media\Service\File::PUBLIC_PATH . $urlPart);
                    $image->writeimage(\Media\Service\File::PUBLIC_PATH . $urlPart);
                }

                return $urlPart;
            case self::AUDIO_FILETYPE:
                return \Media\Service\Audio::audioPath($this->id, $this->getExtension(), true);
            case self::VIDEO_FILETYPE:
                return \Media\Service\Video::videoPath($this->id, $this->getExtension(), true);
            default:
        }

        return null;
    }
}
