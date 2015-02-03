<?php

namespace Comment\Form\Filter;

use Zend\ServiceManager\ServiceManager;

class EditCommentInputFilter extends CommentInputFilter
{
    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->comment();
    }
}
