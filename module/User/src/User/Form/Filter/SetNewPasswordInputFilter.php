<?php

namespace User\Form\Filter;

use Zend\ServiceManager\ServiceManager;

class SetNewPasswordInputFilter extends CreateInputFilter
{
    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->password();
        $this->repeatPassword();
    }
}
