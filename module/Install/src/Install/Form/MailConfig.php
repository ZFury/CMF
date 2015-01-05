<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/20/14
 * Time: 10:26 AM
 */

namespace Install\Form;

use Zend\Form\Form;

class MailConfig extends Form
{
    public function __construct()
    {
        parent::__construct('mail_config');

        $this->add([
            'name' => 'host',
            'type' => 'Text',
            'options' => [
                'label' => 'Host',
            ],
        ]);

        $this->add([
            'name' => 'port',
            'type' => 'Number',
            'options' => [
                'label' => 'Port',
            ],
        ]);

        $this->add([
            'name' => 'project',
            'type' => 'Text',
            'options' => [
                'label' => 'Project',
            ],
        ]);

        $this->add([
            'name' => 'emails',
            'type' => 'Text',
            'options' => [
                'label' => 'Emails',
            ],
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Text',
            'options' => [
                'label' => 'From',
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
