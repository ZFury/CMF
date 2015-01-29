<?php

namespace Fury\Grid;

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

    /**
     * Fields that are allowed for ordering by
     *
     * @var array
     */
    protected $allowedOrders = [];

    /**
     * Filter
     * [
     *  'field' => 'filter'
     * ]
     * @var array
     */
    protected $filter = [];

    /**
     * Fields that are allowed for filtering by
     *
     * @var array
     */
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

    /**
     * Columns with aliases
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Total number of rows
     *
     * @var int
     */
    protected $total = 0;

    /**
     * Aliases of the query
     * @var array
     */
    protected $aliases = [];

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

    /**
     * Parses request and sets grid params
     */
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
            $columnMod = str_replace('.', '_', $column);
            $order = $params->get('order-' . $columnMod);

            if ($order) {
                $this->setOrder([
                    $column => $order
                ]);
            }
        }

        foreach ($this->allowedFilters as $column) {
            $columnMod = str_replace('.', '_', $column);
            $filter = $params->get('filter-' . $columnMod);
            if ($filter) {
                $this->setFilter([$column => $filter]);
            }
        }
    }

    /**
     * Process
     */
    protected function process()
    {
        $source = $this->getSource();
        /** @var \Doctrine\ORM\Query\Expr\From $from */
        $from = current($source->getDQLPart('from'));
        $this->entityAlias = $from->getAlias();
        $limit = $this->getLimit();
        $source->setMaxResults($limit);
        $offset = $limit * ($this->getPage() - 1);
        $source->setFirstResult($offset);

        if ($order = $this->getOrder()) {
            $source->orderBy(key($order), current($order));
        }
        if ($filter = $this->getFilter()) {
            $source->where(
                $source->expr()->orX()
                    ->add(
                        $source->expr()
                            ->like(
                                key($filter),
                                $source->expr()->literal('%' . current($filter) . '%')
                            )
                    )
            );
        }
        $data = $source->getQuery();

        $this->data = $data->getArrayResult();

        if (empty($this->data) && $this->page > 1) {
            $this->page--;
            $this->process();
        }

        $this->total = $this->countTotalRows();
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

    /**
     * Get entity alias
     *
     * @return string
     */
    public function getEntityAlias()
    {
        return $this->entityAlias;
    }

    /**
     * Set entity alias
     *
     * @param $alias
     * @return $this
     */
    public function setEntityAlias($alias)
    {
        $this->entityAlias = $alias;

        return $this;
    }

    /**
     * Get aliases
     *
     * @return string
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set aliases
     *
     * @param array $aliases
     * @return $this
     */
    public function setAliases($aliases)
    {
        $this->aliases = $aliases;

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

    /**
     * Get total rows in result ignoring limit
     *
     * @return int
     */
    protected function countTotalRows()
    {
        $source = $this->getSource();
        /** @var \Doctrine\ORM\Query\Expr\Select $select */
        $source->resetDQLPart('select')->setFirstResult(0)->select('count(' . $this->entityAlias . ')');
        return (int)$source->getQuery()->getSingleScalarResult();
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get number of pages
     *
     * @return int
     */
    public function totalPages()
    {
        return ceil($this->total / $this->limit);
    }

    /**
     * Get all grid params
     * Passing $rewrite allows you to change them
     *
     * @param array $rewrite
     * @return array
     */
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

    /**
     * Number of the previous page
     *
     * @return int|null
     */
    public function prev()
    {
        if ($this->page == 1) {
            return null;
        }

        return $this->page - 1;
    }

    /**
     * Number of the next page
     *
     * @return int|null
     */
    public function next()
    {
        if ($this->page == $this->totalPages()) {
            return null;
        }

        return $this->page + 1;
    }

    /**
     * Get url for grid according to passed $params
     *
     * @param array $params
     * @return string
     */
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

    /**
     * Get url for order by column name
     *
     * @param $column
     * @return string
     */
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

    /**
     * Set aliases for columns
     *
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns = [])
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns
     *
     * @return array
     */
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
