<?php

namespace Pages\Form\Filter;

use Zend\InputFilter\InputFilter;

class Create extends InputFilter
{
    public function __construct()
    {
        $this->add(
            array(
                'name' => 'title',
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
                            'min' => 1
                        ),
                    ),
                    array(
                        'name' => 'Alnum',
                        'options' => array(
                            'allowWhiteSpace' => true
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'alias',
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
                            'min' => 1,
                            'max' => 255
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z\d\-]+$/',
                            'message' => 'Allow letters and numbers only'
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'content',
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
                            'min' => 1
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'keywords',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'description',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'userId',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1
                        ),
                    ),
                ),
            )
        );
    }
}
