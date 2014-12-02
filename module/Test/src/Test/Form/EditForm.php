<?php

namespace Test\Form;

use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ClassMethods;

class EditForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('form-create');
        $this->setAttribute('method', 'post')->setAttribute('role', 'form')
            ->setAttribute('class', 'form-create form-horizontal')
            ->setHydrator(new ClassMethods());
        $this->setInputFilter(new CreateInputFilter($sm));

        $this->add([
            'name' => 'id',
            'type' => 'hidden'
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'min' => 3,
                'max' => 225,
                'label' => 'email',
            ],
            'attributes' => ['class' => 'form-control']
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'min' => 3,
                'max' => 255,
                'label' => 'name',
            ],
            'attributes' => ['class' => 'form-control']
        ]);
    }
}