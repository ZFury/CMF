<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/8/15
 * Time: 10:19 AM
 */

namespace Install\Form\Fieldset;

use Zend\Form\Fieldset;

class FromFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('from');
        $this->add(['name' => 'from']);
    }
}
