<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/4/14
 * Time: 3:38 PM
 */

namespace Media\Form;

use Media\Entity\File;
use Zend\InputFilter;
use Zend\Form\Element;

class VideoUpload extends FileUpload
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
    }

    public function addElements()
    {
        $file = new Element\File('video');
        $file->setLabel('Video Upload')
            ->setAttribute('id', 'video');
        $this->add($file);
    }

    public function getFileType()
    {
        return File::VIDEO_FILETYPE;
    }
}
