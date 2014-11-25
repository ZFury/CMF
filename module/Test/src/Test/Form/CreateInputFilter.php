<?php

namespace Test\Form;

use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\UniqueObject;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;

class CreateInputFilter extends InputFilter
{
    /** @var  ServiceManager */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->email()->name();
    }

    /**
     * @return $this
     */
    protected function email()
    {
        $recordExistsValidator = new UniqueObject(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')->getRepository('Test\Entity\Test'),
                'object_manager'    => $this->sm->get('Doctrine\ORM\EntityManager'),
                'fields'            => 'email'
            )
        );
        $recordExistsValidator->setMessage(
            'This email already in use',
            UniqueObject::ERROR_OBJECT_NOT_UNIQUE
        );

        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress'
                ),
                $recordExistsValidator
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        return $this;
    }

    protected function name()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 3,
                        'max' => 100,
                    ),
                )
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        return $this;
    }
}