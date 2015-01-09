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

class FromFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('from');

        $this->add(array(
            'name' => 'from',
            'options' => array(
//                'label' => 'These emails will be showed to a recipient as senders emails'
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
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\EmailAddress'
                    ]
                ]
            )
        );
    }
}
