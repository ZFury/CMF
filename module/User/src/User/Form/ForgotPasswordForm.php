<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use User\Form\Filter\ForgotPasswordInputFilter;

class ForgotPasswordForm extends Form
{
    public function __construct($name = null, array $options = array())
    {
        parent::__construct('forgot-password');
        $this->setAttribute('method', 'post')
            ->setAttribute('role', 'form')
            ->setAttribute('class', 'form-horizontal forgot-password');

        if (!isset($options['serviceLocator']) || !($options['serviceLocator'] instanceof ServiceManager)) {
            throw new \Exception('No service locator is provided');
        }

        $this->setInputFilter(new ForgotPasswordInputFilter($options['serviceLocator']));
        $this->add(
            array(
                'name' => 'security',
                'type' => 'Zend\Form\Element\Csrf',
            )
        );
        $this->add(
            array(
                'name' => 'email',
                'type' => 'text',
                'options' => array(
                    'label' => 'Email',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label'
                    ),
                ),
                'attributes' => ['class' => 'form-control', 'placeholder' => 'email']
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Recovery',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-lg btn-primary btn-block'
                ),
            )
        );
    }
}
