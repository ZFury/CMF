<?php

namespace Starter\Mvc\Grid;

use Doctrine\ORM\QueryBuilder;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractGrid
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
     * Page
     * @var int
     */
    protected $page;

    /**
     * Limit per page
     * @var int
     */
    protected $limit;

    /**
     * Orders
     * @var array
     */
    protected $orders = array();

    /**
     * Filters
     * @var array
     */
    protected $filters = array();

    /**
     * Service locator
     * @var ServiceLocatorInterface
     */
    public $sm;

    /**
     * __construct
     *
     * @param ServiceLocatorInterface $serviseManager
     * @return AbstractGrid
     */
    public function __construct(ServiceLocatorInterface $serviseManager)
    {
        $this->sm = $serviseManager;
    }

    /**
     * Abstract function init
     */
    abstract public function init();

    /**
     *  Get data
     *
     * @return array
     */
    public function getData()
    {
        $source = $this->getSource();
        $limit = $this->getLimit();
        $offset = $limit * ($this->getPage());
        $order = $this->getOrder();
        $filter = $this->getFilter();
        $data = $source
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy($order['field'], $order['order'])
            ->where(
                $source->expr()->orX()
                ->add($source->expr()->like($filter['filterField'], $source->expr()->literal('%' . $filter['searchString'] . '%')))
            )
            ->getQuery();

        return $data->getArrayResult();
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
     * Set source
     *
     * @param \Doctrine\ORM\QueryBuilder $source
     * @return AbstractGrid
     */
    public function setSource($source)
    {
        $this->source = $source;
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
     * @return AbstractGrid
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
     * @return AbstractGrid
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
     * @return AbstractGrid
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
     * @return AbstractGrid
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
     * Set filter
     *
     * @param array $filters
     * @return AbstractGrid
     */
    public function setFilter($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filter
     *
     * @return array
     */
    public function getFilter()
    {
        return $this->filters;
    }
}
