<?php

namespace User\Form\Filter;

use Zend\ServiceManager\ServiceManager;

class ForgotPasswordInputFilter extends CreateInputFilter
{
    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->emailExist();
    }
}
