<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/5/15
 * Time: 1:10 PM
 */

namespace Install\Form\Filter;

use Zend\InputFilter\InputFilter;

class ModulesInputFilter extends InputFilter
{
    public function __construct($sm, $userId = null)
    {
        $this->add([
            'name'     => 'Categories',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'Comment',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'Mail',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'Options',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'Pages',
            'required' => false,
        ]);
    }
}
