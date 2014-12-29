<?php

namespace Test\Grid;

use Starter\Mvc\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()->select(['test.id', 'test.email', 'test.name'])
            ->from('\Test\Entity\Test', 'test');
        $this->setSource($source)->setColumns(['id' => 'id', 'Email' => 'email', 'Name' => 'name'])
            ->setAllowedFilters(['email', 'name'])->setAllowedOrders(['id', 'name']);
    }
}
