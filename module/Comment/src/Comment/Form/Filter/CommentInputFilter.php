<?php

namespace Comment\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;

class CommentInputFilter extends InputFilter
{
    /** @var  ServiceManager */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->entity();
        $this->comment();
        $this->entityId();
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
    protected function comment()
    {
        $this->add(array(
            'name' => 'comment',
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
    protected function entityId()
    {
        $this->add(array(
            'name' => 'entityId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        return $this;
    }
}
