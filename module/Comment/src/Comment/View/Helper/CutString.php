<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 2/3/15
 * Time: 11:24 AM
 */
namespace Comment\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

class CutString implements HelperInterface
{
    /**
     * @var Renderer
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
     * @param $stringToCut
     * @param $numberOfSymbolsToCut
     * @param null $appendInTheEnd
     * @return string
     */
    public function __invoke($stringToCut, $numberOfSymbolsToCut, $appendInTheEnd = null)
    {
        $cutString = substr($stringToCut, 0, $numberOfSymbolsToCut);
        if ($appendInTheEnd && is_string($appendInTheEnd)) {
            return $cutString . $appendInTheEnd;
        }

        return $cutString;
    }
}
