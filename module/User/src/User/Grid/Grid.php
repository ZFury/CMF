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
                'Id' => 'id',
                'Email' => 'email',
                'Name' => 'displayName',
                'Role' => 'role',
                'Status' => 'status',
                'Created' => 'created'
            ]
        )->setAllowedFilters(['email', 'displayName'])
            ->setAllowedOrders(['id', 'email', 'displayName', 'role', 'status', 'created']);
    }
}
