<?php

namespace Starter\View\Helper\Grid;

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
        $result = '<ul id="' . $this->id . '" class="' . implode(' ', $this->class) . '">';
        $result .= '<li class="disabled"><a href="javascript:;">Limit</a></li>';
        foreach ($this->limit as $limitValue) {
            $result .= '<li' . (($this->grid->getLimit() == $limitValue) ? ' class="active"' : '') . '>';
            $result .= '<a href="' . $this->grid->getUrl(['limit' => $limitValue]) . '">' . $limitValue . '</a>';
            $result .= '</li>';
        }
        $result .= '</ul>';
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
