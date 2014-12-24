<?php

namespace Comment\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;


class Comment implements HelperInterface
{
    private $view;

    public function setView(Renderer $view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view;
    }

    public function __invoke($partial, $comments)
    {
       return $this->view->partialLoop($partial, $comments);
    }
}