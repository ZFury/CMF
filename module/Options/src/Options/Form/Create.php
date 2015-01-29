<?php

namespace Options\Form;

use Zend\Form\Form;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class Create
 * @package Options\Form\Create
 */
class Create extends Form
{
    protected $inputFilter;
    protected $serviceLocator;

    /**
     * @param int|null|string $name
     * @param array $options
     */
    public function __construct($name, array $options)
    {
        $this->serviceLocator = $options['serviceLocator'];

        $this->setHydrator(new ClassMethods);

        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');
        $this->setInputFilter(new Filter\Create());

        $this->add(
            array(
                'name' => 'namespace',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'namespace',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Namespace',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'key',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'key',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Key',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'value',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'value',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Value',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'description',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'description',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Description',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Create',
                    'id' => 'submit',
                    'class' => 'form-control col-sm-6 btn btn-success'
                ),
            )
        );
    }
}
