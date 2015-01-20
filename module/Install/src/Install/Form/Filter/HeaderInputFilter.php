<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/13/15
 * Time: 12:48 PM
 */

namespace Install\Form\Filter;

use Zend\InputFilter\InputFilter;

class HeaderInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'header-name',
            'required' => false,
            'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 40,
                        ],
                    ]
                ]
        ]);
        $this->add([
            'name' => 'header-value',
            'required' => false,
            'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 40,
                        ],
                    ]
                ]
        ]);
    }
}
