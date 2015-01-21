<?php

namespace Fury\View\Helper\Grid;

use Fury\Grid\AbstractGrid;
use Zend\View\Helper\AbstractHelper;

abstract class AbstractGridHelper extends AbstractHelper
{
    /**
     * @var AbstractGrid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var array
     */
    protected $class = [];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getWidget();
    }

    /**
     * @return string
     */
    abstract public function getWidget();

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string|array $classes
     * @return $this
     */
    public function setClass($classes)
    {
        if (is_array($classes)) {
            $this->class = $classes;
        } else {
            $this->class = [$classes];
        }

        return $this;
    }

    /**
     * @param AbstractGrid $grid
     * @param array $options
     * @return $this
     */
    public function __invoke(AbstractGrid $grid, $options = [])
    {
        $this->grid = $grid;

        if (!empty($options)) {
            $this->setOptions($options);
        }

        return $this;
    }
}
