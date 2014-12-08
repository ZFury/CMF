<?php

namespace Test\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Class Test
 *
 * @ORM\Entity(repositoryClass="Test\Repository\Test")
 * @ORM\Table(name="test")
 * @package                                            Test\Entity
 */
class Test
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false, length=255)
     */
    protected $email;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Validator({"name":"EmailAddress"})
     * @ORM\Column(type="string", nullable=false, length=255)
     */
    protected $name;

    /**
     * @param $id int
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int )$id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $email string
     *
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $name string
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
