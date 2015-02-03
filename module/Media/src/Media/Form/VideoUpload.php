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
     * Video input
     */
    public function addElements()
    {
        $file = new Element\File('video');
        $file->setLabel('Video Upload')
            ->setAttribute('id', 'video');
        $this->add($file);
    }

    /**
     * Returns the file type of a form input
     *
     * @return string
     */
    public function getFileType()
    {
        return File::VIDEO_FILETYPE;
    }
}
