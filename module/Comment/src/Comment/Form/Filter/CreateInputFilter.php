<?php

namespace Comment\Form\Filter;

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
        $this->aliasEntity();
        $this->entity();
        $this->description();
    }

    /**
     * @return $this
     */
    protected function aliasEntity()
    {
        $this->add(array(
            'name' => 'aliasEntity',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z-_]*$/',
                        'message' => 'Entity type contains invalid characters'
                    ),
                ),
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function entity()
    {
        $this->add(array(
            'name' => 'entity',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function description()
    {
        $this->add(array(
            'name' => 'description',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 5,
                        'max' => 50,
                    ),
                ),
            ),

        ));

        return $this;
    }
}
