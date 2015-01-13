<?php

namespace Starter\View\Helper\Grid;

class Pagination extends AbstractGridHelper
{
    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = '';
        if ($this->grid->totalPages() > 1) {
            $result .= '<ul id="' . $this->id . '" class="' . implode(' ', $this->class) . '">';
            if ($prev = $this->grid->prev()) {
                $result .= '<li><a href="' . $this->grid->getUrl(['page' => $prev]) . '">&laquo;</a></li>';
            } else {
                $result .= '<li class="disabled"><a href="javascript:;">&laquo;</a></li>';
            }

            for ($page = 1; $page <= $this->grid->totalPages(); $page++) {
                $result .= '<li' . ($page == $this->grid->getPage() ? ' class="active"' : '') . '>';
                $result .= '<a href="' . $this->grid->getUrl(['page' => $page]) . '">' . $page . '</a>';
                $result .= '</li>';
            }

            if ($next = $this->grid->next()) {
                $result .= '<li><a href="' . $this->grid->getUrl(['page' => $next]) . '">&raquo;</a></li>';
            } else {
                $result .= '<li class="disabled"><a href="javascript:;">&raquo;</a></li>';
            }

            $result .= '</ul>';
        }

        return $result;
    }
}
