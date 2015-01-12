<?php

namespace Pages\Grid;

use Starter\Grid\AbstractGrid;

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
                'pages.id' => 'Id',
                'pages.title' => 'Title',
                'pages.description' => 'Description',
                'pages.created' => 'Created',
                'pages.updated' => 'Updated',
            ]
        )->setAllowedFilters(['pages.title', 'pages.description'])
            ->setAllowedOrders(['pages.id', 'pages.title', 'pages.description', 'pages.created', 'pages.updated']);
    }
}
