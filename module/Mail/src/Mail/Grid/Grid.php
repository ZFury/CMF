<?php

namespace Mail\Grid;

use Starter\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()
            ->select(['mail.id', 'mail.alias', 'mail.description', 'mail.created', 'mail.updated'])
            ->from('\Mail\Entity\Mail', 'mail');
        $this->setSource($source)->setColumns(
            [
                'Id' => 'id',
                'Alias' => 'alias',
                'Description' => 'description',
                'Created' => 'created',
                'Updated' => 'updated'
            ]
        )->setAllowedFilters(['alias', 'description'])
            ->setAllowedOrders(['id', 'alias', 'description', 'created', 'updated']);
    }
}
