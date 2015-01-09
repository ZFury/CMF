<?php

namespace User\Grid;

use Starter\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()
            ->select(['user.id', 'user.email', 'user.displayName', 'user.role', 'user.status', 'user.created'])
            ->from('\User\Entity\User', 'user');
        $this->setSource($source)->setColumns(
            [
                'user.id' => 'Id',
                'user.email' => 'Email',
                'user.displayName' => 'Name',
                'user.role' => 'Role',
                'user.status' => 'Status',
                'user.created' => 'Created'
            ]
        )->setAllowedFilters(['user.email', 'user.displayName'])
            ->setAllowedOrders(['user.id', 'user.email', 'user.displayName', 'user.role', 'user.status', 'user.created']);
    }
}
