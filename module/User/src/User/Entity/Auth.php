<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 01.10.14
 * Time: 12:01
 */

namespace User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\ServiceManager\ServiceManager;

/**
 * An example of how to implement a role aware user entity.
 *
 * @ORM\Entity(repositoryClass="User\Repository\Auth")
 * @ORM\Table(name="auth")
 * @ORM\HasLifecycleCallbacks
 * @author Oleksii Novikov
 */
class Auth
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=64)
     */
    protected $provider;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $foreignKey;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $tokenSecret;

    /**
     * @var string
     * @ORM\Column(type="string", length=8)
     */
    protected $tokenType;

    /**
     * @var created
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var updated
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="auths")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", onDelete="cascade")
     **/
    private $user;

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set provider.
     *
     * @param string $provider
     *
     * @return void
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get foreignKey
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Set foreignKey.
     *
     * @param string $foreignKey
     *
     * @return void
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
 * Get tokenSecret.
 *
 * @return string
 */
    public function getTokenSecret()
    {
        return $this->tokenSecret;
    }

    /**
     * Set tokenSecret.
     *
     * @param string $tokenSecret
     *
     * @return void
     */
    public function setTokenSecret($tokenSecret)
    {
        $this->tokenSecret = $tokenSecret;
    }

    /**
     * Get tokenType.
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Set tokenType.
     *
     * @param string $tokenType
     *
     * @return void
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
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
     * @param ServiceManager $sm
     */
    public function login(ServiceManager $sm)
    {
        $sm->get('Zend\Authentication\AuthenticationService')->getStorage()->write($this);
    }
}
