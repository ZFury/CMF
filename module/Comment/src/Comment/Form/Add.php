<?php
namespace Comment\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Comment;

class Add extends Form
{
    public function __construct($name = null, ServiceManager $sl = null)
    {
        parent::__construct('form-add');
        $this->setAttribute('method', 'post')
            ->setAttribute('role', 'form')
            ->setAttribute('class', 'form-add form-horizontal');
        //$this->setInputFilter(new SignupInputFilter($sl));
        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf',
        ));
        $this->add(array(
            'name' => 'comment',
            'type' => 'textArea',
            'options' => array(
                'rows' => 4,
                'cols' => 8,
                'label' => 'Comment',
            ),
            'attributes' => ['class' => 'form-control']
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block'
            ),
        ));
    }
}
