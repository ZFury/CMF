<?php
namespace Comment\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Comment;

class AddForm extends Form
{
    public function __construct($name = null, ServiceManager $sl = null)
    {
        parent::__construct('form-add');
        $this->setAttribute('method', 'post')
            ->setAttribute('role', 'form')
            ->setAttribute('class', 'form-add form-horizontal');
        //$this->setInputFilter(new SignupInputFilter($sl));
        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf',
        ));
        $this->add(array(
            'name' => 'comment',
            'type' => 'textArea',
            'options' => array(
                'rows' => 5,
                'cols' => 5,
                'label' => 'Comment',
            ),
            'attributes' => ['class' => 'form-control']
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block'
            ),
        ));
    }

    /**
     * @param $entityType
     */
    public function setEntityType($entityType)
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'entityType',
            'attributes' => array(
                'value' => $entityType
            )
        ));
    }

    /**
     * @param $entityId
     */
    public function setEntityId($entityId)
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'entityId',
            'attributes' => array(
                'value' => $entityId
            )
        ));
    }
}
