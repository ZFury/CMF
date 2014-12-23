<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/20/14
 * Time: 10:26 AM
 */

namespace Install\Form;

use Zend\Form\Form;

class DbConnection extends Form
{
    public function __construct()
    {
        parent::__construct('db_connection');

        $this->add([
            'name' => 'host',
            'type' => 'Text',
            'options' => [
                'label' => 'Host',
            ],
        ]);

        $this->add([
            'name' => 'port',
            'type' => 'Text',
            'options' => [
                'label' => 'Port',
            ],
        ]);

        $this->add([
            'name' => 'user',
            'type' => 'Text',
            'options' => [
                'label' => 'User',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Text',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'dbname',
            'type' => 'Text',
            'options' => [
                'label' => 'DB name',
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
