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
            'name'     => 'checkbox_media',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_categories',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_comments',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_dashboard',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_mail',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_options',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_pages',
            'required' => false,
        ]);

        $this->add([
            'name'     => 'checkbox_test',
            'required' => false,
        ]);
    }
}
