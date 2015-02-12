<?php

namespace Comment\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Class Comment
 * @package Comment\View\Helper
 */
class Comment implements HelperInterface
{
    /**
     * @var Renderer|null
     */
    private $view;

    /**
     * @param Renderer $view
     * @return void|HelperInterface
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }

    /**
     * @return Renderer
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param $comments
     * @param string $partial
     * @return mixed
     */
    public function __invoke($comments, $partial = 'comment/index/partial/comment.phtml')
    {
        return $this->view->partialLoop($partial, $comments);
    }
}
