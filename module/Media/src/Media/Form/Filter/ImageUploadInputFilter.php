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

class ImageUploadInputFilter implements InputFilterAwareInterface
{
    public $imageUpload;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->imageUpload  = (isset($data['image-upload']))  ? $data['image-upload']     : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'image',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => '\Zend\Validator\File\IsImage',
                            'options' => array(
                                'messages' => array(
                                    'fileIsImageFalseType' => 'Please select a valid icon image to upload.',
                                    'fileIsImageNotDetected' => 'The icon image is missing mime encoding, please verify you have saved the image with mime encoding.',
                                ),
                            ),
                        ),
                    )
                ))
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
