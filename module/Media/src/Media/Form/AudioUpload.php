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
    /**
     * Construct
     *
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
    }

    /**
     * Audio input
     */
    public function addElements()
    {
        $file = new Element\File('audio');
        $file->setLabel('Audio Upload')
            ->setAttribute('id', 'audio');
        $this->add($file);
    }

    /**
     * Returns the file type of a form input
     *
     * @return string
     */
    public function getFileType()
    {
        return File::AUDIO_FILETYPE;
    }
}
