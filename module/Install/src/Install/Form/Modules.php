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
            'name' => 'checkbox_media','use_hidden_element' =>true,
            'options' => [
                'label' => 'Media (image, audio, video upload)',

                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_categories',
            'options' => [
                'label' => 'Categories',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_comments',
            'options' => [
                'label' => 'Comments',
                'use_hidden_element' =>true,
                'unchecked_value' => 'bad',
                'checked_value' => 'good'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_dashboard',
            'options' => [
                'label' => 'Dashboard',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_mail',
            'options' => [
                'label' => 'Mail',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_options',
            'options' => [
                'label' => 'Options',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_pages',
            'options' => [
                'label' => 'Pages',
                'use_hidden_element' =>true,
                'checked_value' => 'good',
                'unchecked_value' => 'bad'
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'checkbox_test',
            'options' => [
                'label' => 'Test (Controllers that test another modules)',
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
