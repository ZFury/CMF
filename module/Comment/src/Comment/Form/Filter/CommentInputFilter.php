<?php

namespace Comment\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;

class CommentInputFilter extends InputFilter
{
    /** @var  ServiceManager */
    protected $sm;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->alias();
        $this->comment();
        $this->entityId();
    }

    /**
     * @return $this
     */
    protected function alias()
    {
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
            ],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function comment()
    {
        $this->add([
            'name' => 'comment',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function entityId()
    {
        $this->add([
            'name' => 'entityId',
            'required' => true,
            'filters' => [
                ['name' => 'Int'],
            ],
        ]);

        return $this;
    }
}
