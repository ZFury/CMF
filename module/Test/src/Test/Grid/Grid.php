<?php

namespace Test\Grid;

use Starter\Mvc\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
//        $order = self::ORDER_ASC;
        $searchField = 'email';
        $searchString = '';
//        $field = 'id';
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()->select(array("test.id, test.email, test.name"))
            ->from('\Test\Entity\Test', 'test');
        $this->setSource($source)->setEntityAlias('test')
            ->setFilter(['filterField' => $searchField, 'searchString' => $searchString])
            ->setAllowedFilters(['email', 'name'])->setAllowedOrders(['id', 'name']);
    }
}