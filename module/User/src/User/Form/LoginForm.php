<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 05.08.14
 * Time: 18:24
 */

namespace User\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('form-login');
        $this->setAttribute('method', 'post')
            ->setAttribute('role', 'form')
            ->setAttribute('class', 'form-login form-horizontal');
        $this->setInputFilter(new LoginInputFilter());
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Sign In',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block'
            ),
        ));
    }
}
