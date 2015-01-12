<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/12/15
 * Time: 2:57 PM
 */
namespace Install\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class HeaderFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('header');

        $this->add(array(
            'name' => 'header-name',
            'options' => array(
            )
        ));

        $this->add(array(
            'name' => 'header-value',
            'options' => array(
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
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 40,
                        ],
                    ]
                ]
            )
        );
    }
}
