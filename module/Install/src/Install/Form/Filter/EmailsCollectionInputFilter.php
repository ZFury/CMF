<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 1/13/15
 * Time: 6:11 PM
 */
namespace Install\Form\Filter;

use Zend\InputFilter\CollectionInputFilter;

class EmailsCollectionInputFilter extends CollectionInputFilter
{
    public function __construct()
    {
        $inputFilter = new EmailsInputFilter();
        $this->setInputFilter($inputFilter);
    }
}
