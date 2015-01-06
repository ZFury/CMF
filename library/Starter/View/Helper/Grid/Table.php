<?php

namespace Starter\View\Helper\Grid;

class Table extends AbstractGridHelper
{
    /**
     * @inheritdoc
     */
    public function getWidget()
    {
        $result = '';

        $result .= '<table id="' . $this->id . '" class="' . implode(' ', $this->class) . '">';
        $result .= '<thead><tr>';
        foreach ($this->grid->getColumns() as $alias => $column) {
            if ($order = $this->grid->order($column)) {
                $result .= '<th><a href="' . $order . '">' . $alias . '</a></th>';
            } else {
                $result .= '<th>' . $alias . '</th>';
            }
        }
        $result .= '<th width="96px"></th>';
        $result .= '</tr></thead><tbody>';
        foreach ($this->grid->getData() as $row) {
            $result .= '<tr>';
            foreach ($row as $key => $value) {
                $result .= '<td>' . ($value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : $value) . '</td>';
            }
            $result .= '<td><a class="btn btn-primary btn-xs" href="javascript:;">';
            $result .= '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
            $result .= '<a class="btn btn-danger btn-xs" href="javascript:;">';
            $result .= '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
            $result .= '</td></tr>';
        }
        $result .= '</tbody></table>';

        return $result;
    }
}