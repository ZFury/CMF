<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/20/14
 * Time: 10:26 AM
 */

namespace Install\Form;

use Zend\Form\Form;

class Modules extends Form
{
    public function __construct()
    {
        parent::__construct('modules');

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'Categories',
            'options' => [
                'label' => 'Categories',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'Comment',
            'options' => [
                'label' => 'Comments',
                'use_hidden_element' =>true,
                'unchecked_value' => 'bad',
                'checked_value' => 'good'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'Mail',
            'options' => [
                'label' => 'Mail',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'Options',
            'options' => [
                'label' => 'Options',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'Pages',
            'options' => [
                'label' => 'Pages',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

//        $this->add([
//            'name' => 'submit',
//            'type' => 'Submit',
//            'attributes' => [
//                'value' => 'Submit',
//                'id' => 'submitbutton',
//            ],
//        ]);
    }
}
