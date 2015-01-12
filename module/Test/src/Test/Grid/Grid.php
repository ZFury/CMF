<?php

namespace Test\Grid;

use Starter\Grid\AbstractGrid;
use Doctrine\ORM\QueryBuilder;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        /** @var QueryBuilder $source */
        $source = $em->createQueryBuilder()->select(['test.id', 'test.email', 'test.name', 'phone.number'])
            ->from('\Test\Entity\Test', 'test')
            ->leftJoin(
                '\Test\Entity\PhoneForTest',
                'phone',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'test.id = phone.testId'
            );
        $this->setSource($source)
            ->setColumns(['test.id' => 'id', 'test.email' => 'Email', 'test.name' => 'Name', 'phone.number' => 'Phone'])
            ->setAllowedFilters(['test.email', 'test.name', 'phone.number'])
            ->setAllowedOrders(['test.id', 'test.name', 'phone.number'])
            ->setSphinxIndex('usersIndex');
    }
}
