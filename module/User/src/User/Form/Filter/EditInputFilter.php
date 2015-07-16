<?php

namespace User\Form\Filter;

use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\UniqueObject;
use Zend\Form\Annotation\InputFilter;
use Zend\ServiceManager\ServiceManager;

class EditInputFilter extends \Zend\InputFilter\InputFilter
{
    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->password()->repeatPassword()->email();

    }

    /**
     * @return $this
     */
    protected function password()
    {
        $this->add(
            array(
                'name' => 'password',
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 25,
                        ),
                    ),
                ),
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function repeatPassword()
    {
        $this->add(
            array(
                'name' => 'repeat-password',
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 25,
                        ),
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        ),
                    ),
                ),
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );
        return $this;
    }

    /**
     * @return $this
     */
    protected function email()
    {
        $uniqueObjectValidator = new UniqueObject(
            [
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')->getRepository('User\Entity\User'),
                'fields' => ['email'],
                'object_manager' => $this->sm->get('Doctrine\ORM\EntityManager'),
                'use_context' => true
            ]
        );
        $uniqueObjectValidator->setMessage('Email is already taken', UniqueObject::ERROR_OBJECT_NOT_UNIQUE);

        $this->add(
            array(
                'name' => 'email',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                    $uniqueObjectValidator,
                ),
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )
        );

        return $this;
    }
}
