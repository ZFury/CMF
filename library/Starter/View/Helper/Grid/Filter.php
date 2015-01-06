<?php

namespace Starter\View\Helper\Grid;

class Filter extends AbstractGridHelper
{
    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = '';

        if (count($this->grid->getAllowedFilters())) {
            $result .= '<div class="col-md-6 pull-right">';
            $result .= '<form class="navbar-form filter-form pull-right">';
            $result .= '<div class="input-group">';
            $result .= '<div class="input-group-btn grid-filter-search">';
            $result .= '<button type="button" class="btn btn-default dropdown-toggle"';
            $result .= 'data-toggle="dropdown">' . ucfirst(current($this->grid->getAllowedFilters()));
            $result .= '<span class="caret"></span></button>';
            $result .= '<ul class="dropdown-menu">';
            foreach ($this->grid->getAllowedFilters() as $field) {
                $result .= '<li><a href="javascript:;" data-filter="' . $field . '">' . ucfirst($field) . '</a></li>';
            }
            $result .= '</ul></div>';
            $result .= '<input class="grid-filter-search-input"';
            $result .= 'name="' . 'filter-' . current($this->grid->getAllowedFilters()) . '" type="hidden"/>';
            $result .= '<input type="search" class="form-control"/>';
            $result .= '<span class="input-group-btn"><button class="btn btn-default" type="submit">Search</button>';
            $result .= '</span></div></form></div>';
        }

        return $result;
    }
}