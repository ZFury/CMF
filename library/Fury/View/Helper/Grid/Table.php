<?php

namespace Fury\View\Helper\Grid;

class Table extends AbstractGridHelper
{
    protected $editUrl = 'javascript:;';

    protected $deleteUrl = 'javascript:;';

    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = '';

        if ($this->grid->totalPages() > 1) {
            $result = $this->getView()->partial(
                'layout/grid/table.phtml',
                [
                    'grid' => $this->grid,
                    'class' => $this->class,
                    'id' => $this->id
                ]
            );
        }

        return $result;
    }

    public function setEditUrl($editUrl)
    {
        $this->editUrl = $editUrl;

        return $this;
    }

    public function setDeleteUrl($deleteUrl)
    {
        $this->deleteUrl = $deleteUrl;

        return $this;
    }
}
