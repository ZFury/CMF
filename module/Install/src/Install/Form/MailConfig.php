<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/20/14
 * Time: 10:26 AM
 */

namespace Install\Form;

use Zend\Form\Form;
use Zend\Form\FormInterface;

class MailConfig extends Form
{
    public function __construct()
    {
        parent::__construct('mail_config');

        $this->setValidationGroup(FormInterface::VALIDATE_ALL);

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
            'name' => 'header',
            'type' => 'Zend\Form\Element\Collection',
            'options' => [
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'Install\Form\Fieldset\HeaderFieldset'
                ]
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'emails',
            'options' => [
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'Install\Form\Fieldset\EmailsFieldset'
                ]
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'from',
            'options' => [
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'Install\Form\Fieldset\FromFieldset',
                ]
            ]
        ]);
    }
}
