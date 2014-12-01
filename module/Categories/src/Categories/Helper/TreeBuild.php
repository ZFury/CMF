<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 25.11.14
 * Time: 11:12
 */

namespace Categories\Helper;

use Zend\View\Helper\AbstractHelper;

class TreeBuild extends AbstractHelper
{
    /**
     * Invoke.
     *
     * @param $tree array Category tree.
     * @return string
     */
    public function __invoke($tree)
    {
        $treeView = $this->treeBuild($tree);
        return $treeView;
    }

    /**
     * Builds tree view for current root directory.
     *
     * @param $tree array Category tree.
     * @return string
     */
    public function treeBuild($tree)
    {
        $str = '';
        foreach ($tree as $node) {

            if (count($node->getChildren()) == 0) {
                $str .= '<li class="mjs-nestedSortable-leaf" data-order="'
                    . $node->getOrder() . '" id="list_' . $node->getId() . '">'
                    . $this->getView()->partial('categories/management/partial/navigation.phtml', ['node' => $node])
                    . '</li>';
            } else {
                $str .= '<li class="mjs-nestedSortable-leaf" data-order="'
                    . $node->getOrder() . '" id="list_' . $node->getId() . '">'
                    . $this->getView()->partial('categories/management/partial/navigation.phtml', ['node' => $node])
                    . '<ol>'
                    . $this->treeBuild($node->getChildren()->toArray())
                    . '</ol>'
                    . '</li>';
            }
        }

        return $str;
    }

} 