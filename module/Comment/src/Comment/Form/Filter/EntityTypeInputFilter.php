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
        $this->isVisible();
        $this->isEnabled();
    }

    /**
     * @return $this
     */
    protected function alias()
    {
        $recordUniqueValidator = new UniqueObject([
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')
                    ->getRepository('Comment\Entity\EntityType'),
                'fields' => ['alias'],
                'object_manager' => $this->sm->get('Doctrine\ORM\EntityManager'),
        ]);
        $recordUniqueValidator->setMessage(
            'Entity type with this alias already exists'
        );

        $this->add([
            'name' => 'alias',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^[a-zA-Z-_]*$/',
                        'message' => 'Entity type contains invalid characters'
                    ],
                ],
                $recordUniqueValidator
            ],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function entity()
    {
        $recordUniqueValidator = new UniqueObject([
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')
                    ->getRepository('Comment\Entity\EntityType'),
                'fields' => ['entity'],
                'object_manager' => $this->sm->get('Doctrine\ORM\EntityManager'),
        ]);
        $recordUniqueValidator->setMessage(
            'Entity type with this entity already exists'
        );

        $this->add([
            'name' => 'entity',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [$recordUniqueValidator],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function description()
    {
        $this->add([
            'name' => 'description',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 5,
                    ],
                ],
            ],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function isVisible()
    {
        $this->add([
            'name' => 'isVisible',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function isEnabled()
    {
        $this->add([
            'name' => 'isEnabled',
            'required' => false,
        ]);

        return $this;
    }
}
