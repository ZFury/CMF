<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use User;

class SignupForm extends Form
{
    public function __construct($name = null, ServiceManager $sl = null)
    {
        parent::__construct('form-signup');
        $this->setAttribute('method', 'post')
            ->setAttribute('role', 'form')
            ->setAttribute('class', 'form-signup form-horizontal');
        $this->setInputFilter(new SignupInputFilter($sl));
        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf',
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'email',
            ),
            'attributes' => ['class' => 'form-control']
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'password',
            ),
            'attributes' => ['class' => 'form-control']
        ));

        $this->add(array(
            'name' => 'repeat-password',
            'type' => 'Password',
            'options' => array(
                'min' => 3,
                'max' => 25,
                'label' => 'repeat-password',
            ),
            'attributes' => ['class' => 'form-control']
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Sign Up',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block'
            ),
        ));
    }
}
