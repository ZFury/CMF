<?php

namespace Fury\View\Helper\Grid;

class Pagination extends AbstractGridHelper
{
    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = '';
        if ($this->grid->totalPages() > 1) {
            $result = $this->getView()->partial(
                'layout/grid/pagination.phtml',
                [
                    'grid' => $this->grid,
                    'class' => $this->class,
                    'id' => $this->id
                ]
            );
        }

        return $result;
    }
}
