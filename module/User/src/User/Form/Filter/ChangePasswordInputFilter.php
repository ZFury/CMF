<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/2/14
 * Time: 11:48 AM
 */

namespace User\Form\Filter;

use Zend\InputFilter\InputFilter;

class ChangePasswordInputFilter extends InputFilter
{
    public function __construct($sm)
    {

        $this->add(array(
            'name'     => 'currentPassword',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 3,
                        'max'      => 255,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'     => 'newPassword',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 3,
                        'max'      => 255,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'     => 'newPasswordConfirm',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 3,
                        'max'      => 255,
                    ),
                ),
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'newPassword',
                    ),
                ),
            ),
        ));
    }
}
