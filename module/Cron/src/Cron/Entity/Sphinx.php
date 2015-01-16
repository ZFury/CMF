<?php

namespace Cron\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;

/** *
 * @ORM\Entity
 * @ORM\Table(name="sphinx_delta_counter")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Sphinx
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false, columnDefinition="enum('index', 'users', 'pages')")
     */
    protected $index_name;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, options = {"default": 0})
     */
    protected $last_post_id;

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->index_name;
    }

    /**
     * @param $index_name
     */
    public function setIndexName($index_name)
    {
        $this->index_name = $index_name;
    }

    /**
     * @return string
     */
    public function getLastPostId()
    {
        return $this->index_name;
    }

    /**
     * @param $last_post_id
     */
    public function setLastPostId($last_post_id)
    {
        $this->last_post_id = $last_post_id;
    }
}
