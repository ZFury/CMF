<?php

namespace Starter\View\Helper\Grid;

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

        $result .= '<table id="' . $this->id . '" class="' . implode(' ', $this->class) . '">';
        $result .= '<thead><tr>';
        foreach ($this->grid->getColumns() as $alias => $column) {
            if ($order = $this->grid->order($alias)) {
                $result .= '<th><a href="' . $order . '">' . $column . '</a></th>';
            } else {
                $result .= '<th>' . $column . '</th>';
            }
        }
        $result .= '</tr></thead><tbody>';
        foreach ($this->grid->getData() as $row) {
            $result .= '<tr>';
            foreach ($row as $key => $value) {
                $result .= '<td>' . ($value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : $value) . '</td>';
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';

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
