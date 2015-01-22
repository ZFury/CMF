<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 12/10/14
 * Time: 3:37 PM
 */

namespace Fury\Form\Element;

use Zend\Form\Element\Textarea;

class Redactor extends Textarea
{
    protected $attributes = array(
        'type' => 'redactor',
        'class' => 'form-control redactor-content'
    );
}
