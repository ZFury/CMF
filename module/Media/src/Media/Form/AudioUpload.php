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

class AudioUpload extends FileUpload
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
    }

    public function addElements()
    {
        $file = new Element\File('audio');
        $file->setLabel('Audio Upload')
            ->setAttribute('id', 'audio');
        $this->add($file);
    }

    public function getFileType()
    {
        return File::AUDIO_FILETYPE;
    }
}
