<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/2/14
 * Time: 11:48 AM
 */

namespace User\Form\Filter;

use Zend\InputFilter\InputFilter;

class ChangePasswordInputFilter extends CreateInputFilter
{
    public function __construct($sm)
    {
        $this->sm = $sm;
        $this->add(array(
            'name'     => 'currentPassword',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 3,
                        'max' => 25,
                    ),
                ),
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        $this->password();
        $this->repeatPassword();
    }
}
