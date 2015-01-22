<?php

namespace Fury\View\Helper\Grid;

class Limit extends AbstractGridHelper
{
    /**
     * @var array
     */
    protected $limit = [10, 25, 50];

    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = $this->getView()->partial(
            'layout/grid/limit.phtml',
            [
                'grid' => $this->grid,
                'limit' => $this->limit,
                'class' => $this->class,
                'id' => $this->id
            ]
        );
        return $result;
    }

    /**
     * @param int|array $limits
     * @return $this
     */
    public function setLimit($limits)
    {
        if (is_array($limits)) {
            sort($limits);
            $this->limit = $limits;
        } else {
            $this->limit = [intval($limits)];
        }

        return $this;
    }
}
