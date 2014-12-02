<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/2/14
 * Time: 11:47 AM
 */

namespace User\Form;

use Zend\Form\Form;

class ChangePasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('changePassword');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'currentPassword',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Current password',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'New password',
            ),
        ));

        $this->add(array(
            'name' => 'repeat-password',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Confirm new password',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'id' => 'submitbutton',
            ),
        ));
    }
}
