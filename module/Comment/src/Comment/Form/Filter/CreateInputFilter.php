<?php

namespace Comment\Form\Filter;

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
        $this->aliasEntity();
        $this->entity();
        $this->description();
    }

    /**
     * @return $this
     */
    protected function aliasEntity()
    {
        /*$recordExistsValidator = new NoObjectExists(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')->getRepository('Comment\Entity\EntityType'),
                'fields' => 'aliasEntity'
            )
        );
        $recordExistsValidator->setMessage(
            'Alias entity with this title already exists',
            NoObjectExists::ERROR_OBJECT_FOUND
        );*/

        $this->add(array(
            'name' => 'aliasEntity',
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
                //$recordExistsValidator
            ),
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function entity()
    {
        /*$recordExistsValidator = new NoObjectExists(
            array(
                'object_repository' => $this->sm->get('Doctrine\ORM\EntityManager')->getRepository('Comment\Entity\EntityType'),
                'fields' => 'entity'
            )
        );
        $recordExistsValidator->setMessage(
            'Entity with this title already exists',
            NoObjectExists::ERROR_OBJECT_FOUND
        );*/

        $this->add(array(
            'name' => 'entity',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                //$recordExistsValidator
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
                        'max' => 50,
                    ),
                ),
            ),

        ));

        return $this;
    }
}