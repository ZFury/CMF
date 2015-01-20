<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/2/14
 * Time: 11:48 AM
 */

namespace User\Form\Filter;

use Zend\InputFilter\InputFilter;

class ChangePasswordAndEmailInputFilter extends CreateInputFilter
{
    public function __construct($sm)
    {
        $this->sm = $sm;

        $this->email();
        $this->password();
        $this->repeatPassword();
    }
}
