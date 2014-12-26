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
            'name' => 'checkbox_media',
            'options' => [
                'label' => 'Media checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_categories',
            'options' => [
                'label' => 'Categories checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_comments',
            'options' => [
                'label' => 'Comments checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_dashboard',
            'options' => [
                'label' => 'Dashboard checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_mail',
            'options' => [
                'label' => 'Mail checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_options',
            'options' => [
                'label' => 'Options checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_pages',
            'options' => [
                'label' => 'Pages checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_test',
            'options' => [
                'label' => 'Test checkbox',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Submit',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
