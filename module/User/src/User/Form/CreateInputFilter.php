<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 05.09.14
 * Time: 11:25
 */
namespace User\Form;

use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;
use DoctrineModule\Validator\NoObjectExists;

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
        //$this->username();
        $this->email();
    }

    /**
     * @return $this
     */
    protected function password()
    {
        $this->add(array(
            'name' => 'password',
            'required' => true,
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
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function repeatPassword()
    {
        $this->add(array(
            'name' => 'repeat-password',
            'required' => true,
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
        ));
        return $this;
    }

    /**
     * @return $this
     */
    protected function email()
    {
        $recordExistsValidator = new NoObjectExists(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')->getRepository('User\Entity\User'),
                'fields'            => 'email'
            )
        );
        $recordExistsValidator->setMessage(
            'User with this email already exists',
            NoObjectExists::ERROR_OBJECT_FOUND
        );

        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress'
                ),
                $recordExistsValidator
//                array(
//                    'name' => 'Db\NoRecordExists',
//                    'options' => array(
//                        'table' => $this->sm->get('Doctrine\ORM\EntityManager')->getClassMetadata('User\Entity\User')->getTableName(),
//                        'field' => 'email',
//                        'adapter' => $this->sm->get('Db\Adapter')
//                    ),
//                ),
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function username()
    {
        $recordExistsValidator = new NoObjectExists(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')->getRepository('User\Entity\User'),
                'fields'            => 'username'
            )
        );
        $recordExistsValidator->setMessage(
            'User with this email already exists',
            NoObjectExists::ERROR_OBJECT_FOUND
        );
        $this->add(array(
            'name' => 'username',
            'required' => true,
            'validators' => array(
                $recordExistsValidator,
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