<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/12/14
 * Time: 4:01 PM
 */

namespace Media\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Files
 *
 * @ORM\Entity(repositoryClass="Media\Repository\ObjectFile")
 * @ORM\Table(name="objects_files")
 * @Annotation\Name("object_file")
 */
class ObjectFile
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
     * @ORM\Column(name="file_id", type="integer", nullable=true)
     * @Annotation\Options({"label":"File ID:"})
     */
    private $fileId;

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
     * @ORM\ManyToOne(targetEntity="File", inversedBy="objectsFiles")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $file;

    /**
     * Set file
     *
     * @param  string $file
     * @return ObjectFile
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set $fileId
     *
     * @param  string $fileId
     * @return ObjectFile
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * Set entityName
     *
     * @param  string $entityName
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
     * @param  string $objectId
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
