<?php

namespace Pages\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class Create
 * @package Pages\Form\Create
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
                'name' => 'id',
                'attributes' => array(
                    'type'  => 'hidden',
                    'id' => 'id',
                    'class' => 'form-control'
                ),
                'options' => array(
                ),
            )
        );

        $this->add(
            array(
                'name' => 'title',
                'attributes' => array(
                    'type'  => 'text',
                    'id' => 'title',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Title',
                    'label_attributes' => array(
                        'class'  => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'alias',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'alias',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Alias (link)',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'content',
                'attributes' => array(
                    'type'  => 'textarea',
                    'id' => 'content',
                    'class' => 'form-control redactor-content'
                ),
                'options' => array(
                    'label' => 'Content',
                    'label_attributes' => array(
                        'class'  => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'keywords',
                'attributes' => array(
                    'type'  => 'textarea',
                    'id' => 'keywords',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Keywords',
                    'label_attributes' => array(
                        'class'  => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'description',
                'attributes' => array(
                    'type'  => 'textarea',
                    'id' => 'description',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Description',
                    'label_attributes' => array(
                        'class'  => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'userId',
                'attributes' => array(
                    'type'  => 'hidden',
                    'id' => 'userId',
                    'class' => 'form-control'
                ),
                'options' => array(
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Create',
                    'id' => 'submit',
                    'class' => 'form-control col-sm-6 btn btn-success'
                ),
            )
        );
    }
}
