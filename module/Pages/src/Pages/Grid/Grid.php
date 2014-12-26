<?php

namespace Pages\Grid;

use Starter\Mvc\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()
            ->select(['pages.id', 'pages.title', 'pages.description', 'pages.created', 'pages.updated'])
            ->from('\Pages\Entity\Pages', 'pages');
        $this->setSource($source)->setColumns(
            [
                'Id' => 'id',
                'Title' => 'title',
                'Description' => 'description',
                'Created' => 'created',
                'Updated' => 'updated'
            ]
        )->setAllowedFilters(['title', 'description'])
            ->setAllowedOrders(['id', 'title', 'description', 'created', 'updated']);
    }
}
