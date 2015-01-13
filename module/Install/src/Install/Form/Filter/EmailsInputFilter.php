<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/13/15
 * Time: 12:48 PM
 */

namespace Install\Form\Filter;

use Zend\InputFilter\InputFilter;

class EmailsInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'emails',
            'required' => false,
            'validators' => [['name' => 'Zend\Validator\EmailAddress']]
        ]);
    }
}
