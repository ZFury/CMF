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
    abstract public function getFileType();
}
