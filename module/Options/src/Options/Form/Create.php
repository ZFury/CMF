<?php

namespace Options\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;

/**
 * Class Create
 * @package Options\Form\Create
 */
class Create extends Form
{
    protected $inputFilter;
    protected $serviceLocator;

    /**
     * @param null $serviceLocator
     */
    public function __construct($serviceLocator = null)
    {
        $this->serviceLocator = $serviceLocator;

        // we want to ignore the name passed
        parent::__construct('create');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');

        $this->add(array(
            'name' => 'namespace',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'namespace',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Namespace',
                'label_attributes' => array(
                    'class'  => 'col-sm-2 control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'key',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'key',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Key',
                'label_attributes' => array(
                    'class'  => 'col-sm-2 control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'value',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'value',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Value',
                'label_attributes' => array(
                    'class'  => 'col-sm-2 control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'description',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Description',
                'label_attributes' => array(
                    'class'  => 'col-sm-2 control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Create',
                'id' => 'submit',
                'class' => 'form-control col-sm-6 btn btn-success'
            ),
        ));
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'namespace',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 64,
                        ),
                    ),
                )
            ));

            $inputFilter->add(array(
                'name'     => 'key',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'value',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
//                            'max'      => 255,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'description',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
//                            'max'      => 255,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
