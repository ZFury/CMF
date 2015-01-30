<?php

namespace Comment\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;
use DoctrineModule\Validator\UniqueObject;
use Zend\Db\Adapter;

class EntityTypeInputFilter extends InputFilter
{
    /** @var  ServiceManager */
    protected $sm;

    /** @var \Comment\Entity\EntityType */
    protected $entityType;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->alias();
        $this->entity();
        $this->description();
    }

    /**
     * @return $this
     */
    protected function alias()
    {
        $recordUniqueValidator = new UniqueObject(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')
                    ->getRepository('Comment\Entity\EntityType'),
                'fields' => array('alias'),
                'object_manager' => $this->sm->get('Doctrine\ORM\EntityManager'),
            )
        );
        $recordUniqueValidator->setMessage(
            'Entity type with this alias already exists'
        );

        $this->add(array(
            'name' => 'alias',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z-_]*$/',
                        'message' => 'Entity type contains invalid characters'
                    ),
                ),
                $recordUniqueValidator
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function entity()
    {
        $recordUniqueValidator = new UniqueObject(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')
                    ->getRepository('Comment\Entity\EntityType'),
                'fields' => array('entity'),
                'object_manager' => $this->sm->get('Doctrine\ORM\EntityManager'),
            )
        );
        $recordUniqueValidator->setMessage(
            'Entity type with this entity already exists'
        );

        $this->add(array(
            'name' => 'entity',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                $recordUniqueValidator
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function description()
    {
        $this->add(array(
            'name' => 'description',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 5,
                    ),
                ),
            ),

        ));

        return $this;
    }
}
