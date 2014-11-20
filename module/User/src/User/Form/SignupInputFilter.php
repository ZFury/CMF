<?php

namespace User\Form;

use Zend\ServiceManager\ServiceManager;

class SignupInputFilter extends CreateInputFilter
{
    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
//        $this->username();
        $this->email();
        $this->password();
        $this->repeatPassword();
    }
}
