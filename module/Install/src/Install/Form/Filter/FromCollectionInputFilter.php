<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/13/15
 * Time: 12:50 PM
 */
namespace Install\Form\Filter;

use Zend\InputFilter\CollectionInputFilter;

class FromCollectionInputFilter extends CollectionInputFilter
{
    public function __construct()
    {
        $inputFilter = new FromInputFilter();
        $this->setInputFilter($inputFilter);
    }
}
