<?php

namespace User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\ServiceManager\ServiceManager;

/**
 * An example of how to implement a role aware user entity.
 *
 * @ORM\Entity(repositoryClass="User\Repository\User")
 * @ORM\Table(name="users")
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @ORM\HasLifecycleCallbacks
 * @author Oleksii Novikov
 */
class User
{
    const ROLE_USER = 'user';

    const ROLE_ADMIN = 'admin';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_UNCONFIRMED = 'unconfirmed';

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
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Validator({"name":"EmailAddress"})
     * Annotation\Validator({"name":"Db\NoRecordExists", "options":{"adapter": "Db\Adapter", "table" : "users", "fields" : "email"}})
     * @ORM\Column(type="string", unique=true, nullable=true, length=255)
     */
    protected $email;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Role:", "value_options":{"1":"Member", "2":"Admin"}})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(type="string", length=128, options={"default" = "Member"})
     */
    protected $role;

    /**
     * @var string
     * @Annotation\Exclude
     * @ORM\Column(type="string", nullable=true, length=128)
     */
    protected $confirm;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Select")     *
     * @Annotation\Options({"label":"Satus:", "value_options":{"active" : "active", "inactive" : "inactive", "unconfirmed" : "unconfirmed"}})
     * @Annotation\Attributes({"class":"form-control"})
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('active','inactive','unconfirmed')", options={"default" = "unconfirmed"})
     */
    protected $status;

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
     * @Annotation\Exclude
     * @ORM\OneToMany(targetEntity="Auth", mappedBy="user", cascade={"remove"})
     */
    private $auths;

    /**
     * Initialies the auths variable.
     */
    public function __construct()
    {
        $this->auths = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getAuths()
    {
        return $this->auths;
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
        $this->id = (int) $id;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return void
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get role.
     *
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role.
     *
     * @param int $role
     *
     * @return void
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
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
     * @return string
     */
    public function generateConfirm()
    {
        return md5($this->getId() . microtime(false) . $this->getEmail());
    }

    /**
     * @return $this
     */
    public function activate()
    {
        $this->setStatus(self::STATUS_ACTIVE);
        $this->setConfirm(null);

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isUnconfirmed()
    {
        return $this->getStatus() == self::STATUS_UNCONFIRMED;
    }
}
