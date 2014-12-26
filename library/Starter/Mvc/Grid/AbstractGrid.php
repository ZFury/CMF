<?php

namespace Starter\Mvc\Grid;

use Doctrine\ORM\QueryBuilder;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractGrid
{
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

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
    protected $params = [];

    /**
     * Page
     * @var int
     */
    protected $page = 1;

    /**
     * Limit per page
     * @var int
     */
    protected $limit = 10;

    /**
     * Default limit per page
     * @var int
     */
    protected $defaultLimit = 10;

    /**
     * Order
     * [
     *  'field' => 'order'
     * ]
     * @var array
     */
    protected $order = [];

    protected $allowedOrders = [];

    /**
     * Filter
     * @var array
     */
    protected $filter = [];

    protected $allowedFilters = [];

    /**
     * Alias of the entity in Doctrine Query
     * @var string
     */
    protected $entityAlias = '';

    /**
     * Current grid request url
     *
     * @var string
     */
    private $url;

    protected $columns = array();

    protected $total = 0;

    /**
     * Service locator
     * @var ServiceLocatorInterface
     */
    public $sm;

    /**
     * __construct
     *
     * @param ServiceLocatorInterface $serviceManager
     * @return AbstractGrid
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->sm = $serviceManager;
        $this->init();
        $this->processRequest();
        $this->process();
    }

    /**
     * Abstract function init
     */
    abstract protected function init();

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    protected function processRequest()
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->sm->get('Request');
        $url = explode('?', $request->getRequestUri());
        $this->url = $url[0];
        $params = $request->getQuery();
        if ($params->get('page')) {
            //set page
            $this->setPage((int)$params->get('page'));
        }

        if ($params->get('limit')) {
            //set limit
            $this->setLimit((int)$params->get('limit'));
        }

        foreach ($this->allowedOrders as $column) {
            $order = $params->get('order-' . $column);
            if ($order) {
                $this->setOrder([
                    $column => $order
                ]);
            }
        }

        foreach ($this->allowedFilters as $column) {
            $filter = $params->get('filter-' . $column);
            if ($filter) {
                $this->setFilter([$column => $filter]);
            }
        }
    }

    protected function process()
    {
        $source = $this->getSource();
        $limit = $this->getLimit();
        $source->setMaxResults($limit);
        $offset = $limit * ($this->getPage() - 1);
        $source->setFirstResult($offset);
        if ($order = $this->getOrder()) {
            $source->orderBy($this->getDoctrineField(key($order)), current($order));
        }
        if ($filter = $this->getFilter()) {
            $source->where(
                $source->expr()->orX()->add(
                    $source->expr()->like(
                        $this->getDoctrineField(key($filter)),
                        $source->expr()->literal('%' . current($filter) . '%')
                    )
                )
            );
        }
        $data = $source->getQuery();

        $this->data = $data->getArrayResult();
        $this->total = $this->getTotalRows();
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

        return $this;
    }

    public function getEntityAlias()
    {
        return $this->entityAlias;
    }

    public function setEntityAlias($alias)
    {
        $this->entityAlias = $alias;

        return $this;
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        $settings = [];
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
    public function setParams(array $params)
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
     * Set default limit
     *
     * @param $limit
     * @return $this
     */
    public function setDefaultLimit($limit)
    {
        $this->defaultLimit = $limit;

        return $this;
    }

    /**
     * Get default limit
     *
     * @return int
     */
    public function getDefaultLimit()
    {
        return $this->defaultLimit;
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
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set allowed orders for grid
     *
     * @param array $orders
     * @return $this
     */
    public function setAllowedOrders(array $orders)
    {
        $this->allowedOrders = $orders;

        return $this;
    }

    /**
     * Get allowed orders
     *
     * @return array
     */
    public function getAllowedOrders()
    {
        return $this->allowedOrders;
    }

    /**
     * Set filter
     *
     * @param array $filter
     * @return AbstractGrid
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter
     *
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set fields allowed for filtering by
     *
     * @param array $filters
     * @return $this
     */
    public function setAllowedFilters(array $filters)
    {
        $this->allowedFilters = $filters;

        return $this;
    }

    /**
     * Get fields allowed for filtering by
     *
     * @return array
     */
    public function getAllowedFilters()
    {
        return $this->allowedFilters;
    }

    protected function getTotalRows()
    {
        $source = $this->getSource();
        /** @var \Doctrine\ORM\Query\Expr\Select $select */
        $select = current($source->getDQLPart('select'));
        $from = current($select->getParts());
        $source->resetDQLPart('select')->setFirstResult(0)->select('count(' . $from . ')');
        return (int) current($source->getQuery()->getSingleResult());
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function totalPages()
    {
        return ceil($this->total / $this->limit);
    }

    public function getParams(array $rewrite = [])
    {
        $params = $this->params;
        //set page
        if (isset($rewrite['page'])) {
            if ($rewrite['page'] > 1) {
                $params['page'] = $rewrite['page'];
            } else {
                unset($params['page']);
            }
        } else {
            if ($this->page > 1) {
                $params['page'] = $this->page;
            }
        }

        //set limit
        if (isset($rewrite['limit'])) {
            unset($params['page']);
            if ($rewrite['limit'] != $this->defaultLimit) {
                $params['limit'] = $rewrite['limit'];
            }
        } else {
            if ($this->limit != $this->defaultLimit) {
                $params['limit'] = $this->limit;
            }
        }

        //set order
        $orders = array_intersect_key($rewrite, array_flip(preg_grep('/order-.+/i', array_keys($rewrite))));

        if (!empty($orders)) {
            foreach ($orders as $field => $order) {
                if (in_array(str_replace('order-', '', $field), $this->allowedOrders)) {
                    $params[$field] = $order;
                    break;
                }
            }
        } else {
            if (!empty($this->order)) {
                $params['order-' . key($this->order)] = current($this->order);
            }
        }

        //set filter
        $filters = array_intersect_key($rewrite, array_flip(preg_grep('/filter-.+/i', array_keys($rewrite))));

        if (!empty($filters)) {
            foreach ($filters as $field => $filter) {
                if (in_array(str_replace('filter-', '', $field), $this->allowedFilters)) {
                    $params[$field] = $filter;
                    break;
                }
            }
        } else {
            if (!empty($this->filter)) {
                $params['filter-' . key($this->filter)] = current($this->filter);
            }
        }

        return $params;
    }

    public function prev()
    {
        if ($this->page == 1) {
            return null;
        }

        return $this->page - 1;
    }

    public function next()
    {
        if ($this->page == $this->totalPages()) {
            return null;
        }

        return $this->page + 1;
    }

    protected function getDoctrineField($field)
    {
        return $this->entityAlias . '.' . $field;
    }

    public function getUrl(array $params = [])
    {
        $params = $this->getParams($params);
        $constructedUrl = $this->url;
        if (count($params)) {
            $constructedUrl .= '?';
            foreach ($params as $key => $val) {
                $constructedUrl .= $key . '=' . $val . '&';
            }
            $constructedUrl = substr($constructedUrl, 0, -1);
        }
        return $constructedUrl;
    }

    public function order($column)
    {
        if (!in_array($column, $this->allowedOrders)) {
            return null;
        }

        if (isset($this->order[$column])) {
            $order = strtolower($this->order[$column]) == self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC;
        } else {
            $order = self::ORDER_ASC;
        }

        return $this->getUrl(['order-' . $column => $order]);
    }

    public function setColumns(array $columns = [])
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns()
    {
        if (empty($this->columns) && !empty($this->data)) {
            //parse columns from data
            $tmp = array_keys(current($this->data));
            return array_combine($tmp, $tmp);
        }

        return $this->columns;
    }
}
