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
            'name' => 'host',
            'type' => 'Text',
            'options' => [
                'label' => 'Host',
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
