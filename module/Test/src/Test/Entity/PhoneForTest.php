<?php
/**
 * Contacts Entity File
 */
namespace Test\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Salary
 *
 * @ORM\Entity(repositoryClass="Test\Repository\SalaryToTest")
 * @ORM\Table(name="phone_for_test", indexes={@ORM\Index(name="FK_phone_for_test", columns={"testId"})})
 */
class PhoneForTest
{
    /**
     * Primary key for table 'contacts'
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $testId;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $number;
}
