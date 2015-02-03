<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/23/14
 * Time: 12:08 PM
 */

namespace Media\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

abstract class FileUpload extends Form
{
    /**
     * Returns the file type of a form input
     *
     * @return string
     */
    abstract public function getFileType();
}
