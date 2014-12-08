<?php

namespace Starter\Mvc\Controller;

use Zend\Mvc\Controller\AbstractController;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractGridController extends AbstractController
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     *
     * @var QueryBuilder
     */
    private $source;

    /**
     * @var array
     */
    protected $data;

    /**
     * Params array
     * @var array
     */
    protected $params = array();

    /**
     * Default page = 1
     * @var int
     */
    protected $page = 1;

    /**
     * Limit per page
     * @var int
     */
    protected $limit = 25;

    /**
     * Default orders - ASC
     * @var array
     */
    protected $orders = array('field' => 'id', 'order' => self::ORDER_ASC);

    /**
     * __construct
     *
     * @param QueryBuilder $source
     * @return AbstractGridController
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     *  Get data
     *
     * @return array
     */
    public function getData()
    {
        $source = $this->getSource();
        $offset = $this->getLimit() * $this->getPage();
        $limit = $this->getLimit();
        $order = $this->getOrder();

        if (!empty($offset)) {
            $source->setFirstResult($offset);
        }
        if (!empty($limit)) {
            $source->setMaxResults($limit);
        }
        if (!empty($order)) {
            $source->orderBy($order['field'], $order['order']);
        }

        return $source->getQuery()->getArrayResult();
    }

    /**
     * Get source
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        $settings = array();
        $settings['page'] = $this->getPage();
        $settings['limit'] = $this->getLimit();
        $settings['orders'] = $this->getOrder();
        return $settings;
    }

    /**
     * Set settings
     *
     * @param array $params
     * @return AbstractGridController
     */
    public function setSettings(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set page
     *
     * @param int $page
     * @return AbstractGridController
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set limit
     *
     * @param int $limit
     * @return AbstractGridController
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get limit
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set order
     *
     * @param array $order
     * @return AbstractGridController
     */
    public function setOrder($order)
    {
        $this->orders = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return array
     */
    public function getOrder()
    {
        return $this->orders;
    }

    /**
     * Add order
     *
     * @param string $column
     * @param string $order
     * @return AbstractGridController
     */
    public function addOrder($column, $order = self::ORDER_ASC)
    {
        $this->orders[$column] = $order;

        return $this;
    }

    /**
     * Add orders
     *
     * @param array $orders
     * @return AbstractGridController
     */
    public function addOrders(array $orders)
    {
        foreach ($orders as $key => $value) {
            $this->orders[$key] = $value;
        }

        return $this;
    }
}
