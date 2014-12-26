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
use Media\Service\Audio;
use Media\Service\Image;
use Media\Service\Video;
use Media\Service\File as FileService;
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

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getLocation()
    {
        $ext = $this->getExtension();

        switch ($this->type) {
            case self::IMAGE_FILETYPE:
                return Image::imgPath(Image::ORIGINAL, $this->id, $ext);
            case self::AUDIO_FILETYPE:
                return Audio::audioPath($this->id, $ext);
            case self::VIDEO_FILETYPE:
                return Video::videoPath($this->id, $ext);
            default:
                return '';
        }
    }

    /**
     * @return null|string
     * @throws \Exception
     */
    public function getUrlPart()
    {
        $ext = $this->getExtension();

        switch ($this->type) {
            case self::IMAGE_FILETYPE:
                return Image::imgPath(Image::ORIGINAL, $this->id, $ext, FileService::FROM_PUBLIC);
            case self::AUDIO_FILETYPE:
                return Audio::audioPath($this->id, $ext, FileService::FROM_PUBLIC);
            case self::VIDEO_FILETYPE:
                return Video::videoPath($this->id, $ext, FileService::FROM_PUBLIC);
            default:
        }

        return null;
    }

    /**
     * @param int $thumbSize
     * @return null|string
     * @throws \Exception
     */
    public function getThumb($thumbSize = Image::SMALL_THUMB)
    {
        switch ($this->type) {
            case self::IMAGE_FILETYPE:
                $ext = $this->getExtension();
                $imageId = $this->getId();
                $urlPart = Image::imgPath($thumbSize, $imageId, $ext, FileService::FROM_PUBLIC);
                if (!file_exists($urlPart)) {
                    $originalLocation = $this->getLocation();
                    $image = new \Imagick($originalLocation);
                    $image->cropThumbnailImage(Image::S_THUMB_WIDTH, Image::S_THUMB_HEIGHT);
                    FileService::prepareDir(FileService::PUBLIC_PATH . $urlPart);
                    $image->writeimage(FileService::PUBLIC_PATH . $urlPart);
                }

                return $urlPart;
            case self::AUDIO_FILETYPE:
                return Audio::audioPath($this->id, $this->getExtension(), FileService::FROM_PUBLIC);
            case self::VIDEO_FILETYPE:
                return Video::videoPath($this->id, $this->getExtension(), FileService::FROM_PUBLIC);
            default:
        }

        return null;
    }
}
