<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/4/14
 * Time: 3:39 PM
 */

namespace Media\Form\Filter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class VideoUploadInputFilter implements InputFilterAwareInterface
{
    public $videoUpload;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->videoUpload = (isset($data['video-upload'])) ? $data['video-upload'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'video',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => '\Zend\Validator\File\MimeType',
                                'options' => array(
                                    'mimeType' => ['video/mp4', 'video/x-flv', 'video/x-msvideo', 'video/x-ms-asf']
                                ),
                            ),
                        )
                    )
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
