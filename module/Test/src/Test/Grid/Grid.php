<?php

namespace Test\Grid;

use Starter\Mvc\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $params = array();
        $page = 1;
        $limit = 3;
        $field = 'test.id';
        $order = $this::ORDER_ASC;
        $searchField = 'test.email';
        $searchString = '';
        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        /* @var \Zend\Http\Request $request */
        $request = $this->sm->get('Request');
        $source = $em->createQueryBuilder()->select(array("test.id, test.email"))
            ->from('\Test\Entity\Test', 'test');
        $this->setSource($source);
        if ($request->getPost('data')) {
            $params = $request->getPost('data');
        }
        if (isset($params['page'])) {
            $page = $params['page'];
        }
        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }
        if (isset($params['field']) && isset($params['reverse'])) {
            $field = 'test.' . $params['field'];
            $order = $params['reverse'];
        }
        if (isset($params['searchString']) && isset($params['searchField'])) {
            $searchField = 'test.' . $params['searchField'];
            $searchString = $params['searchString'];
        }
        $this->setPage($page);
        $this->setLimit($limit);
        $this->setOrder(['field' => $field, 'order' => $order]);
        $this->setFilter(['filterField' => $searchField, 'searchString' => $searchString]);
    }
}
