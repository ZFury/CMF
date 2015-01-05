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
use Media\Entity\File;

class ImageUploadInputFilter implements InputFilterAwareInterface
{
    /**
     * @var InputFilter|InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return InputFilter|InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => File::IMAGE_FILETYPE,
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => '\Zend\Validator\File\IsImage',
                                'options' => array(
                                    'messages' => array(
                                        'fileIsImageFalseType' => 'Please select a valid icon image to upload.',
                                        'fileIsImageNotDetected' => 'The icon image is missing mime encoding,
                                        please verify you have saved the image with mime encoding.',
                                    ),
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
