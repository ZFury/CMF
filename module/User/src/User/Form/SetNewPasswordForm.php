<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use User\Form\Filter\SetNewPasswordInputFilter;

class SetNewPasswordForm extends Form
{
    public function __construct($name = null, array $options = array())
    {
        parent::__construct('form-signup');
        $this->setAttribute('method', 'post')
            ->setAttribute('role', 'form')
            ->setAttribute('class', 'form-horizontal');

        if (!isset($options['serviceLocator']) || !($options['serviceLocator'] instanceof ServiceManager)) {
            throw new \Exception('No service locator is provided');
        }

        $this->setInputFilter(new SetNewPasswordInputFilter($options['serviceLocator']));
        $this->add(
            array(
                'name' => 'security',
                'type' => 'Zend\Form\Element\Csrf',
            )
        );
        $this->add(
            array(
                'name' => 'password',
                'type' => 'Password',
                'options' => array(
                    'min' => 3,
                    'max' => 25,
                    'label' => 'Password',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
                'attributes' => ['class' => 'form-control']
            )
        );
        $this->add(
            array(
                'name' => 'repeat-password',
                'type' => 'Password',
                'options' => array(
                    'min' => 3,
                    'max' => 25,
                    'label' => 'Repeat password',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
                'attributes' => ['class' => 'form-control']
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Sign Up',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-lg btn-primary btn-block'
                ),
            )
        );
    }
}
