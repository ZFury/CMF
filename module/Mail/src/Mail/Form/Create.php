<?php

namespace Mail\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\I18n\Validator;
use Zend\Validator\Db;
use Zend\Validator\Exception;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class Create
 * @package Mail\Form
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
                    'type' => 'hidden',
                    'id' => 'id',
                    'class' => 'form-control'
                ),
                'options' => array(),
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
                'name' => 'subject',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'subject',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Subject',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'from_email',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'from_email',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'From email',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'from_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'from_name',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'From name',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $redactor = new \Starter\Form\Element\Redactor();
        $redactor->setName('body_html')
            ->setAttributes(['id' => 'body_html',
                'class' => 'form-control redactor-content'])
            ->setOptions([
                'label' => 'Body (html)',
                'label_attributes' => [
                    'class' => 'col-sm-2 control-label'
                ]
            ]);
        $this->add($redactor);

        $this->add(
            array(
                'name' => 'body_text',
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'body_text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Body (text)',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name' => 'is_used',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'is_used',
                    'class' => 'form-control'
                ),
                'options' => array(),
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
