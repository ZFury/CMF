<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/12/15
 * Time: 2:57 PM
 */
namespace Install\Form\Fieldset;

use Zend\Form\Fieldset;

class HeaderFieldset extends Fieldset
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
}
