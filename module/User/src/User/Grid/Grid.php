<?php

namespace User\Grid;

use Starter\Mvc\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init() {
        $params = array();
        $page = 1;
        $limit = 3;
        $field = 'user.id';
        $order = $this::ORDER_ASC;
        $searchField = 'user.email';
        $searchString = '';
        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        /* @var \Zend\Http\Request $request */
        $request = $this->sm->get('Request');
        $source = $em->createQueryBuilder()->select(array("user.id, user.email"))
            ->from('\User\Entity\User', 'user');
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
            $field = 'user.' . $params['field'];
            $order = $params['reverse'];
        }
        if (isset($params['searchString']) && isset($params['searchField'])) {
            $searchField = 'user.' . $params['searchField'];
            $searchString = $params['searchString'];
        }
        $this->setPage($page);
        $this->setLimit($limit);
        $this->setOrder(['field' => $field, 'order' => $order]);
        $this->setFilter(['filterField' => $searchField, 'searchString' => $searchString]);
    }
}