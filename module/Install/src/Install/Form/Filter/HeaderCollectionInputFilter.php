<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/13/15
 * Time: 6:15 PM
 */
namespace Install\Form\Filter;

use Zend\InputFilter\CollectionInputFilter;

class HeaderCollectionInputFilter extends CollectionInputFilter
{
    public function __construct()
    {
        $inputFilter = new HeaderInputFilter();
        $this->setInputFilter($inputFilter);
    }
}
