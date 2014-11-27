<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 05.09.14
 * Time: 11:25
 */
namespace Categories\Form;

use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;
use DoctrineModule\Validator\NoObjectExists;

class CreateInputFilter extends InputFilter
{
    /** @var  ServiceManager */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->name();
        $this->alias();
    }

    /**
     * @return $this
     */
    protected function name()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 50,
                    ),
                ),
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function alias()
    {
//        $recordExistsValidator = new \Categories\Validators\NoObjectExists($this->sm->get('Doctrine\ORM\EntityManager')->getRepository('Categories\Entity\Categories')
//        );
//        $recordExistsValidator->setMessage(
//            'Category with this alias already exists'
//        );
        $this->add(array(
            'name' => 'alias',
            'required' => true,
            'validators' => array(
//                $recordExistsValidator,
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 2,
                        'max' => 50,
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z0-9]*$/',
                        'message' => 'Alias contains invalid characters'
                    ),
                ),
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        return $this;
    }
}