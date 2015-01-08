<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/8/15
 * Time: 10:19 AM
 */

namespace Install\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class EmailsFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('emails');

        $this->add(array(
            'name' => 'emails',
            'options' => array(
//                'label' => 'To these emails messages will be forwarded'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));
    }

    /**
     * @return array
    */
    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\EmailAddress'
                    ]
                ]
            )
        );
    }
}
