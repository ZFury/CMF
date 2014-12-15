<?php

namespace Mail\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\ServiceManager\ServiceManager;
use Zend\InputFilter\InputFilter;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;
use Starter\DBAL\Entity\EntityBase;

/**
 * @ORM\Entity
 * @Annotation\Name("mail")
 * @ORM\Table(name="mail")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 */
class Mail extends EntityBase
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
     * @ORM\Column(type="string", length=255, unique = true)
     */
    protected $alias;

    /**
     * @var string
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $from_email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $from_name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body_html;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body_text;

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

//    /**
//     * @var string
//     * @Annotation\Type("Zend\Form\Element\Select")
//     * @Annotation\Required(false)
//     * @Annotation\Options({"label":"Role:", "value_options":{"1":"Member", "2":"Admin"}})
//     * @Annotation\Attributes({"class":"form-control"})
//     * @ORM\Column(type="string", length=128, options={"default" = "Member"})
//     */
    protected $is_used;

    /**
     * @var string
     */
    protected $inputFilter;

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
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->from_email;
    }

    /**
     * @param $fromEmail
     */
    public function setFromEmail($fromEmail)
    {
        $this->from_email = $fromEmail;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * @param $fromName
     */
    public function setFromName($fromName)
    {
        $this->from_name = $fromName;
    }

    /**
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->body_html;
    }

    /**
     * @param $bodyHtml
     */
    public function setBodyHtml($bodyHtml)
    {
        $this->body_html = $bodyHtml;
    }

    /**
     * @return string
     */
    public function getBodyText()
    {
        return $this->body_text;
    }

    /**
     * @param $bodyText
     */
    public function setBodyText($bodyText)
    {
        $this->body_text = $bodyText;
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
//    public function getIsUsed()
//    {
//        return $this->is_used;
//    }

    /**
     * @param $isUsed
     */
//    public function setIsUsed($isUsed)
//    {
//        $this->is_used = $isUsed;
//    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array(
            "id" => $this->getId(),
            "alias" => $this->getAlias(),
            "description" => $this->getDescription(),
            "subject" => $this->getSubject(),
            "from_email" => $this->getFromEmail(),
            "from_name" => $this->getFromName(),
            "body_html" => $this->getBodyHtml(),
            "body_text" => $this->getBodyText(),
            "created" => $this->getCreated(),
            "updated" => $this->getUpdated(),
//            "isUsed" => $this->getIsUsed(),
        );

        return $result;
    }
}
