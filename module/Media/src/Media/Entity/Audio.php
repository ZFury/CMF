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
 *
 * @ORM\Entity(repositoryClass="Media\Repository\Audio")
 * @ORM\Table(name="audios")
 * @Annotation\Name("audio")
 */
class Audio
{

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
     * @ORM\OneToMany(targetEntity="ObjectAudio", mappedBy="audio")
     */
    private $objectsAudios;

    public function __construct()
    {
        $this->objectsAudios = new ArrayCollection();
    }

    /**
     * @param ObjectAudio $objectAudio
     */
    public function addObjectAudio(ObjectAudio $objectAudio)
    {
        $this->objectsAudios[] = $objectAudio;
    }

    /**
     * @param $objectsAudios
     * @return Audio
     */
    public function setObjectsAudios($objectsAudios)
    {
        $this->objectsAudios = $objectsAudios;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getObjectsAudios()
    {
        return $this->objectsAudios;
    }

    /**
     * @param $objectAudio
     * @return Audio
     */
    public function removeObjectAudio($objectAudio)
    {
        $this->objectsAudios->removeElement($objectAudio);

        return $this;
    }

    /**
     * @return Audio
     */
    public function removeAllObjectAudios()
    {
        $this->objectsAudios->clear();

        return $this;
    }

    /**
     * Set extension
     *
     * @param  string $extension
     * @return Audio
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

        return \Media\Service\Audio::audioPath($this->id, $ext);
    }

    /**
     * @return string
     */
    public function getUrlPart()
    {
        $ext = $this->getExtension();

        return \Media\Service\Audio::audioPath($this->id, $ext, true);
    }
}
