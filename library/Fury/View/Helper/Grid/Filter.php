<?php

namespace Fury\View\Helper\Grid;

class Filter extends AbstractGridHelper
{
    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = '';

        if (count($this->grid->getAllowedFilters())) {
            $result = $this->getView()->partial('layout/grid/filter.phtml', ['grid' => $this->grid]);
        }

        return $result;
    }
}
