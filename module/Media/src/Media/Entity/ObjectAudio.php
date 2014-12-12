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
 * Audios
 *
 * @ORM\Entity(repositoryClass="Media\Repository\ObjectAudio")
 * @ORM\Table(name="objects_audios")
 * @Annotation\Name("object_audio")
 */
class ObjectAudio
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
     * @ORM\Column(name="audio_id", type="integer", nullable=false)
     * @Annotation\Options({"label":"Audio ID:"})
     */
    private $audioId;

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
     * @ORM\ManyToOne(targetEntity="Audio", inversedBy="objectsAudios")
     * @ORM\JoinColumn(name="audio_id", referencedColumnName="id")
     */
    private $audio;

    /**
     * Set audio
     *
     * @param  string $audio
     * @return ObjectAudio
     */
    public function setAudio($audio)
    {
        $this->audio = $audio;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * Set audioId
     *
     * @param  string $audioId
     * @return ObjectAudio
     */
    public function setAudioId($audioId)
    {
        $this->audioId = $audioId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudioId()
    {
        return $this->audioId;
    }

    /**
     * Set entityName
     *
     * @param  string $entityName
     * @return ObjectAudio
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
     * @param  string $objectId
     * @return ObjectAudio
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
